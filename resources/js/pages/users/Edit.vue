<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface UserItem {
    id: number;
    name: string;
    email: string;
    role: string | null;
}

const props = defineProps<{
    user: UserItem;
    roles: string[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: '/users',
    },
    {
        title: 'Edit',
        href: `/users/${props.user.id}/edit`,
    },
];

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    role: props.user.role ?? props.roles[0] ?? '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        _method: 'put',
    })).post(route('users.update', { user: props.user.id }));
};
</script>

<template>
    <Head title="Edit User" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-2xl space-y-6 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Edit Pengguna</h1>
                <Button variant="outline" as-child>
                    <Link :href="route('users.index')">Kembali</Link>
                </Button>
            </div>

            <form class="space-y-4 rounded-lg border p-4" @submit.prevent="submit">
                <div class="space-y-1.5">
                    <Label for="name">Nama</Label>
                    <Input id="name" v-model="form.name" placeholder="Nama pengguna" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="space-y-1.5">
                    <Label for="email">Email</Label>
                    <Input id="email" v-model="form.email" type="email" placeholder="email@domain.com" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="space-y-1.5">
                    <Label for="role">Role</Label>
                    <select id="role" v-model="form.role" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                        <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
                    </select>
                    <InputError :message="form.errors.role" />
                </div>

                <div class="space-y-1.5">
                    <Label for="password">Password Baru (opsional)</Label>
                    <Input id="password" v-model="form.password" type="password" placeholder="Kosongkan jika tidak diubah" />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="space-y-1.5">
                    <Label for="password_confirmation">Konfirmasi Password Baru</Label>
                    <Input id="password_confirmation" v-model="form.password_confirmation" type="password" placeholder="Ulangi password baru" />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <Button :disabled="form.processing">Update Pengguna</Button>
            </form>
        </div>
    </AppLayout>
</template>
