<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Subscription Plans',
        href: '/subscription-plans',
    },
    {
        title: 'Create',
        href: '/subscription-plans/create',
    },
];

const form = useForm({
    name: '',
    jenis: '',
    price: 0,
    discount_price: '' as number | string,
    quota: '' as number | string,
    duration_months: 1,
    description: '',
    features_text: '',
    is_featured: false,
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        discount_price: data.discount_price === '' ? null : Number(data.discount_price),
        quota: data.quota === '' ? null : Number(data.quota),
        features: data.features_text
            .split('\n')
            .map((line) => line.trim())
            .filter((line) => line.length > 0),
    })).post(route('subscription-plans.store'));
};

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(price || 0);
};
</script>

<template>
    <Head title="Create Subscription Plan" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-3xl space-y-6 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Buat Paket Langganan</h1>
                <Button variant="outline" as-child>
                    <Link :href="route('subscription-plans.index')">Kembali</Link>
                </Button>
            </div>

            <form class="space-y-4 rounded-lg border p-4" @submit.prevent="submit">
                <div class="space-y-1.5">
                    <Label for="name">Nama Paket</Label>
                    <Input id="name" v-model="form.name" placeholder="Paket Premium" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="space-y-1.5">
                    <Label for="jenis">Jenis Paket</Label>
                    <select id="jenis" v-model="form.jenis" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                        <option value="">Pilih Jenis Paket</option>
                        <option value="basic">Basic</option>
                        <option value="premium">Premium</option>
                        <option value="elite">Elite</option>
                    </select>
                    <InputError :message="form.errors.jenis" />
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-1.5">
                        <Label for="price">Harga</Label>
                        <Input id="price" v-model.number="form.price" type="number" inputmode="numeric" min="0" placeholder="0" />
                        <p class="text-xs text-muted-foreground">{{ formatPrice(form.price) }}</p>
                        <InputError :message="form.errors.price" />
                    </div>

                    <div class="space-y-1.5">
                        <Label for="discount-price">Harga Diskon (opsional)</Label>
                        <Input id="discount-price" v-model.number="form.discount_price" type="number" inputmode="numeric" min="0" placeholder="0" />
                        <InputError :message="form.errors.discount_price" />
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-1.5">
                        <Label for="duration">Durasi (bulan)</Label>
                        <Input id="duration" v-model.number="form.duration_months" type="number" min="1" inputmode="numeric" />
                        <InputError :message="form.errors.duration_months" />
                    </div>

                    <div class="space-y-1.5">
                        <Label for="quota">Kuota (opsional)</Label>
                        <Input
                            id="quota"
                            v-model.number="form.quota"
                            type="number"
                            min="0"
                            inputmode="numeric"
                            placeholder="Kosongkan untuk unlimited"
                        />
                        <InputError :message="form.errors.quota" />
                    </div>
                </div>

                <div class="space-y-1.5">
                    <Label for="features">Fitur (satu baris satu fitur)</Label>
                    <textarea
                        id="features"
                        v-model="form.features_text"
                        rows="5"
                        class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        placeholder="Akses semua materi\nPrioritas support\nLaporan mingguan"
                    />
                    <InputError :message="form.errors.features_text" />
                </div>

                <div class="space-y-1.5">
                    <Label for="description">Deskripsi (opsional)</Label>
                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="5"
                        class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        placeholder="Deskripsi paket langganan"
                    />
                    <InputError :message="form.errors.description" />
                </div>

                <label class="flex items-center gap-2 text-sm">
                    <input v-model="form.is_featured" type="checkbox" class="rounded border-input" />
                    Tandai sebagai paket unggulan
                </label>

                <Button :disabled="form.processing">Simpan Paket</Button>
            </form>
        </div>
    </AppLayout>
</template>
