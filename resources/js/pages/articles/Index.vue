<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { onBeforeUnmount, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

interface Category {
    id: number;
    name: string;
    slug: string;
    description: string | null;
}

interface Article {
    id: number;
    title: string;
    slug: string;
    featured_image_url: string | null;
    is_published: boolean;
    category: { id: number; name: string } | null;
    author: { id: number; name: string } | null;
    created_at: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Pagination {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: PaginationLink[];
}

interface Filters {
    search: string;
    category_id: number | null;
    status: 'published' | 'draft' | null;
    per_page: number;
}

const props = defineProps<{
    articles: Article[];
    pagination: Pagination;
    categories: Category[];
    filters: Filters;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Articles',
        href: '/dashboard/articles',
    },
];

const categoryModalOpen = ref(false);
const editingCategoryId = ref<number | null>(null);
const confirmArticleDeleteOpen = ref(false);
const confirmCategoryDeleteOpen = ref(false);
const articleToDelete = ref<Article | null>(null);
const categoryToDelete = ref<Category | null>(null);

const filterForm = useForm({
    search: props.filters.search ?? '',
    category_id: props.filters.category_id,
    status: props.filters.status,
    per_page: props.filters.per_page ?? 10,
});

const createCategoryForm = useForm({
    name: '',
    description: '',
});

const editCategoryForm = useForm({
    name: '',
    description: '',
});

const submitCreateCategory = () => {
    createCategoryForm.post(route('article-categories.store'), {
        preserveScroll: true,
        onSuccess: () => createCategoryForm.reset(),
    });
};

const startEditCategory = (category: Category) => {
    editingCategoryId.value = category.id;
    editCategoryForm.name = category.name;
    editCategoryForm.description = category.description ?? '';
};

const cancelEditCategory = () => {
    editingCategoryId.value = null;
    editCategoryForm.reset();
    editCategoryForm.clearErrors();
};

const submitEditCategory = () => {
    if (!editingCategoryId.value) {
        return;
    }

    editCategoryForm.put(route('article-categories.update', { articleCategory: editingCategoryId.value }), {
        preserveScroll: true,
        onSuccess: () => {
            cancelEditCategory();
            toast.success('Kategori artikel berhasil diperbarui.');
        },
    });
};

const openDeleteCategoryDialog = (category: Category) => {
    categoryToDelete.value = category;
    confirmCategoryDeleteOpen.value = true;
};

const confirmDeleteCategory = () => {
    if (!categoryToDelete.value) {
        return;
    }

    router.delete(route('article-categories.destroy', { articleCategory: categoryToDelete.value.id }), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Kategori artikel berhasil dihapus.');
            confirmCategoryDeleteOpen.value = false;
            categoryToDelete.value = null;
        },
        onError: (errors) => {
            toast.error(errors.category ?? 'Kategori artikel tidak bisa dihapus karena masih memiliki data child.');
            confirmCategoryDeleteOpen.value = false;
        },
    });
};

const openDeleteArticleDialog = (article: Article) => {
    articleToDelete.value = article;
    confirmArticleDeleteOpen.value = true;
};

const confirmDeleteArticle = () => {
    if (!articleToDelete.value) {
        return;
    }

    router.delete(route('articles.destroy', { article: articleToDelete.value.id }), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Artikel berhasil dihapus.');
            confirmArticleDeleteOpen.value = false;
            articleToDelete.value = null;
        },
    });
};

