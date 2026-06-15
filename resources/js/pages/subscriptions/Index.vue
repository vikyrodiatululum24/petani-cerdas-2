<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

interface SubscriptionItem {
    id: number;
    generation: string;
    status: 'pending' | 'active' | 'inactive' | 'canceled';
    start_date: string | null;
    end_date: string | null;
    created_at: string | null;
    pending_payment_id: number | null;
    user: {
        id: number | null;
        name: string | null;
        email: string | null;
    };
    plan: {
        id: number | null;
        name: string | null;
        duration_months: number | null;
        price: number | null;
    };
    approver: {
        id: number | null;
        name: string | null;
    };
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
    status: string | null;
    plan_id: number | null;
}

interface PlanOption {
    id: number;
    name: string;
}

const props = defineProps<{
    subscriptions: SubscriptionItem[];
    pagination: Pagination;
    filters: Filters;
    plans: PlanOption[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Subscriptions',
        href: '/subscriptions',
    },
];

const filterForm = useForm({
    per_page: props.pagination.per_page,
    search: props.filters.search ?? '',
    status: props.filters.status ?? '',
    plan_id: props.filters.plan_id ? String(props.filters.plan_id) : '',
});

const selected = ref<SubscriptionItem | null>(null);
const editDialogOpen = ref(false);

const updateForm = useForm({
    status: 'pending',
    start_date: '',
    end_date: '',
    generation: '',
});

const formatCurrency = (value: number | null) => {
    if (value === null) {
        return '-';
    }

    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};

const statusClass = computed(() => (status: string) => {
    const classes: Record<string, string> = {
        pending: 'bg-amber-100 text-amber-800',
        active: 'bg-emerald-100 text-emerald-800',
        inactive: 'bg-slate-100 text-slate-700',
        canceled: 'bg-red-100 text-red-800',
    };

    return classes[status] ?? 'bg-gray-100 text-gray-800';
});

const applyFilters = () => {
    router.get(
        route('subscriptions.index'),
        {
            search: filterForm.search || undefined,
            status: filterForm.status || undefined,
            plan_id: filterForm.plan_id || undefined,
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
    () => [filterForm.status, filterForm.plan_id],
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
    filterForm.status = '';
    filterForm.plan_id = '';
    applyFilters();
};

const openEditDialog = (item: SubscriptionItem) => {
    selected.value = item;
    updateForm.status = item.status;
    updateForm.start_date = item.start_date ?? '';
    updateForm.end_date = item.end_date ?? '';
    updateForm.generation = item.generation ?? '';
    editDialogOpen.value = true;
};

const submitUpdate = () => {
    if (!selected.value) {
        return;
    }

    updateForm.put(route('subscriptions.update', { subscription: selected.value.id }), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Subscription berhasil diperbarui.');
            editDialogOpen.value = false;
            selected.value = null;
        },
        onError: () => {
            toast.error('Gagal memperbarui subscription.');
        },
    });
};
</script>

<template>
    <Head title="Manajemen Subscription" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold">Manajemen Subscription</h1>
                    <p class="text-sm text-muted-foreground">Kelola status, tanggal aktif, dan generation subscription petani.</p>
                </div>
                <Button variant="outline" as-child>
                    <Link :href="route('admin.subscriptions.payments.index')">Buka Review Pembayaran</Link>
                </Button>
            </div>

            <div class="max-w-full overflow-hidden rounded-lg border">
                <div class="grid gap-3 bg-muted/30 p-4 md:grid-cols-4">
                    <div class="md:col-span-2">
                        <Label for="search">Search</Label>
                        <Input id="search" v-model="filterForm.search" placeholder="Cari petani, email, paket, atau generation" />
                    </div>

                    <div>
                        <Label for="plan-filter">Jenis Plan</Label>
                        <select
                            id="plan-filter"
                            v-model="filterForm.plan_id"
                            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        >
                            <option value="">Semua plan</option>
                            <option v-for="plan in plans" :key="plan.id" :value="String(plan.id)">
                                {{ plan.name }}
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
                            <option value="">Semua status</option>
                            <option value="pending">pending</option>
                            <option value="active">active</option>
                            <option value="inactive">inactive</option>
                            <option value="canceled">canceled</option>
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
                    <table class="w-full min-w-[980px] text-sm">
                        <thead class="bg-muted/60 text-left">
                            <tr>
                                <th class="px-4 py-3 font-medium">Petani</th>
                                <th class="px-4 py-3 font-medium">Paket</th>
                                <th class="px-4 py-3 font-medium">Generation</th>
                                <th class="px-4 py-3 font-medium">Periode</th>
                                <th class="px-4 py-3 font-medium">Status</th>
                                <th class="px-4 py-3 font-medium">Approver</th>
                                <th class="px-4 py-3 font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="subscriptions.length === 0">
                                <td colspan="7" class="px-4 py-6 text-center text-muted-foreground">Belum ada data subscription.</td>
                            </tr>
                            <tr v-for="item in subscriptions" :key="item.id" class="border-t">
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ item.user.name ?? '-' }}</div>
                                    <div class="text-xs text-muted-foreground">{{ item.user.email ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ item.plan.name ?? '-' }}</div>
                                    <div class="text-xs text-muted-foreground">{{ formatCurrency(item.plan.price) }}</div>
                                </td>
                                <td class="px-4 py-3">{{ item.generation || '-' }}</td>
                                <td class="px-4 py-3 text-xs text-muted-foreground">
                                    <div>{{ item.start_date ?? '-' }}</div>
                                    <div>{{ item.end_date ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span :class="['inline-flex rounded-full px-2.5 py-1 text-xs font-medium capitalize', statusClass(item.status)]">
                                        {{ item.status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ item.approver.name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <Button size="sm" variant="outline" @click="openEditDialog(item)">Edit</Button>
                                        <Button v-if="item.pending_payment_id" size="sm" variant="outline" as-child>
                                            <Link :href="route('admin.subscriptions.payments.show', { payment: item.pending_payment_id })"
                                                >Lihat Payment</Link
                                            >
                                        </Button>
                                        <Button v-else size="sm" variant="outline" disabled>Tidak Ada Payment</Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 border-t p-4 text-sm">
                    <p class="text-muted-foreground">
                        Menampilkan {{ pagination.from ?? 0 }} - {{ pagination.to ?? 0 }} dari {{ pagination.total }} subscription
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

        <Dialog v-model:open="editDialogOpen">
            <DialogContent class="max-w-lg">
                <DialogHeader>
                    <DialogTitle>Update Subscription</DialogTitle>
                    <DialogDescription> Ubah status subscription dan atur periode aktif bila diperlukan. </DialogDescription>
                </DialogHeader>

                <div class="grid gap-4">
                    <div>
                        <Label for="status">Status</Label>
                        <select id="status" v-model="updateForm.status" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                            <option value="pending">pending</option>
                            <option value="active">active</option>
                            <option value="inactive">inactive</option>
                            <option value="canceled">canceled</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <Label for="start-date">Start Date</Label>
                            <Input id="start-date" v-model="updateForm.start_date" type="date" />
                        </div>
                        <div>
                            <Label for="end-date">End Date</Label>
                            <Input id="end-date" v-model="updateForm.end_date" type="date" />
                        </div>
                    </div>

                    <div>
                        <Label for="generation">Generation</Label>
                        <Input id="generation" v-model="updateForm.generation" type="number" placeholder="Contoh: 8" />
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="outline" @click="editDialogOpen = false">Batal</Button>
                    <Button :disabled="updateForm.processing" @click="submitUpdate">
                        {{ updateForm.processing ? 'Menyimpan...' : 'Simpan' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
