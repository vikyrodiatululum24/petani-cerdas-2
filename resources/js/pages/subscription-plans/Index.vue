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

interface SubscriptionPlan {
    id: number;
    name: string;
    jenis: string;
    price: number;
    discount_price: number | null;
    effective_price: number;
    quota: number | null;
    duration_months: number;
    is_featured: boolean;
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
    featured: 'featured' | 'regular' | null;
    per_page: number;
    jenis: string | null;
}

const props = defineProps<{
    plans: SubscriptionPlan[];
    pagination: Pagination;
    filters: Filters;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Subscription Plans',
        href: '/subscription-plans',
    },
];

const confirmDeleteOpen = ref(false);
const planToDelete = ref<SubscriptionPlan | null>(null);

const filterForm = useForm({
    search: props.filters.search ?? '',
    featured: props.filters.featured,
    per_page: props.filters.per_page ?? 10,
    jenis: props.filters.jenis,
});

const openDeleteDialog = (plan: SubscriptionPlan) => {
    planToDelete.value = plan;
    confirmDeleteOpen.value = true;
};

const confirmDelete = () => {
    if (!planToDelete.value) {
        return;
    }

    router.delete(route('subscription-plans.destroy', { subscription_plan: planToDelete.value.id }), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Paket langganan berhasil dihapus.');
            confirmDeleteOpen.value = false;
            planToDelete.value = null;
        },
    });
};

const applyFilters = () => {
    router.get(
        route('subscription-plans.index'),
        {
            search: filterForm.search || undefined,
            featured: filterForm.featured || undefined,
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
    () => [filterForm.featured, filterForm.per_page, filterForm.jenis],
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
    filterForm.featured = null;
    filterForm.per_page = 10;
    filterForm.jenis = null;
    applyFilters();
};

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(price);
};
</script>

<template>
    <Head title="Manajemen Paket Langganan" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold">Manajemen Paket Langganan</h1>
                    <p class="text-sm text-muted-foreground">Kelola paket langganan untuk pengguna platform.</p>
                </div>

                <div class="flex gap-2">
                    <Button>
                        <Link :href="route('subscription-plans.create')">Tambah Paket</Link>
                    </Button>
                </div>
            </div>

            <div class="grid min-w-0 grid-cols-1 gap-3 bg-muted/30 p-4 md:grid-cols-4">
                <div class="md:col-span-2">
                    <Label for="search">Search</Label>
                    <Input id="search" v-model="filterForm.search" placeholder="Cari nama atau deskripsi paket" />
                </div>
                <div>
                    <Label for="jenis-filter">Jenis</Label>
                    <select
                        id="jenis-filter"
                        v-model="filterForm.jenis"
                        class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        >
                            <option :value="null">Semua</option>
                            <option value="basic">Basic</option>
                            <option value="premium">Premium</option>
                            <option value="elite">Elite</option>
                        </select>
                    </div>
    
                    <div>
                        <Label for="featured-filter">Tipe</Label>
                        <select
                            id="featured-filter"
                            v-model="filterForm.featured"
                            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        >
                            <option :value="null">Semua</option>
                            <option value="featured">Utama</option>
                            <option value="regular">Biasa</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between gap-4 px-4 py-4">
                <Button size="sm" @click="resetFilters">Reset</Button>

                <div class="flex items-center gap-2">
                    <Label for="per-page" class="whitespace-nowrap">Per Page</Label>
                    <select id="per-page" v-model="filterForm.per_page" class="w-20 rounded-md border border-input bg-background px-3 py-2 text-sm">
                        <option :value="10">10</option>
                        <option :value="15">15</option>
                        <option :value="25">25</option>
                        <option :value="50">50</option>
                    </select>
                </div>
            </div>

            <div class="w-full">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1200px] text-sm">
                        <thead class="bg-muted/60 text-left">
                            <tr>
                                <th class="px-4 py-3 font-medium">Nama Paket</th>
                                <th class="px-4 py-3 font-medium">Jenis</th>
                                <th class="px-4 py-3 font-medium">Harga</th>
                                <th class="px-4 py-3 font-medium">Durasi</th>
                                <th class="px-4 py-3 font-medium">Kuota</th>
                                <th class="px-4 py-3 font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="plans.length === 0">
                                <td colspan="5" class="px-4 py-6 text-center text-muted-foreground">Belum ada paket langganan.</td>
                            </tr>
                            <tr v-for="plan in plans" :key="plan.id" class="border-t">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <p class="font-medium">{{ plan.name }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700">{{ plan.jenis }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div>
                                        <p class="font-medium">{{ formatPrice(plan.effective_price) }}</p>
                                        <p v-if="plan.discount_price" class="text-xs text-muted-foreground line-through">{{ formatPrice(plan.price) }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-3">{{ plan.duration_months }} bulan</td>
                                <td class="px-4 py-3">{{ plan.quota ?? 'Unlimited' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <Button size="sm" variant="outline" as-child>
                                            <Link :href="route('subscription-plans.show', { subscription_plan: plan.id })">Detail</Link>
                                        </Button>
                                        <Button size="sm" variant="outline" as-child>
                                            <Link :href="route('subscription-plans.edit', { subscription_plan: plan.id })">Edit</Link>
                                        </Button>
                                        <Button size="sm" variant="destructive" @click="openDeleteDialog(plan)">Hapus</Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="flex justify-between gap-3 border-t p-4 text-sm">
                        <p class="text-muted-foreground">
                            Menampilkan {{ pagination.from ?? 0 }} - {{ pagination.to ?? 0 }} dari {{ pagination.total }} paket
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
                    <DialogTitle>Hapus Paket Langganan</DialogTitle>
                    <DialogDescription>
                        Paket <strong>{{ planToDelete?.name }}</strong> akan dihapus permanen.
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
