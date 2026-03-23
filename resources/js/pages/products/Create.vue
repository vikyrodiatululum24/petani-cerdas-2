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
        title: 'Products',
        href: '/dashboard/products',
    },
    {
        title: 'Create',
        href: '/dashboard/products/create',
    },
];

const form = useForm({
    name: '',
    description: '',
    price: 0,
    discount_price: 0 as number,
    image_path: null as File | null,
    olshop_links: {
        shopee: '',
        tokopedia: '',
        lazada: '',
        tiktok_shop: '',
        blibli: '',
        lainnya: '',
    },
    category_id: props.categories[0]?.id ?? null,
});

const submit = () => {
    form.post(route('products.store'), {
        forceFormData: true,
    });
};

const onImageChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    form.image_path = target.files?.[0] ?? null;
};

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(price);
};
</script>

<template>
    <Head title="Create Product" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-3xl space-y-6 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Buat Produk</h1>
                <Button variant="outline" as-child>
                    <Link :href="route('products.index')">Kembali</Link>
                </Button>
            </div>

            <form class="space-y-4 rounded-lg border p-4" @submit.prevent="submit">
                <div class="space-y-1.5">
                    <Label for="name">Nama Produk</Label>
                    <Input id="name" v-model="form.name" placeholder="Nama produk" />
                    <InputError :message="form.errors.name" />
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
                    <Label for="price">Harga</Label>
                    <Input
                        id="price"
                        v-model.number="form.price"
                        type="number"
                        inputmode="numeric"
                        placeholder="0"
                    />
                    <p class="text-xs text-muted-foreground">{{ form.price ? formatPrice(form.price) : '-' }}</p>
                    <InputError :message="form.errors.price" />
                </div>

                <div class="space-y-1.5">
                    <Label for="discount-price">Harga Diskon (opsional)</Label>
                    <Input
                        id="discount-price"
                        v-model.number="form.discount_price"
                        type="number"
                        inputmode="numeric"
                        placeholder="0"
                    />
                    <p class="text-xs text-muted-foreground">{{ form.discount_price ? formatPrice(form.discount_price) : '-' }}</p>
                    <InputError :message="form.errors.discount_price" />
                </div>

                <div class="space-y-1.5">
                    <Label for="image">Gambar Produk</Label>
                    <input
                        id="image"
                        type="file"
                        accept="image/png,image/jpeg,image/jpg,image/webp"
                        class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        @change="onImageChange"
                    />
                    <InputError :message="form.errors.image_path" />
                </div>

                <div class="space-y-1.5">
                    <Label for="description">Deskripsi</Label>
                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="10"
                        class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        placeholder="Tulis deskripsi produk di sini"
                    />
                    <InputError :message="form.errors.description" />
                </div>

                <div class="space-y-3 rounded-md border p-3">
                    <p class="text-sm font-medium">Link Online Shop (opsional)</p>

                    <div class="space-y-1.5">
                        <Label for="olshop-shopee">Shopee</Label>
                        <Input id="olshop-shopee" v-model="form.olshop_links.shopee" placeholder="https://shopee.co.id/..." />
                    </div>

                    <div class="space-y-1.5">
                        <Label for="olshop-tokopedia">Tokopedia</Label>
                        <Input id="olshop-tokopedia" v-model="form.olshop_links.tokopedia" placeholder="https://www.tokopedia.com/..." />
                    </div>

                    <div class="space-y-1.5">
                        <Label for="olshop-lazada">Lazada</Label>
                        <Input id="olshop-lazada" v-model="form.olshop_links.lazada" placeholder="https://www.lazada.co.id/..." />
                    </div>

                    <div class="space-y-1.5">
                        <Label for="olshop-tiktok">TikTok Shop</Label>
                        <Input id="olshop-tiktok" v-model="form.olshop_links.tiktok_shop" placeholder="https://www.tiktok.com/..." />
                    </div>

                    <div class="space-y-1.5">
                        <Label for="olshop-blibli">Blibli</Label>
                        <Input id="olshop-blibli" v-model="form.olshop_links.blibli" placeholder="https://www.blibli.com/..." />
                    </div>

                    <div class="space-y-1.5">
                        <Label for="olshop-other">Lainnya</Label>
                        <Input id="olshop-other" v-model="form.olshop_links.lainnya" placeholder="https://..." />
                    </div>

                    <InputError :message="form.errors.olshop_links" />
                </div>

                <Button :disabled="form.processing">Simpan Produk</Button>
            </form>
        </div>
    </AppLayout>
</template>
