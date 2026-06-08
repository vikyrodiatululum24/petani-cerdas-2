<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowLeft, ArrowRight, CalendarDays, Facebook, Leaf, Linkedin, MessageCircle, Twitter, User } from 'lucide-vue-next';
import { computed } from 'vue';

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

const props = defineProps<{
    article: Article;
    related_articles: Article[];
}>();

const page = usePage();

const currentUrl = computed(() => {
    if (typeof window === 'undefined') {
        return page.url;
    }

    return `${window.location.origin}${page.url}`;
});

const encodedUrl = computed(() => encodeURIComponent(currentUrl.value));
const encodedTitle = computed(() => encodeURIComponent(props.article.title));

const shareLinks = computed(() => [
    {
        label: 'WhatsApp',
        href: `https://wa.me/?text=${encodedTitle.value}%20${encodedUrl.value}`,
        icon: MessageCircle,
    },
    {
        label: 'Facebook',
        href: `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl.value}`,
        icon: Facebook,
    },
    {
        label: 'X',
        href: `https://twitter.com/intent/tweet?text=${encodedTitle.value}&url=${encodedUrl.value}`,
        icon: Twitter,
    },
    {
        label: 'LinkedIn',
        href: `https://www.linkedin.com/sharing/share-offsite/?url=${encodedUrl.value}`,
        icon: Linkedin,
    },
]);

const formatDate = (date: string) => {
    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    }).format(new Date(date));
};

const getExcerpt = (content: string) => {
    const plain = content.replace(/<[^>]*>/g, '').trim();
    return plain.length > 120 ? `${plain.slice(0, 120)}...` : plain;
};
</script>

<template>
    <Head :title="article.title" />

    <PublicLayout>
        <section class="bg-gradient-to-b from-[#eef8ef] to-white py-10">
            <div class="mx-auto max-w-4xl px-6 lg:px-8">
                <Button as-child variant="outline" class="mb-6">
                    <Link :href="route('public.articles.index')">
                        <ArrowLeft class="h-4 w-4" />
                        Kembali ke Artikel
                    </Link>
                </Button>

                <div class="mb-4 flex flex-wrap items-center gap-3 text-sm text-gray-500">
                    <span v-if="article.category" class="rounded-full bg-[#E8F5E9] px-3 py-1 font-medium text-[#2E7D32]">
                        {{ article.category.name }}
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <CalendarDays class="h-4 w-4" />
                        {{ formatDate(article.created_at) }}
                    </span>
                    <span v-if="article.author" class="inline-flex items-center gap-1">
                        <User class="h-4 w-4" />
                        {{ article.author.name }}
                    </span>
                </div>

                <h1 class="text-3xl font-extrabold leading-tight text-gray-900 lg:text-4xl">{{ article.title }}</h1>

                <div class="mt-5 flex flex-wrap items-center gap-2.5">
                    Bagikan:
                    <a
                        v-for="share in shareLinks"
                        :key="share.label"
                        :href="share.href"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-1.5 rounded-full border border-gray-200 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 transition-colors hover:border-[#2E7D32] hover:text-[#2E7D32]"
                    >
                        <component :is="share.icon" class="h-4 w-4" />
                        {{ share.label }}
                    </a>
                </div>

                <div class="mt-6 overflow-hidden rounded-2xl border border-green-100 bg-gradient-to-br from-[#E8F5E9] to-[#C8E6C9]">
                    <img
                        v-if="article.featured_image_url"
                        :src="article.featured_image_url"
                        :alt="article.title"
                        class="h-full w-full object-cover"
                    />
                    <div v-else class="flex h-72 items-center justify-center">
                        <Leaf class="h-16 w-16 text-[#2E7D32]" />
                    </div>
                </div>

                <article class="prose prose-gray mt-8 max-w-none whitespace-pre-line leading-8 text-gray-700">
                    {{ article.content }}
                </article>
            </div>
        </section>

        <section v-if="related_articles.length" class="border-t border-gray-100 py-10">
            <div class="mx-auto max-w-4xl px-6 lg:px-8">
                <h2 class="text-xl font-bold text-gray-900">Artikel Terkait</h2>
                <div class="mt-4 grid gap-4 md:grid-cols-3">
                    <Card
                        v-for="related in related_articles"
                        :key="related.id"
                        class="group border-gray-100 transition-all hover:-translate-y-0.5 hover:shadow-md"
                    >
                        <CardContent class="p-4">
                            <p class="text-xs text-gray-500">{{ formatDate(related.created_at) }}</p>
                            <h3 class="mt-1 line-clamp-2 font-semibold text-gray-900 transition-colors group-hover:text-[#2E7D32]">
                                {{ related.title }}
                            </h3>
                            <p class="mt-2 text-sm text-gray-600">{{ getExcerpt(related.content) }}</p>
                            <Link
                                :href="route('public.articles.show', { article: related.slug })"
                                class="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-[#2E7D32]"
                            >
                                Baca
                                <ArrowRight class="h-4 w-4" />
                            </Link>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
