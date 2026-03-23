<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

interface Sponsor {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    logo_url: string | null;
    created_at: string;
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
        title: props.sponsor.name,
        href: `/sponsors/${props.sponsor.id}`,
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
    <Head :title="sponsor.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-4xl space-y-6 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">{{ sponsor.name }}</h1>
                <div class="flex gap-2">
                    <Button variant="outline" as-child>
                        <Link :href="route('sponsors.edit', { sponsor: sponsor.id })">Edit</Link>
                    </Button>
                    <Button variant="outline" as-child>
                        <Link :href="route('sponsors.index')">Kembali</Link>
                    </Button>
                </div>
            </div>

            <div class="rounded-lg border p-5">
                <div class="mb-4 text-sm text-muted-foreground">Slug: /{{ sponsor.slug }}</div>

                <img v-if="sponsor.logo_url" :src="sponsor.logo_url" alt="logo sponsor" class="mb-6 h-28 w-28 rounded object-cover" />

                <div class="space-y-2">
                    <p class="text-sm font-medium">Deskripsi</p>
                    <p class="whitespace-pre-line text-sm leading-relaxed text-muted-foreground">{{ sponsor.description || '-' }}</p>
                </div>

                <p class="mt-6 text-xs text-muted-foreground">Dibuat: {{ formatDate(sponsor.created_at) }}</p>
            </div>
        </div>
    </AppLayout>
</template>
