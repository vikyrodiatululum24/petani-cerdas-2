<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowRight, Search, ShoppingBag, Tag } from 'lucide-vue-next';
import { onBeforeUnmount, ref, watch } from 'vue';

interface ProductCategory {
    id: number;
    name: string;
    slug: string;
    description: string | null;
}

interface Product {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    image_url: string | null;
    price: number;
    discount_price: number | null;
    effective_price: number;
    category: { id: number; name: string; slug: string } | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Pagination {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: PaginationLink[];
}

interface Filters {
    search: string;
    category: string | null;
    per_page: number;
}

const props = defineProps<{
    products: Product[];
    categories: ProductCategory[];
    category_counts: Record<string, number>;
    pagination: Pagination;
    filters: Filters;
}>();

const search = ref(props.filters.search ?? '');

const applyFilters = (categorySlug: string | null = props.filters.category) => {
    router.get(
        route('public.products.index'),
        {
            search: search.value || undefined,
            category: categorySlug || undefined,
            per_page: props.filters.per_page,
        },
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        },
    );
};

let searchDebounceTimer: ReturnType<typeof setTimeout> | null = null;

watch(search, () => {
    if (searchDebounceTimer) {
        clearTimeout(searchDebounceTimer);
    }

    searchDebounceTimer = setTimeout(() => {
        applyFilters();
    }, 500);
});

onBeforeUnmount(() => {
    if (searchDebounceTimer) {
        clearTimeout(searchDebounceTimer);
    }
});

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
</script>

<template>
    <Head title="Produk Pertanian" />

    <PublicLayout>
        <section class="relative overflow-hidden bg-gradient-to-br from-[#1f5a2f] via-[#2E7D32] to-[#5fae62] py-16 lg:py-24">
            <div
                class="absolute inset-0 opacity-15"
                style="background-image: radial-gradient(circle, #ffffff 1px, transparent 1px); background-size: 26px 26px"
            />
            <div class="relative mx-auto max-w-7xl px-6 lg:px-8">
                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-green-100">Katalog Produk</p>
                <h1 class="mt-2 text-3xl font-extrabold text-white lg:text-4xl">Produk Pertanian Pilihan Petani Cerdas</h1>
                <p class="text-green-100">Jelajahi produk yang sudah digunakan komunitas petani kami, lengkap dengan harga dan tautan marketplace.</p>
            </div>
        </section>

        <!-- Filter Section -->
        <section class="mb-10 md:mb-14">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mt-8 flex flex-col gap-3 rounded-2xl border border-green-100 bg-white shadow-sm md:flex-row md:items-center">
                    <form class="relative w-full flex-1" @submit.prevent="applyFilters()">
                        <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                        <Input v-model="search" placeholder="Cari nama atau deskripsi produk" class="pl-9" />
                    </form>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <Button size="sm" class="rounded-full bg-[#2E7D32] text-white hover:bg-[#256427]" @click="applyFilters(null)"> Semua </Button>
                    <Button
                        v-for="category in categories"
                        :key="category.id"
                        size="sm"
                        class="rounded-full bg-[#2E7D32] text-white hover:bg-[#256427]"
                        @click="applyFilters(category.slug)"
                    >
                        {{ category.name }}
                    </Button>
                </div>
            </div>
        </section>

        <section class="mb-10 lg:mb-14">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <p class="mb-6 text-sm text-gray-500">
                    Menampilkan {{ pagination.from ?? 0 }}-{{ pagination.to ?? 0 }} dari {{ pagination.total }} produk
                </p>

                <div v-if="products.length" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <Card
                        v-for="product in products"
                        :key="product.id"
                        class="group overflow-hidden border-gray-100 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md"
                    >
                        <div class="aspect-square overflow-hidden bg-gradient-to-br from-[#E8F5E9] to-[#C8E6C9]">
                            <img v-if="product.image_url" :src="product.image_url" :alt="product.name" class="h-full w-full object-cover" />
                            <div v-else class="flex h-full items-center justify-center">
                                <ShoppingBag class="h-10 w-10 text-[#2E7D32]" />
                            </div>
                        </div>
                        <CardContent class="p-4">
                            <p
                                v-if="product.category"
                                class="inline-flex items-center gap-1 rounded-full bg-[#E8F5E9] px-2.5 py-0.5 text-xs font-medium text-[#2E7D32]"
                            >
                                <Tag class="h-3.5 w-3.5" />
                                {{ product.category.name }}
                            </p>
                            <h2 class="mt-2 line-clamp-2 text-base font-bold leading-snug text-gray-900 transition-colors group-hover:text-[#2E7D32]">
                                {{ product.name }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ product.description || 'Produk pertanian berkualitas dari mitra Petani Cerdas.' }}
                            </p>

                            <div class="mt-3">
                                <p class="text-lg font-bold text-[#2E7D32]">{{ formatPrice(product.effective_price) }}</p>
                                <p v-if="product.discount_price" class="text-xs text-gray-400 line-through">{{ formatPrice(product.price) }}</p>
                            </div>

                            <Link
                                :href="route('public.products.show', { product: product.slug })"
                                class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-[#2E7D32] transition-all hover:gap-2"
                            >
                                Lihat detail
                                <ArrowRight class="h-4 w-4" />
                            </Link>
                        </CardContent>
                    </Card>
                </div>

                <Card v-else class="border-dashed">
                    <CardContent class="py-10 text-center text-gray-500"> Produk belum tersedia untuk filter yang dipilih. </CardContent>
                </Card>

                <div v-if="pagination.last_page > 1" class="mt-8 flex flex-wrap justify-center gap-2">
                    <Button
                        v-for="link in pagination.links"
                        :key="`${link.label}-${link.active}`"
                        :disabled="!link.url"
                        :variant="link.active ? 'default' : 'outline'"
                        size="sm"
                        as-child
                    >
                        <Link :href="link.url || '#'" v-html="link.label" />
                    </Button>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
