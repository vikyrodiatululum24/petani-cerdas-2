<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

interface Plan {
    id: number;
    name: string;
    price: number;
    discount_price: number | null;
    effective_price: number;
    quota: number | null;
    features: string[];
    description: string | null;
    duration_months: number;
    is_featured: boolean;
}

const props = defineProps<{
    plan: Plan;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Subscription Plans',
        href: '/subscription-plans',
    },
    {
        title: props.plan.name,
        href: `/subscription-plans/${props.plan.id}`,
    },
];

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(price);
};
</script>

<template>
    <Head :title="plan.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-4xl space-y-6 p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-semibold">{{ plan.name }}</h1>
                    <span v-if="plan.is_featured" class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700">Featured</span>
                </div>
                <div class="flex gap-2">
                    <Button variant="outline" as-child>
                        <Link :href="route('subscription-plans.edit', { subscription_plan: plan.id })">Edit</Link>
                    </Button>
                    <Button variant="outline" as-child>
                        <Link :href="route('subscription-plans.index')">Kembali</Link>
                    </Button>
                </div>
            </div>

            <div class="rounded-lg border p-5">
                <div class="grid gap-4 text-sm md:grid-cols-2">
                    <div>
                        <p class="text-muted-foreground">Harga</p>
                        <p class="text-xl font-semibold">{{ formatPrice(plan.effective_price) }}</p>
                        <p v-if="plan.discount_price" class="text-xs text-muted-foreground line-through">{{ formatPrice(plan.price) }}</p>
                    </div>
                    <div>
                        <p class="text-muted-foreground">Durasi</p>
                        <p class="font-medium">{{ plan.duration_months }} bulan</p>
                    </div>
                    <div>
                        <p class="text-muted-foreground">Kuota</p>
                        <p class="font-medium">{{ plan.quota ?? 'Unlimited' }}</p>
                    </div>
                </div>

                <div class="mt-6">
                    <p class="mb-2 text-sm font-medium">Fitur</p>
                    <ul v-if="plan.features?.length" class="list-disc space-y-1 pl-5 text-sm">
                        <li v-for="(feature, index) in plan.features" :key="index">{{ feature }}</li>
                    </ul>
                    <p v-else class="text-sm text-muted-foreground">Belum ada fitur.</p>
                </div>

                <div class="mt-6">
                    <p class="mb-2 text-sm font-medium">Deskripsi</p>
                    <p class="whitespace-pre-line text-sm leading-relaxed text-muted-foreground">{{ plan.description || '-' }}</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
