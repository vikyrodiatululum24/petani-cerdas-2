<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, ShoppingBag, ArrowRight, Tag } from 'lucide-vue-next';

interface Product {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    image_url: string | null;
    price: number;
    discount_price: number | null;
    effective_price: number;
    olshop_links: Record<string, string>;
    category: { id: number; name: string; slug: string } | null;
}

const props = defineProps<{
    product: Product;
    related_products: Product[];
}>();

const formatPrice = (price: number | null) => {
    if (price === null) {
        return '-';
    }

    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(price);
};

const marketplaceLinks = Object.entries(props.product.olshop_links || {}).filter(([, link]) => Boolean(link));
</script>

<template>
    <Head :title="product.name" />

    <PublicLayout>
        <section class="py-10 lg:py-14">
            <div class="mx-auto max-w-6xl px-6 lg:px-8">
                <Button as-child variant="outline" class="mb-6">
                    <Link :href="route('public.products.index')">
                        <ArrowLeft class="h-4 w-4" />
                        Kembali ke Produk
                    </Link>
                </Button>

                <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                    <div class="overflow-hidden rounded-2xl border border-green-100 bg-gradient-to-br from-[#E8F5E9] to-[#C8E6C9]">
                        <img
                            v-if="product.image_url"
                            :src="product.image_url"
                            :alt="product.name"
                            class="h-full w-full object-cover"
                        >
                        <div v-else class="flex h-80 items-center justify-center">
                            <ShoppingBag class="h-16 w-16 text-[#2E7D32]" />
                        </div>
                    </div>

                    <div>
                        <p v-if="product.category" class="inline-flex items-center gap-1 rounded-full bg-[#E8F5E9] px-3 py-1 text-xs font-medium text-[#2E7D32]">
                            <Tag class="h-3.5 w-3.5" />
                            {{ product.category.name }}
                        </p>
                        <h1 class="mt-3 text-3xl font-extrabold text-gray-900">{{ product.name }}</h1>

                        <div class="mt-4 rounded-2xl border border-gray-200 bg-gray-50 p-4">
                            <p class="text-3xl font-extrabold text-[#2E7D32]">{{ formatPrice(product.effective_price) }}</p>
                            <p v-if="product.discount_price" class="mt-1 text-sm text-gray-400 line-through">{{ formatPrice(product.price) }}</p>
                        </div>

                        <p class="mt-5 whitespace-pre-line leading-relaxed text-gray-700">
                            {{ product.description || 'Produk pertanian berkualitas untuk mendukung kebutuhan budidaya Anda.' }}
                        </p>

                        <div v-if="marketplaceLinks.length" class="mt-6 space-y-2">
                            <p class="text-sm font-semibold text-gray-900">Beli di Marketplace:</p>
                            <div class="flex flex-wrap gap-2">
                                <Button
                                    v-for="[marketplace, link] in marketplaceLinks"
                                    :key="marketplace"
                                    variant="outline"
                                    as-child
                                >
                                    <a :href="link" target="_blank" rel="noopener noreferrer">
                                        {{ marketplace }}
                                        <ArrowRight class="h-4 w-4" />
                                    </a>
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section v-if="related_products.length" class="border-t border-gray-100 py-10">
            <div class="mx-auto max-w-6xl px-6 lg:px-8">
                <h2 class="text-xl font-bold text-gray-900">Produk Terkait</h2>
                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <Card
                        v-for="related in related_products"
                        :key="related.id"
                        class="group border-gray-100 transition-all hover:-translate-y-0.5 hover:shadow-md"
                    >
                        <CardContent class="p-4">
                            <h3 class="line-clamp-2 font-semibold text-gray-900 transition-colors group-hover:text-[#2E7D32]">{{ related.name }}</h3>
                            <p class="mt-1 text-sm font-bold text-[#2E7D32]">{{ formatPrice(related.effective_price) }}</p>
                            <Link
                                :href="route('public.products.show', { product: related.slug })"
                                class="mt-2 inline-flex items-center gap-1 text-sm font-semibold text-[#2E7D32]"
                            >
                                Lihat
                                <ArrowRight class="h-4 w-4" />
                            </Link>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
