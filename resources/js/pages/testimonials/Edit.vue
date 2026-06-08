<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface Testimonial {
    id: number;
    name: string;
    role: string | null;
    content: string;
    rating: number;
    avatar_color: string;
    is_published: boolean;
    sort_order: number;
}

const props = defineProps<{
    testimonial: Testimonial;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Testimonials',
        href: '/admin/testimonials',
    },
    {
        title: 'Edit',
        href: `/admin/testimonials/${props.testimonial.id}/edit`,
    },
];

const form = useForm({
    name: props.testimonial.name,
    role: props.testimonial.role ?? '',
    content: props.testimonial.content,
    rating: props.testimonial.rating,
    avatar_color: props.testimonial.avatar_color,
    sort_order: props.testimonial.sort_order,
    is_published: props.testimonial.is_published,
});

const submit = () => {
    form.put(route('testimonials.update', { testimonial: props.testimonial.id }));
};
</script>

<template>
    <Head title="Edit Testimonial" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-3xl space-y-6 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Edit Testimoni</h1>
                <Button variant="outline" as-child>
                    <Link :href="route('testimonials.index')">Kembali</Link>
                </Button>
            </div>

            <form class="space-y-4 rounded-lg border p-4" @submit.prevent="submit">
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-1.5">
                        <Label for="name">Nama</Label>
                        <Input id="name" v-model="form.name" placeholder="Nama pemberi testimoni" />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="space-y-1.5">
                        <Label for="role">Peran/Jabatan</Label>
                        <Input id="role" v-model="form.role" placeholder="Contoh: Petani Padi, Jawa Tengah" />
                        <InputError :message="form.errors.role" />
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <div class="space-y-1.5">
                        <Label for="rating">Rating</Label>
                        <select id="rating" v-model="form.rating" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                            <option :value="5">5 Bintang</option>
                            <option :value="4">4 Bintang</option>
                            <option :value="3">3 Bintang</option>
                            <option :value="2">2 Bintang</option>
                            <option :value="1">1 Bintang</option>
                        </select>
                        <InputError :message="form.errors.rating" />
                    </div>

                    <div class="space-y-1.5">
                        <Label for="avatar_color">Warna Avatar</Label>
                        <select
                            id="avatar_color"
                            v-model="form.avatar_color"
                            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        >
                            <option value="bg-[#2E7D32]">Hijau Tua</option>
                            <option value="bg-blue-600">Biru</option>
                            <option value="bg-orange-600">Oranye</option>
                            <option value="bg-purple-600">Ungu</option>
                            <option value="bg-rose-600">Merah Muda</option>
                        </select>
                        <InputError :message="form.errors.avatar_color" />
                    </div>

                    <div class="space-y-1.5">
                        <Label for="sort_order">Urutan Tampil</Label>
                        <Input id="sort_order" v-model.number="form.sort_order" type="number" min="0" />
                        <InputError :message="form.errors.sort_order" />
                    </div>
                </div>

                <div class="space-y-1.5">
                    <Label for="content">Isi Testimoni</Label>
                    <textarea
                        id="content"
                        v-model="form.content"
                        rows="6"
                        class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        placeholder="Masukkan testimoni pelanggan"
                    />
                    <InputError :message="form.errors.content" />
                </div>

                <label class="flex items-center gap-2 text-sm">
                    <input v-model="form.is_published" type="checkbox" class="rounded border-input" />
                    Tampilkan di landing page
                </label>

                <Button :disabled="form.processing">Update Testimoni</Button>
            </form>
        </div>
    </AppLayout>
</template>