const applyFilters = () => {
    router.get(
        route('articles.index'),
        {
            search: filterForm.search || undefined,
            category_id: filterForm.category_id || undefined,
            status: filterForm.status || undefined,
            per_page: filterForm.per_page,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
};

let searchDebounceTimer: ReturnType<typeof setTimeout> | null = null;

watch(
    () => filterForm.search,
    () => {
        if (searchDebounceTimer) {
            clearTimeout(searchDebounceTimer);
        }

        searchDebounceTimer = setTimeout(() => {
            applyFilters();
        }, 400);
    },
);

watch(
    () => [filterForm.category_id, filterForm.status, filterForm.per_page],
    () => {
        applyFilters();
    },
);

onBeforeUnmount(() => {
    if (searchDebounceTimer) {
        clearTimeout(searchDebounceTimer);
    }
});

const resetFilters = () => {
    filterForm.search = '';
    filterForm.category_id = null;
    filterForm.status = null;
    filterForm.per_page = 10;
    applyFilters();
};
</script>

<template>
    <Head title="Articles" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold">Manajemen Artikel</h1>
                    <p class="text-sm text-muted-foreground">Kelola artikel dan kategori artikel dalam satu halaman.</p>
                </div>

                <div class="flex gap-2">
                    <Button variant="outline" @click="categoryModalOpen = true">Manage Kategori</Button>
                    <Button as-child>
                        <Link :href="route('articles.create')">Tambah Artikel</Link>
                    </Button>
                </div>
            </div>

            <div class="rounded-lg border">
                <div class="grid gap-3 bg-muted/30 p-4 md:grid-cols-5">
                    <div class="md:col-span-3">
                        <Label for="search">Search</Label>
                        <Input id="search" v-model="filterForm.search" placeholder="Cari judul, slug, atau konten" />
                    </div>

                    <div>
                        <Label for="category-filter">Kategori</Label>
                        <select
                            id="category-filter"
                            v-model="filterForm.category_id"
                            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        >
                            <option :value="null">Semua kategori</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <Label for="status-filter">Status</Label>
                        <select
                            id="status-filter"
                            v-model="filterForm.status"
                            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        >
                            <option :value="null">Semua status</option>
                            <option value="published">Published</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-4 px-4 py-4">
                    <Button size="sm" @click="resetFilters">Reset</Button>

                    <div class="flex items-center gap-2">
                        <Label for="per-page" class="whitespace-nowrap">Per Page</Label>
                        <select
                            id="per-page"
                            v-model="filterForm.per_page"
                            class="w-20 rounded-md border border-input bg-background px-3 py-2 text-sm"
                        >
                            <option :value="10">10</option>
                            <option :value="15">15</option>
                            <option :value="25">25</option>
                            <option :value="50">50</option>
                        </select>
                    </div>
                </div>

                <div class="w-full overflow-x-auto">
                    <table class="w-full min-w-[720px] text-sm">
                        <thead class="bg-muted/60 text-left">
                            <tr>
                                <th class="px-4 py-3 font-medium">Judul</th>
                                <th class="px-4 py-3 font-medium">Kategori</th>
                                <th class="px-4 py-3 font-medium">Author</th>
                                <th class="px-4 py-3 font-medium">Status</th>
                                <th class="px-4 py-3 font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="articles.length === 0">
                                <td colspan="5" class="px-4 py-6 text-center text-muted-foreground">Belum ada artikel.</td>
                            </tr>
                            <tr v-for="article in articles" :key="article.id" class="border-t">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <img
                                            v-if="article.featured_image_url"
                                            :src="article.featured_image_url"
                                            alt="thumbnail"
                                            class="h-10 w-14 rounded object-cover"
                                        />
                                        <p class="font-medium">{{ article.title }}</p>
                                    </div>
                                    <p class="text-xs text-muted-foreground">/{{ article.slug }}</p>
                                </td>
                                <td class="px-4 py-3">{{ article.category?.name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ article.author?.name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium"
                                        :class="article.is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'"
                                    >
                                        {{ article.is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <Button size="sm" variant="outline" as-child>
                                            <Link :href="route('articles.show', { article: article.id })">Detail</Link>
                                        </Button>
                                        <Button size="sm" variant="outline" as-child>
                                            <Link :href="route('articles.edit', { article: article.id })">Edit</Link>
                                        </Button>
                                        <Button size="sm" variant="destructive" @click="openDeleteArticleDialog(article)">Hapus</Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-wrap gap-3 border-t p-4 text-sm md:flex-row md:items-center justify-between">
                    <p class="text-muted-foreground">
                        Menampilkan {{ pagination.from ?? 0 }} - {{ pagination.to ?? 0 }} dari {{ pagination.total }} artikel
                    </p>

                    <div class="flex flex-wrap gap-2">
                        <Button
                            v-for="(link, index) in pagination.links"
                            :key="index"
                            :variant="link.active ? 'default' : 'outline'"
                            size="sm"
                            :disabled="!link.url"
                            @click="link.url && router.visit(link.url, { preserveScroll: true, preserveState: true })"
                        >
                            <span v-html="link.label" />
                        </Button>
                    </div>
                </div>
            </div>
        </div>

        <Dialog v-model:open="categoryModalOpen">
            <DialogContent class="max-w-3xl">
                <DialogHeader>
                    <DialogTitle>Manage Kategori Artikel</DialogTitle>
                    <DialogDescription>Tambah, ubah, dan hapus kategori tanpa meninggalkan halaman artikel.</DialogDescription>
                </DialogHeader>

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="space-y-4 rounded-lg border p-4">
                        <h3 class="font-medium">Tambah Kategori</h3>
                        <form class="space-y-3" @submit.prevent="submitCreateCategory">
                            <div class="space-y-1.5">
                                <Label for="new-category-name">Nama</Label>
                                <Input id="new-category-name" v-model="createCategoryForm.name" placeholder="Tips Budidaya" />
                                <InputError :message="createCategoryForm.errors.name" />
                            </div>

                            <div class="space-y-1.5">
                                <Label for="new-category-description">Deskripsi</Label>
                                <textarea
                                    id="new-category-description"
                                    v-model="createCategoryForm.description"
                                    rows="3"
                                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                    placeholder="Deskripsi singkat kategori"
                                />
                                <InputError :message="createCategoryForm.errors.description" />
                            </div>

                            <Button :disabled="createCategoryForm.processing">Simpan Kategori</Button>
                        </form>
                    </div>

                    <div class="space-y-3 rounded-lg border p-4">
                        <h3 class="font-medium">Daftar Kategori</h3>
                        <div v-if="categories.length === 0" class="text-sm text-muted-foreground">Belum ada kategori.</div>

                        <div v-for="category in categories" :key="category.id" class="rounded-md border p-3">
                            <template v-if="editingCategoryId === category.id">
                                <form class="space-y-2" @submit.prevent="submitEditCategory">
                                    <Input v-model="editCategoryForm.name" placeholder="Nama kategori" />
                                    <InputError :message="editCategoryForm.errors.name" />

                                    <textarea
                                        v-model="editCategoryForm.description"
                                        rows="2"
                                        class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                        placeholder="Deskripsi"
                                    />
                                    <InputError :message="editCategoryForm.errors.description" />

                                    <div class="flex gap-2">
                                        <Button size="sm" :disabled="editCategoryForm.processing">Update</Button>
                                        <Button size="sm" variant="outline" type="button" @click="cancelEditCategory">Batal</Button>
                                    </div>
                                </form>
                            </template>

                            <template v-else>
                                <p class="font-medium">{{ category.name }}</p>
                                <p class="text-xs text-muted-foreground">/{{ category.slug }}</p>
                                <p class="mt-2 text-xs text-muted-foreground">{{ category.description || '-' }}</p>

                                <div class="mt-3 flex gap-2">
                                    <Button size="sm" variant="outline" @click="startEditCategory(category)">Edit</Button>
                                    <Button size="sm" variant="destructive" @click="openDeleteCategoryDialog(category)">Hapus</Button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="outline" @click="categoryModalOpen = false">Tutup</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <Dialog v-model:open="confirmArticleDeleteOpen">
            <DialogContent class="max-w-md">
                <DialogHeader>
                    <DialogTitle>Hapus Artikel</DialogTitle>
                    <DialogDescription>
                        Artikel <strong>{{ articleToDelete?.title }}</strong> akan dihapus permanen.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="confirmArticleDeleteOpen = false">Batal</Button>
                    <Button variant="destructive" @click="confirmDeleteArticle">Ya, Hapus</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <Dialog v-model:open="confirmCategoryDeleteOpen">
            <DialogContent class="max-w-md">
                <DialogHeader>
                    <DialogTitle>Hapus Kategori</DialogTitle>
                    <DialogDescription>
                        Kategori <strong>{{ categoryToDelete?.name }}</strong> akan dihapus. Artikel terkait juga bisa terdampak.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="confirmCategoryDeleteOpen = false">Batal</Button>
                    <Button variant="destructive" @click="confirmDeleteCategory">Ya, Hapus</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
