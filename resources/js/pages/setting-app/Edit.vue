<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

interface SettingPayload {
    app_name: string;
    brand_name: string;
    app_description: string | null;
    logo_url: string | null;
    contact_email: string | null;
    contact_phone: string | null;
    contact_address: string | null;
    instagram_url: string | null;
    youtube_url: string | null;
    facebook_url: string | null;
    tiktok_url: string | null;
    tiktok_shop_url: string | null;
    payment_destination_accounts: string;
}

const props = defineProps<{
    setting: SettingPayload;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Setting App',
        href: '/setting-app',
    },
];

const form = useForm({
    app_name: props.setting.app_name ?? '',
    brand_name: props.setting.brand_name ?? '',
    app_description: props.setting.app_description ?? '',
    logo: null as File | null,
    contact_email: props.setting.contact_email ?? '',
    contact_phone: props.setting.contact_phone ?? '',
    contact_address: props.setting.contact_address ?? '',
    instagram_url: props.setting.instagram_url ?? '',
    youtube_url: props.setting.youtube_url ?? '',
    facebook_url: props.setting.facebook_url ?? '',
    tiktok_url: props.setting.tiktok_url ?? '',
    tiktok_shop_url: props.setting.tiktok_shop_url ?? '',
    payment_destination_accounts: props.setting.payment_destination_accounts ?? '',
});

const onLogoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    form.logo = target.files?.[0] ?? null;
};

const submit = () => {
    form.transform((data) => ({
        ...data,
        _method: 'put',
    })).post(route('setting-app.update'), {
        forceFormData: true,
        onSuccess () {
            toast.success('Setting aplikasi berhasil diperbarui');
        },
    });

};
</script>

<template>
    <Head title="Setting App" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-4xl space-y-6 p-4 w-full">
            <div>
                <h1 class="text-2xl font-semibold">Setting Aplikasi</h1>
                <p class="text-sm text-muted-foreground">Kelola logo, brand, deskripsi, kontak, social media, dan rekening pembayaran aplikasi.</p>
            </div>

            <form @submit.prevent="submit" class="space-y-6 rounded-xl border bg-white p-6">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <Label for="app_name">Nama Aplikasi</Label>
                        <Input id="app_name" v-model="form.app_name" placeholder="Petani Cerdas" />
                        <InputError :message="form.errors.app_name" class="mt-1" />
                    </div>
                    <div>
                        <Label for="brand_name">Nama Brand</Label>
                        <Input id="brand_name" v-model="form.brand_name" placeholder="PetaniCerdas" />
                        <InputError :message="form.errors.brand_name" class="mt-1" />
                    </div>
                </div>

                <div>
                    <Label for="app_description">Deskripsi</Label>
                    <Textarea id="app_description" v-model="form.app_description" placeholder="Deskripsi singkat aplikasi" />
                    <InputError :message="form.errors.app_description" class="mt-1" />
                </div>

                <div>
                    <Label for="logo">Logo Aplikasi</Label>
                    <input id="logo" type="file" accept="image/*" class="block w-full rounded-md border border-input bg-background px-3 py-2 text-sm" @change="onLogoChange" />
                    <InputError :message="form.errors.logo" class="mt-1" />
                    <div v-if="setting.logo_url" class="mt-3">
                        <p class="mb-1 text-xs text-muted-foreground">Logo saat ini:</p>
                        <img :src="setting.logo_url" alt="Logo aplikasi" class="h-12 w-12 rounded-md border object-contain" />
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <Label for="contact_email">Email Kontak</Label>
                        <Input id="contact_email" v-model="form.contact_email" type="email" placeholder="hello@petanicerdas.id" />
                        <InputError :message="form.errors.contact_email" class="mt-1" />
                    </div>
                    <div>
                        <Label for="contact_phone">No. Telepon</Label>
                        <Input id="contact_phone" v-model="form.contact_phone" placeholder="+62 ..." />
                        <InputError :message="form.errors.contact_phone" class="mt-1" />
                    </div>
                    <div>
                        <Label for="contact_address">Alamat</Label>
                        <Textarea id="contact_address" v-model="form.contact_address" placeholder="Kota, Provinsi" />
                        <InputError :message="form.errors.contact_address" class="mt-1" />
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex w-full items-center gap-4">
                        <Label for="instagram_url" class="w-36">Instagram URL</Label>
                        <div class="flex-1">
                            <Input id="instagram_url" v-model="form.instagram_url" placeholder="https://instagram.com/..." />
                            <InputError :message="form.errors.instagram_url" class="mt-1" />
                        </div>
                    </div>
                    <div class="flex w-full items-center gap-4">
                        <Label for="tiktok_url" class="w-36">TikTok URL</Label>
                        <div class="flex-1">
                            <Input id="tiktok_url" v-model="form.tiktok_url" placeholder="https://tiktok.com/..." />
                            <InputError :message="form.errors.tiktok_url" class="mt-1" />
                        </div>
                    </div>
                    <div class="flex w-full items-center gap-4">
                        <Label for="tiktok_shop_url" class="w-36">TikTok Shop URL</Label>
                        <div class="flex-1">
                            <Input id="tiktok_shop_url" v-model="form.tiktok_shop_url" placeholder="https://shop-id.tokopedia.com/..." />
                            <InputError :message="form.errors.tiktok_shop_url" class="mt-1" />
                        </div>
                    </div>
                    <div class="flex w-full items-center gap-4">
                        <Label for="youtube_url" class="w-36">YouTube URL</Label>
                        <div class="flex-1">
                            <Input id="youtube_url" v-model="form.youtube_url" placeholder="https://youtube.com/..." />
                            <InputError :message="form.errors.youtube_url" class="mt-1" />
                        </div>
                    </div>
                    <div class="flex w-full items-center gap-4">
                        <Label for="facebook_url" class="w-36">Facebook URL</Label>
                        <div class="flex-1">
                            <Input id="facebook_url" v-model="form.facebook_url" placeholder="https://facebook.com/..." />
                            <InputError :message="form.errors.facebook_url" class="mt-1" />
                        </div>
                    </div>
                </div>

                <div>
                    <Label for="payment_destination_accounts">Rekening Tujuan Pembayaran</Label>
                    <Textarea
                        id="payment_destination_accounts"
                        v-model="form.payment_destination_accounts"
                        placeholder="BCA|1234567890|PT Petani Cerdas Indonesia"
                        rows="6"
                    />
                    <p class="mt-1 text-xs text-muted-foreground">Satu rekening per baris dengan format: Nama Bank|Nomor Rekening|Nama Pemilik.</p>
                    <InputError :message="form.errors.payment_destination_accounts" class="mt-1" />
                </div>

                <div class="flex justify-end">
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Setting' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
