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

interface UserItem {
    id: number;
    name: string;
    email: string;
    role: string | null;
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
    role: string | null;
    per_page: number;
}

const props = defineProps<{
    users: UserItem[];
    pagination: Pagination;
    filters: Filters;
    roles: string[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: '/users',
    },
];

const confirmDeleteOpen = ref(false);
const userToDelete = ref<UserItem | null>(null);

const filterForm = useForm({
    search: props.filters.search ?? '',
    role: props.filters.role,
    per_page: props.filters.per_page ?? 10,
});

const openDeleteDialog = (user: UserItem) => {
    userToDelete.value = user;
    confirmDeleteOpen.value = true;
};

const confirmDelete = () => {
    if (!userToDelete.value) {
        return;
    }

    router.delete(route('users.destroy', { user: userToDelete.value.id }), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Pengguna berhasil dihapus.');
            confirmDeleteOpen.value = false;
            userToDelete.value = null;
        },
        onError: (errors) => {
            toast.error(errors.user ?? 'Pengguna gagal dihapus.');
            confirmDeleteOpen.value = false;
        },
    });
};

const applyFilters = () => {
    router.get(
        route('users.index'),
        {
            search: filterForm.search || undefined,
            role: filterForm.role || undefined,
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
    () => [filterForm.role, filterForm.per_page],
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
    filterForm.role = null;
    filterForm.per_page = 10;
    applyFilters();
};
</script>

<template>
    <Head title="Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold">Manajemen Pengguna</h1>
                    <p class="text-sm text-muted-foreground">Kelola akun pengguna dan role dalam satu halaman.</p>
                </div>

                <Button as-child>
                    <Link :href="route('users.create')">Tambah Pengguna</Link>
                </Button>
            </div>

            <div class="max-w-full overflow-hidden rounded-lg border">
                <div class="grid gap-3 border-b bg-muted/30 p-4 md:grid-cols-4">
                    <div class="md:col-span-3">
                        <Label for="search">Search</Label>
                        <Input id="search" v-model="filterForm.search" placeholder="Cari nama atau email" />
                    </div>

                    <div>
                        <Label for="role-filter">Role</Label>
                        <select
                            id="role-filter"
                            v-model="filterForm.role"
                            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        >
                            <option :value="null">Semua role</option>
                            <option v-for="role in roles" :key="role" :value="role">
                                {{ role }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-2 px-6 py-4">
                    <Button size="sm" @click="resetFilters">Reset</Button>
                    <div class="flex items-center gap-2">
                        <Label for="per-page" class="whitespace-nowrap text-sm">Per Page</Label>
                        <select
                            id="per-page"
                            v-model="filterForm.per_page"
                            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
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
                                <th class="px-4 py-3 font-medium">Nama</th>
                                <th class="px-4 py-3 font-medium">Email</th>
                                <th class="px-4 py-3 font-medium">Role</th>
                                <th class="px-4 py-3 font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="users.length === 0">
                                <td colspan="4" class="px-4 py-6 text-center text-muted-foreground">Belum ada pengguna.</td>
                            </tr>
                            <tr v-for="user in users" :key="user.id" class="border-t">
                                <td class="px-4 py-3 font-medium">{{ user.name }}</td>
                                <td class="px-4 py-3">{{ user.email }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full bg-muted px-2.5 py-1 text-xs font-medium capitalize">
                                        {{ user.role ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <Button size="sm" variant="outline" as-child>
                                            <Link :href="route('users.show', { user: user.id })">Detail</Link>
                                        </Button>
                                        <Button size="sm" variant="outline" as-child>
                                            <Link :href="route('users.edit', { user: user.id })">Edit</Link>
                                        </Button>
                                        <Button size="sm" variant="destructive" @click="openDeleteDialog(user)">Hapus</Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 border-t p-4 text-sm">
                    <p class="text-muted-foreground">
                        Menampilkan {{ pagination.from ?? 0 }} - {{ pagination.to ?? 0 }} dari {{ pagination.total }} pengguna
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
                    <DialogTitle>Hapus Pengguna</DialogTitle>
                    <DialogDescription>
                        Pengguna <strong>{{ userToDelete?.name }}</strong> akan dihapus permanen.
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
