<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { onBeforeUnmount, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

interface Testimonial {
    id: number;
    name: string;
    role: string | null;
    content: string;
    short_content: string;
    rating: number;
    avatar: string;
    avatar_color: string;
    is_published: boolean;
    sort_order: number;
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
    status: 'published' | 'draft' | null;
    rating: number | null;
    per_page: number;
}

const props = defineProps<{
    testimonials: Testimonial[];
    pagination: Pagination;
    filters: Filters;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Testimonials',
        href: '/admin/testimonials',
    },
];

const confirmDeleteOpen = ref(false);
const testimonialToDelete = ref<Testimonial | null>(null);

const filterForm = useForm({
    search: props.filters.search ?? '',
    status: props.filters.status,
    rating: props.filters.rating,
    per_page: props.filters.per_page ?? 10,
});

const openDeleteDialog = (testimonial: Testimonial) => {
    testimonialToDelete.value = testimonial;
    confirmDeleteOpen.value = true;
};

const confirmDelete = () => {
    if (!testimonialToDelete.value) {
        return;
    }

    router.delete(route('testimonials.destroy', { testimonial: testimonialToDelete.value.id }), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Testimoni berhasil dihapus.');
            confirmDeleteOpen.value = false;
            testimonialToDelete.value = null;
        },
    });
};

const togglePublish = (testimonial: Testimonial) => {
    const routeName = testimonial.is_published ? 'testimonials.unpublish' : 'testimonials.publish';

    router.post(
        route(routeName, { testimonial: testimonial.id }),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success(testimonial.is_published ? 'Testimoni disembunyikan.' : 'Testimoni dipublikasikan.');
            },
        },
    );
};

const applyFilters = () => {
    router.get(
        route('testimonials.index'),
        {
            search: filterForm.search || undefined,
            status: filterForm.status || undefined,
            rating: filterForm.rating || undefined,
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
    () => [filterForm.status, filterForm.rating, filterForm.per_page],
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
    filterForm.status = null;
    filterForm.rating = null;
    filterForm.per_page = 10;
    applyFilters();
};
</script>

<template>
    <Head title="Testimonials" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold">Manajemen Testimoni</h1>
                    <p class="text-sm text-muted-foreground">Kelola testimoni untuk landing page interaktif.</p>
                </div>

                <Button as-child>
                    <Link :href="route('testimonials.create')">Tambah Testimoni</Link>
                </Button>
            </div>

            <div class="max-w-full overflow-hidden rounded-lg border">
                <div class="grid gap-3 border-b bg-muted/30 p-4 md:grid-cols-5">
                    <div class="md:col-span-3">
                        <Label for="search">Search</Label>
                        <Input id="search" v-model="filterForm.search" placeholder="Cari nama, role, atau isi testimoni" />
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

                    <div>
                        <Label for="rating-filter">Rating</Label>
                        <select
                            id="rating-filter"
                            v-model="filterForm.rating"
                            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        >
                            <option :value="null">Semua rating</option>
                            <option :value="5">5 Bintang</option>
                            <option :value="4">4 Bintang</option>
                            <option :value="3">3 Bintang</option>
                            <option :value="2">2 Bintang</option>
                            <option :value="1">1 Bintang</option>
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
                    <table class="w-full min-w-[900px] text-sm">
                        <thead class="bg-muted/60 text-left">
                            <tr>
                                <th class="px-4 py-3 font-medium">Pemberi Testimoni</th>
                                <th class="px-4 py-3 font-medium">Isi</th>
                                <th class="px-4 py-3 font-medium">Rating</th>
                                <th class="px-4 py-3 font-medium">Status</th>
                                <th class="px-4 py-3 font-medium">Urutan</th>
                                <th class="px-4 py-3 font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="testimonials.length === 0">
                                <td colspan="6" class="px-4 py-6 text-center text-muted-foreground">Belum ada testimoni.</td>
                            </tr>
                            <tr v-for="testimonial in testimonials" :key="testimonial.id" class="border-t">
                                <td class="px-4 py-3">
                                    <p class="font-medium">{{ testimonial.name }}</p>
                                    <p class="text-xs text-muted-foreground">{{ testimonial.role || '-' }}</p>
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">{{ testimonial.short_content }}</td>
                                <td class="px-4 py-3">{{ testimonial.rating }} / 5</td>
                                <td class="px-4 py-3">
                                    <span
                                        :class="[
                                            'rounded-full px-2 py-1 text-xs font-medium',
                                            testimonial.is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700',
                                        ]"
                                    >
                                        {{ testimonial.is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ testimonial.sort_order }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <Button size="sm" variant="outline" as-child>
                                            <Link :href="route('testimonials.edit', { testimonial: testimonial.id })">Edit</Link>
                                        </Button>
                                        <Button size="sm" variant="outline" @click="togglePublish(testimonial)">
                                            {{ testimonial.is_published ? 'Unpublish' : 'Publish' }}
                                        </Button>
                                        <Button size="sm" variant="destructive" @click="openDeleteDialog(testimonial)">Hapus</Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-col gap-3 border-t p-4 text-sm md:flex-row md:items-center md:justify-between">
                    <p class="text-muted-foreground">
                        Menampilkan {{ pagination.from ?? 0 }} - {{ pagination.to ?? 0 }} dari {{ pagination.total }} testimoni
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

        <Dialog v-model:open="confirmDeleteOpen">
            <DialogContent class="max-w-md">
                <DialogHeader>
                    <DialogTitle>Hapus Testimoni</DialogTitle>
                    <DialogDescription>
                        Testimoni dari <strong>{{ testimonialToDelete?.name }}</strong> akan dihapus permanen.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="confirmDeleteOpen = false">Batal</Button>
                    <Button variant="destructive" @click="confirmDelete">Ya, Hapus</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
