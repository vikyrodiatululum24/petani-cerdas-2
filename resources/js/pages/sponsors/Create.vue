<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Sponsors',
        href: '/sponsors',
    },
    {
        title: 'Create',
        href: '/sponsors/create',
    },
];

const form = useForm({
    name: '',
    description: '',
    logo_path: null as File | null,
});

const submit = () => {
    form.post(route('sponsors.store'), {
        forceFormData: true,
    });
};

const onLogoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    form.logo_path = target.files?.[0] ?? null;
};
</script>

<template>
    <Head title="Create Sponsor" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-3xl space-y-6 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Buat Sponsor</h1>
                <Button variant="outline" as-child>
                    <Link :href="route('sponsors.index')">Kembali</Link>
                </Button>
            </div>

            <form class="space-y-4 rounded-lg border p-4" @submit.prevent="submit">
                <div class="space-y-1.5">
                    <Label for="name">Nama Sponsor</Label>
                    <Input id="name" v-model="form.name" placeholder="Nama sponsor" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="space-y-1.5">
                    <Label for="logo">Logo Sponsor</Label>
                    <input
                        id="logo"
                        type="file"
                        accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml"
                        class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        @change="onLogoChange"
                    />
                    <InputError :message="form.errors.logo_path" />
                </div>

                <div class="space-y-1.5">
                    <Label for="description">Deskripsi</Label>
                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="6"
                        class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        placeholder="Deskripsi sponsor"
                    />
                    <InputError :message="form.errors.description" />
                </div>

                <Button :disabled="form.processing">Simpan Sponsor</Button>
            </form>
        </div>
    </AppLayout>
</template>
