<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    roles: string[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: '/users',
    },
    {
        title: 'Create',
        href: '/users/create',
    },
];

const form = useForm({
    name: '',
    email: '',
    role: props.roles[0] ?? '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('users.store'));
};
</script>

<template>
    <Head title="Create User" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-2xl space-y-6 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Buat Pengguna</h1>
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
                    <Label for="password">Password</Label>
                    <Input id="password" v-model="form.password" type="password" placeholder="Minimal 8 karakter" />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="space-y-1.5">
                    <Label for="password_confirmation">Konfirmasi Password</Label>
                    <Input id="password_confirmation" v-model="form.password_confirmation" type="password" placeholder="Ulangi password" />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <Button :disabled="form.processing">Simpan Pengguna</Button>
            </form>
        </div>
    </AppLayout>
</template>
