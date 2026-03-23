<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowRight, CalendarDays, Leaf, Search, User } from 'lucide-vue-next';
import { onBeforeUnmount, ref, watch } from 'vue';

interface ArticleCategory {
    id: number;
    name: string;
    slug: string;
    description: string | null;
}

interface Article {
    id: number;
    title: string;
    slug: string;
    content: string;
    featured_image_url: string | null;
    category: { id: number; name: string; slug: string } | null;
    author: { id: number; name: string } | null;
    created_at: string;
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
    articles: Article[];
    categories: ArticleCategory[];
    category_counts: Record<string, number>;
    pagination: Pagination;
    filters: Filters;
}>();

const search = ref(props.filters.search ?? '');

const applyFilters = (categorySlug: string | null = props.filters.category) => {
    router.get(
        route('public.articles.index'),
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

const formatDate = (date: string) => {
    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    }).format(new Date(date));
};

const getExcerpt = (content: string) => {
    const plain = content.replace(/<[^>]*>/g, '').trim();
    return plain.length > 160 ? `${plain.slice(0, 160)}...` : plain;
};
</script>

<template>
    <Head title="Artikel Pertanian" />

    <PublicLayout>
        <section class="relative overflow-hidden bg-gradient-to-br from-[#1f5a2f] via-[#2E7D32] to-[#5fae62] py-16 lg:py-24">
            <div
                class="absolute inset-0 opacity-15"
                style="background-image: radial-gradient(circle, #ffffff 1px, transparent 1px); background-size: 26px 26px"
            />
            <div class="relative mx-auto max-w-7xl px-6 lg:px-8">
                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-green-100">Katalog Artikel</p>
                <h1 class="mt-2 text-3xl font-extrabold text-white lg:text-4xl">Artikel Pertanian Pilihan Petani Cerdas</h1>
                <p class="text-green-100">Jelajahi artikel yang sudah digunakan komunitas petani kami, lengkap dengan informasi dan tautan sumber.</p>
            </div>
        </section>

        <!-- Filter Section -->
        <section class="mb-10 lg:mb-14">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mt-8 flex flex-col gap-3 rounded-2xl border border-green-100 bg-white p-4 shadow-sm md:flex-row md:items-center">
                    <form class="relative w-full flex-1" @submit.prevent="applyFilters()">
                        <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                        <Input v-model="search" placeholder="Cari judul atau isi artikel" class="pl-9" />
                    </form>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <Button size="sm" class="rounded-full bg-[#2E7D32] text-white hover:bg-[#256427]" @click="applyFilters(null)">
                        Semua ({{ pagination.total }})
                    </Button>
                    <Button
                        v-for="category in categories"
                        :key="category.id"
                        size="sm"
                        class="rounded-full bg-[#2E7D32] text-white hover:bg-[#256427]"
                        @click="applyFilters(category.slug)"
                    >
                        {{ category.name }} ({{ category_counts[category.slug] ?? 0 }})
                    </Button>
                </div>
            </div>
        </section>

        <section class="mb-10 lg:mb-14">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mb-6 flex items-center justify-between">
                    <p class="text-sm text-gray-500">
                        Menampilkan {{ pagination.from ?? 0 }}-{{ pagination.to ?? 0 }} dari {{ pagination.total }} artikel
                    </p>
                </div>

                <div v-if="articles.length" class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                    <Card
                        v-for="article in articles"
                        :key="article.id"
                        class="group overflow-hidden border-gray-100 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md"
                    >
                        <div class="aspect-video w-full overflow-hidden bg-gradient-to-br from-[#E8F5E9] to-[#C8E6C9]">
                            <img
                                v-if="article.featured_image_url"
                                :src="article.featured_image_url"
                                :alt="article.title"
                                class="h-full w-full object-cover"
                            />
                            <div v-else class="flex h-full items-center justify-center">
                                <Leaf class="h-10 w-10 text-[#2E7D32]" />
                            </div>
                        </div>
                        <CardContent class="p-5">
                            <div class="mb-3 flex flex-wrap items-center gap-2 text-xs text-gray-500">
                                <span v-if="article.category" class="rounded-full bg-[#E8F5E9] px-2.5 py-0.5 font-medium text-[#2E7D32]">
                                    {{ article.category.name }}
                                </span>
                                <span class="inline-flex items-center gap-1">
                                    <CalendarDays class="h-3.5 w-3.5" />
                                    {{ formatDate(article.created_at) }}
                                </span>
                                <span v-if="article.author" class="inline-flex items-center gap-1">
                                    <User class="h-3.5 w-3.5" />
                                    {{ article.author.name }}
                                </span>
                            </div>

                            <h2 class="line-clamp-2 text-lg font-bold leading-snug text-gray-900 transition-colors group-hover:text-[#2E7D32]">
                                {{ article.title }}
                            </h2>
                            <p class="mt-2 text-sm leading-relaxed text-gray-600">
                                {{ getExcerpt(article.content) }}
                            </p>

                            <Link
                                :href="route('public.articles.show', { article: article.slug })"
                                class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-[#2E7D32] transition-all hover:gap-2"
                            >
                                Baca selengkapnya
                                <ArrowRight class="h-4 w-4" />
                            </Link>
                        </CardContent>
                    </Card>
                </div>

                <Card v-else class="border-dashed">
                    <CardContent class="py-10 text-center text-gray-500"> Artikel belum tersedia untuk filter yang dipilih. </CardContent>
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
