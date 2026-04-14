<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

interface UserItem {
    id: number;
    name: string;
    email: string;
    role: string | null;
    created_at: string;
    updated_at: string;
}

interface UserProfileItem {
    phone: string | null;
    main_crop: string | null;
    land_type: string | null;
    province: string | null;
    city: string | null;
    district: string | null;
    village: string | null;
    postal_code: string | null;
    address: string | null;
    photo_url: string | null;
}

const props = defineProps<{
    user: UserItem;
    profile: UserProfileItem;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: '/users',
    },
    {
        title: props.user.name,
        href: `/users/${props.user.id}`,
    },
];

const formatDate = (value: string) => {
    return new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(value));
};

const isPetani = props.user.role === 'petani';
</script>

<template>
    <Head :title="user.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-3xl space-y-6 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">{{ user.name }}</h1>
                <div class="flex gap-2">
                    <Button variant="outline" as-child>
                        <Link :href="route('users.edit', { user: user.id })">Edit</Link>
                    </Button>
                    <Button variant="outline" as-child>
                        <Link :href="route('users.index')">Kembali</Link>
                    </Button>
                </div>
            </div>

            <div class="rounded-lg border p-5">
                <dl class="grid gap-4 text-sm md:grid-cols-2">
                    <div>
                        <dt class="text-muted-foreground">Nama</dt>
                        <dd class="font-medium">{{ user.name }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Email</dt>
                        <dd class="font-medium">{{ user.email }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Role</dt>
                        <dd class="font-medium capitalize">{{ user.role ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Dibuat</dt>
                        <dd class="font-medium">{{ formatDate(user.created_at) }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Diupdate</dt>
                        <dd class="font-medium">{{ formatDate(user.updated_at) }}</dd>
                    </div>
                </dl>
            </div>

            <div v-if="isPetani" class="rounded-lg border p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold">Detail Data Petani</h2>
                        <p class="text-sm text-muted-foreground">Informasi profil tambahan untuk akun dengan role petani.</p>
                    </div>
                    <div v-if="profile.photo_url" class="h-14 w-14 overflow-hidden rounded-full border bg-muted">
                        <img :src="profile.photo_url" alt="Foto profil petani" class="h-full w-full object-cover" />
                    </div>
                </div>

                <dl class="mt-4 grid gap-4 text-sm md:grid-cols-2">
                    <div>
                        <dt class="text-muted-foreground">No. Telepon</dt>
                        <dd class="font-medium">{{ profile.phone || '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Komoditas Utama</dt>
                        <dd class="font-medium">{{ profile.main_crop || '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Jenis Lahan</dt>
                        <dd class="font-medium">{{ profile.land_type || '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Provinsi</dt>
                        <dd class="font-medium">{{ profile.province || '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Kota</dt>
                        <dd class="font-medium">{{ profile.city || '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Kecamatan</dt>
                        <dd class="font-medium">{{ profile.district || '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Desa</dt>
                        <dd class="font-medium">{{ profile.village || '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Kode Pos</dt>
                        <dd class="font-medium">{{ profile.postal_code || '-' }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-muted-foreground">Alamat</dt>
                        <dd class="font-medium">{{ profile.address || '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </AppLayout>
</template>
