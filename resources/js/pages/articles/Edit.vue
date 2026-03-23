<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface CategoryOption {
    id: number;
    name: string;
}

interface Article {
    id: number;
    title: string;
    slug: string;
    content: string;
    featured_image: string | null;
    featured_image_url: string | null;
    category_id: number;
    is_published: boolean;
}

const props = defineProps<{
    article: Article;
    categories: CategoryOption[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Articles',
        href: '/dashboard/articles',
    },
    {
        title: 'Edit',
        href: `/articles/${props.article.id}/edit`,
    },
];

const form = useForm({
    title: props.article.title,
    content: props.article.content,
    featured_image: null as File | null,
    remove_featured_image: false,
    category_id: props.article.category_id,
    is_published: props.article.is_published,
});

const submit = () => {
    form
        .transform((data) => ({
            ...data,
            _method: 'put',
        }))
        .post(route('articles.update', { article: props.article.id }), {
        forceFormData: true,
    });
};

const onFeaturedImageChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    form.featured_image = target.files?.[0] ?? null;
};
</script>

<template>
    <Head title="Edit Article" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-3xl space-y-6 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Edit Artikel</h1>
                <Button variant="outline" as-child>
                    <Link :href="route('articles.index')">Kembali</Link>
                </Button>
            </div>

            <form class="space-y-4 rounded-lg border p-4" @submit.prevent="submit">
                <div class="space-y-1.5">
                    <Label for="title">Judul</Label>
                    <Input id="title" v-model="form.title" placeholder="Judul artikel" />
                    <InputError :message="form.errors.title" />
                </div>

                <div class="space-y-1.5">
                    <Label for="category">Kategori</Label>
                    <select id="category" v-model="form.category_id" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                        <option v-for="category in categories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.category_id" />
                </div>

                <div class="space-y-1.5">
                    <Label for="featured-image">Featured Image</Label>
                    <div v-if="article.featured_image_url" class="mb-2">
                        <img :src="article.featured_image_url" alt="featured" class="h-28 w-full rounded-md object-cover md:w-56" />
                    </div>
                    <input
                        id="featured-image"
                        type="file"
                        accept="image/png,image/jpeg,image/jpg,image/webp"
                        class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        @change="onFeaturedImageChange"
                    />
                    <label class="mt-2 flex items-center gap-2 text-sm">
                        <input v-model="form.remove_featured_image" type="checkbox" class="rounded border-input" />
                        Hapus featured image lama
                    </label>
                    <InputError :message="form.errors.featured_image" />
                </div>

                <div class="space-y-1.5">
                    <Label for="content">Konten</Label>
                    <textarea
                        id="content"
                        v-model="form.content"
                        rows="10"
                        class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        placeholder="Tulis konten artikel di sini"
                    />
                    <InputError :message="form.errors.content" />
                </div>

                <label class="flex items-center gap-2 text-sm">
                    <input v-model="form.is_published" type="checkbox" class="rounded border-input" />
                    Publish
                </label>

                <Button :disabled="form.processing">Update Artikel</Button>
            </form>
        </div>
    </AppLayout>
</template>
