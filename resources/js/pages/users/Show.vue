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

const props = defineProps<{
    user: UserItem;
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
        </div>
    </AppLayout>
</template>
