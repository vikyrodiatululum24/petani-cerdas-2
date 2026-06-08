<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

interface Product {
    id: number;
    name: string;
    slug: string;
    description: string;
    price: number;
    discount_price: number | null;
    effective_price: number;
    image_path: string | null;
    image_url: string | null;
    olshop_links: Record<string, string> | null;
    category: { id: number; name: string; slug: string } | null;
    created_at: string;
}

const props = defineProps<{
    product: Product;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Products',
        href: '/dashboard/products',
    },
    {
        title: props.product.name,
        href: `/products/${props.product.id}`,
    },
];

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(price);
};

const formatPlatformName = (key: string) => {
    const labels: Record<string, string> = {
        shopee: 'Shopee',
        tokopedia: 'Tokopedia',
        lazada: 'Lazada',
        tiktok_shop: 'TikTok Shop',
        blibli: 'Blibli',
        lainnya: 'Lainnya',
    };

    return labels[key] ?? key;
};
</script>

<template>
    <Head :title="product.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-4xl space-y-6 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">{{ product.name }}</h1>
                <div class="flex gap-2">
                    <Button variant="outline" as-child>
                        <Link :href="route('products.edit', { product: product.id })">Edit</Link>
                    </Button>
                    <Button variant="outline" as-child>
                        <Link :href="route('products.index')">Kembali</Link>
                    </Button>
                </div>
            </div>

            <div class="rounded-lg border p-5">
                <div class="mb-4 flex flex-wrap gap-3 text-xs text-muted-foreground">
                    <span>Kategori: {{ product.category?.name ?? '-' }}</span>
                </div>

                <img v-if="product.image_url" :src="product.image_url" alt="product image" class="mb-6 max-h-80 w-full rounded-md object-cover" />

                <div class="mb-6 space-y-2">
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-bold">{{ formatPrice(product.effective_price) }}</span>
                        <span v-if="product.discount_price" class="text-lg text-muted-foreground line-through">
                            {{ formatPrice(product.price) }}
                        </span>
                    </div>
                </div>

                <div v-if="product.olshop_links && Object.keys(product.olshop_links).length" class="mb-6 space-y-2">
                    <p class="text-sm font-medium">Link Online Shop</p>
                    <div class="flex flex-wrap gap-2">
                        <a
                            v-for="(url, platform) in product.olshop_links"
                            :key="platform"
                            :href="url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex rounded-full border px-3 py-1 text-xs font-medium text-primary hover:bg-muted"
                        >
                            {{ formatPlatformName(platform) }}
                        </a>
                    </div>
                </div>

                <article class="whitespace-pre-line text-sm leading-relaxed">
                    {{ product.description }}
                </article>
            </div>
        </div>
    </AppLayout>
</template>
