<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

interface Article {
    id: number;
    title: string;
    slug: string;
    content: string;
    featured_image: string | null;
    featured_image_url: string | null;
    is_published: boolean;
    category: { id: number; name: string; slug: string } | null;
    author: { id: number; name: string } | null;
    created_at: string;
}

const props = defineProps<{
    article: Article;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Articles',
        href: '/dashboard/articles',
    },
    {
        title: props.article.title,
        href: `/articles/${props.article.id}`,
    },
];
</script>

<template>
    <Head :title="article.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-4xl space-y-6 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">{{ article.title }}</h1>
                <div class="flex gap-2">
                    <Button variant="outline" as-child>
                        <Link :href="route('articles.edit', { article: article.id })">Edit</Link>
                    </Button>
                    <Button variant="outline" as-child>
                        <Link :href="route('articles.index')">Kembali</Link>
                    </Button>
                </div>
            </div>

            <div class="rounded-lg border p-5">
                <div class="mb-4 flex flex-wrap gap-2 text-xs text-muted-foreground">
                    <span>Kategori: {{ article.category?.name ?? '-' }}</span>
                    <span>Penulis: {{ article.author?.name ?? '-' }}</span>
                    <span>Status: {{ article.is_published ? 'Published' : 'Draft' }}</span>
                </div>

                <img v-if="article.featured_image_url" :src="article.featured_image_url" alt="featured" class="mb-4 max-h-80 w-full rounded-md object-cover" />

                <article class="whitespace-pre-line text-sm leading-relaxed">
                    {{ article.content }}
                </article>
            </div>
        </div>
    </AppLayout>
</template>
