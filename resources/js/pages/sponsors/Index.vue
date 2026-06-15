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

interface Sponsor {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    logo_url: string | null;
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
    per_page: number;
}

const props = defineProps<{
    sponsors: Sponsor[];
    pagination: Pagination;
    filters: Filters;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Sponsors',
        href: '/sponsors',
    },
];

const confirmDeleteOpen = ref(false);
const sponsorToDelete = ref<Sponsor | null>(null);

const filterForm = useForm({
    search: props.filters.search ?? '',
    per_page: props.filters.per_page ?? 10,
});

const openDeleteDialog = (sponsor: Sponsor) => {
    sponsorToDelete.value = sponsor;
    confirmDeleteOpen.value = true;
};

const confirmDelete = () => {
    if (!sponsorToDelete.value) {
        return;
    }

    router.delete(route('sponsors.destroy', { sponsor: sponsorToDelete.value.id }), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Sponsor berhasil dihapus.');
            confirmDeleteOpen.value = false;
            sponsorToDelete.value = null;
        },
    });
};

const applyFilters = () => {
    router.get(
        route('sponsors.index'),
        {
            search: filterForm.search || undefined,
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
    () => filterForm.per_page,
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
    filterForm.per_page = 10;
    applyFilters();
};
</script>

<template>
    <Head title="Sponsors" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold">Manajemen Sponsor</h1>
                    <p class="text-sm text-muted-foreground">Kelola daftar sponsor platform dalam satu halaman.</p>
                </div>

                <div class="flex">
                    <Button as-child>
                        <Link :href="route('sponsors.create')">Tambah Sponsor</Link>
                    </Button>
                </div>
            </div>

            <div class="max-w-full overflow-hidden rounded-lg border">
                <div class="flex w-full items-center gap-3 bg-muted/30 p-4 bg">
                    <div class="flex flex-col gap-2 w-full sm:w-64">
                        <Label for="search">Search</Label>
                        <Input id="search" v-model="filterForm.search" placeholder="Cari nama, slug, atau deskripsi sponsor" />
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
                    <table class="w-full min-w-[760px] text-sm">
                        <thead class="bg-muted/60 text-left">
                            <tr>
                                <th class="px-4 py-3 font-medium">Sponsor</th>
                                <th class="px-4 py-3 font-medium">Deskripsi</th>
                                <th class="px-4 py-3 font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="sponsors.length === 0">
                                <td colspan="3" class="px-4 py-6 text-center text-muted-foreground">Belum ada sponsor.</td>
                            </tr>
                            <tr v-for="sponsor in sponsors" :key="sponsor.id" class="border-t">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <img v-if="sponsor.logo_url" :src="sponsor.logo_url" alt="logo" class="h-10 w-10 rounded object-cover" />
                                        <div>
                                            <p class="font-medium">{{ sponsor.name }}</p>
                                            <p class="text-xs text-muted-foreground">/{{ sponsor.slug }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">{{ sponsor.description || '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <Button size="sm" variant="outline" as-child>
                                            <Link :href="route('sponsors.show', { sponsor: sponsor.id })">Detail</Link>
                                        </Button>
                                        <Button size="sm" variant="outline" as-child>
                                            <Link :href="route('sponsors.edit', { sponsor: sponsor.id })">Edit</Link>
                                        </Button>
                                        <Button size="sm" variant="destructive" @click="openDeleteDialog(sponsor)">Hapus</Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 border-t p-4 text-sm">
                    <p class="text-muted-foreground">
                        Menampilkan {{ pagination.from ?? 0 }} - {{ pagination.to ?? 0 }} dari {{ pagination.total }} sponsor
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
                    <DialogTitle>Hapus Sponsor</DialogTitle>
                    <DialogDescription>
                        Sponsor <strong>{{ sponsorToDelete?.name }}</strong> akan dihapus permanen.
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
