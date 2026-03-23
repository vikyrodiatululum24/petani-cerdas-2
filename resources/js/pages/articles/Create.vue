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

const props = defineProps<{
    categories: CategoryOption[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Articles',
        href: '/dashboard/articles',
    },
    {
        title: 'Create',
        href: '/dashboard/articles/create',
    },
];

const form = useForm({
    title: '',
    content: '',
    featured_image: null as File | null,
    category_id: props.categories[0]?.id ?? null,
    is_published: false,
});

const submit = () => {
    form.post(route('articles.store'), {
        forceFormData: true,
    });
};

const onFeaturedImageChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    form.featured_image = target.files?.[0] ?? null;
};
</script>

<template>
    <Head title="Create Article" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-3xl space-y-6 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Buat Artikel</h1>
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
                        <option :value="null" disabled>Pilih kategori</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.category_id" />
                </div>

                <div class="space-y-1.5">
                    <Label for="featured-image">Featured Image</Label>
                    <input
                        id="featured-image"
                        type="file"
                        accept="image/png,image/jpeg,image/jpg,image/webp"
                        class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        @change="onFeaturedImageChange"
                    />
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
                    Publish sekarang
                </label>

                <Button :disabled="form.processing">Simpan Artikel</Button>
            </form>
        </div>
    </AppLayout>
</template>
