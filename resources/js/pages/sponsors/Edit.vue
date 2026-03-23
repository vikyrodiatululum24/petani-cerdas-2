<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface Sponsor {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    logo_path: string | null;
    logo_url: string | null;
}

const props = defineProps<{
    sponsor: Sponsor;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Sponsors',
        href: '/sponsors',
    },
    {
        title: 'Edit',
        href: `/sponsors/${props.sponsor.id}/edit`,
    },
];

const form = useForm({
    name: props.sponsor.name,
    description: props.sponsor.description ?? '',
    logo_path: null as File | null,
    remove_logo: false,
});

const submit = () => {
    form
        .transform((data) => ({
            ...data,
            _method: 'put',
        }))
        .post(route('sponsors.update', { sponsor: props.sponsor.id }), {
            forceFormData: true,
        });
};

const onLogoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    form.logo_path = target.files?.[0] ?? null;
};
</script>

<template>
    <Head title="Edit Sponsor" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-3xl space-y-6 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Edit Sponsor</h1>
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
                    <div v-if="sponsor.logo_url" class="mb-2">
                        <img :src="sponsor.logo_url" alt="logo sponsor" class="h-20 w-20 rounded object-cover" />
                    </div>
                    <input
                        id="logo"
                        type="file"
                        accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml"
                        class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        @change="onLogoChange"
                    />
                    <label class="mt-2 flex items-center gap-2 text-sm">
                        <input v-model="form.remove_logo" type="checkbox" class="rounded border-input" />
                        Hapus logo lama
                    </label>
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

                <Button :disabled="form.processing">Update Sponsor</Button>
            </form>
        </div>
    </AppLayout>
</template>
