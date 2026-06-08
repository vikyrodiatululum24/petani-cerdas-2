<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { UserRound } from 'lucide-vue-next';
import { ref } from 'vue';

interface FormData {
    name: string;
    email: string;
    photo_url: string | null;
    phone: string | null;
    main_crop: string | null;
    land_type: string | null;
    province: string | null;
    city: string | null;
    district: string | null;
    village: string | null;
    postal_code: string | null;
    address: string | null;
}

const props = defineProps<{
    formData: FormData;
}>();

const form = useForm({
    name: props.formData.name ?? '',
    email: props.formData.email ?? '',
    photo: null as File | null,
    phone: props.formData.phone ?? '',
    main_crop: props.formData.main_crop ?? '',
    land_type: props.formData.land_type ?? '',
    province: props.formData.province ?? '',
    city: props.formData.city ?? '',
    district: props.formData.district ?? '',
    village: props.formData.village ?? '',
    postal_code: props.formData.postal_code ?? '',
    address: props.formData.address ?? '',
});

const photoPreviewUrl = ref<string | null>(props.formData.photo_url ?? null);

const onPhotoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0] ?? null;

    form.photo = file;
    photoPreviewUrl.value = file ? URL.createObjectURL(file) : (props.formData.photo_url ?? null);
};

const submit = () => {
    form.transform((data) => ({
        ...data,
        _method: 'put',
    })).post(route('petani.profile.update'), {
        forceFormData: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Edit Profil Petani" />

    <PublicLayout>
        <section class="bg-gradient-to-br from-[#1b5e20] via-[#2E7D32] to-[#66BB6A] py-12 text-white">
            <div class="mx-auto max-w-4xl px-6 lg:px-8">
                <p class="text-xs uppercase tracking-[0.18em] text-green-100">Profil Petani</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight">Edit Profil</h1>
                <p class="mt-2 text-sm text-green-50">Perbarui data akun dan data lahan Anda dari halaman publik Petani Cerdas.</p>
            </div>
        </section>

        <section class="bg-[#f4f8f3] py-8">
            <div class="mx-auto max-w-4xl px-6 lg:px-8">
                <div class="mb-4">
                    <Button as-child variant="outline">
                        <Link :href="route('petani.profile.index')">Kembali ke Profil</Link>
                    </Button>
                </div>

                <form @submit.prevent="submit" class="space-y-6 rounded-2xl border border-gray-200 bg-white p-6 lg:p-8">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Data Akun</h2>
                    </div>

                    <div>
                        <Label for="photo">Foto Profil</Label>
                        <div class="mt-2 flex items-center gap-4">
                            <div class="h-16 w-16 overflow-hidden rounded-full border border-gray-200 bg-gray-50">
                                <img v-if="photoPreviewUrl" :src="photoPreviewUrl" alt="Preview foto profil" class="h-full w-full object-cover" />
                                <div v-else class="flex h-full w-full items-center justify-center text-gray-400">
                                    <UserRound class="h-7 w-7" />
                                </div>
                            </div>
                            <div class="w-full">
                                <input
                                    id="photo"
                                    type="file"
                                    accept="image/jpeg,image/png,image/webp"
                                    class="block w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                    @change="onPhotoChange"
                                />
                                <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, WEBP. Maks 4MB.</p>
                            </div>
                        </div>
                        <InputError :message="form.errors.photo" class="mt-1" />
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <Label for="name">Nama</Label>
                            <Input id="name" v-model="form.name" placeholder="Nama lengkap" />
                            <InputError :message="form.errors.name" class="mt-1" />
                        </div>
                        <div>
                            <Label for="email">Email</Label>
                            <Input id="email" v-model="form.email" type="email" placeholder="email@contoh.com" />
                            <InputError :message="form.errors.email" class="mt-1" />
                        </div>
                    </div>

                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Data Pribadi dan Lahan</h2>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <Label for="phone">No. Telepon</Label>
                            <Input id="phone" v-model="form.phone" placeholder="08xxxx" />
                            <InputError :message="form.errors.phone" class="mt-1" />
                        </div>
                        <div>
                            <Label for="main_crop">Komoditas Utama</Label>
                            <Input id="main_crop" v-model="form.main_crop" placeholder="Padi, Cabai, Jagung..." />
                            <InputError :message="form.errors.main_crop" class="mt-1" />
                        </div>
                        <div>
                            <Label for="land_type">Jenis Lahan</Label>
                            <Input id="land_type" v-model="form.land_type" placeholder="Sawah, Kebun, Hidroponik..." />
                            <InputError :message="form.errors.land_type" class="mt-1" />
                        </div>
                        <div>
                            <Label for="province">Provinsi</Label>
                            <Input id="province" v-model="form.province" />
                            <InputError :message="form.errors.province" class="mt-1" />
                        </div>
                        <div>
                            <Label for="city">Kota</Label>
                            <Input id="city" v-model="form.city" />
                            <InputError :message="form.errors.city" class="mt-1" />
                        </div>
                        <div>
                            <Label for="district">Kecamatan</Label>
                            <Input id="district" v-model="form.district" />
                            <InputError :message="form.errors.district" class="mt-1" />
                        </div>
                        <div>
                            <Label for="village">Desa</Label>
                            <Input id="village" v-model="form.village" />
                            <InputError :message="form.errors.village" class="mt-1" />
                        </div>
                        <div>
                            <Label for="postal_code">Kode Pos</Label>
                            <Input id="postal_code" v-model="form.postal_code" />
                            <InputError :message="form.errors.postal_code" class="mt-1" />
                        </div>
                    </div>

                    <div>
                        <Label for="address">Alamat</Label>
                        <textarea
                            id="address"
                            v-model="form.address"
                            rows="3"
                            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                            placeholder="Alamat lengkap"
                        />
                        <InputError :message="form.errors.address" class="mt-1" />
                    </div>

                    <div class="flex justify-end gap-2">
                        <Button type="button" variant="outline" as-child>
                            <Link :href="route('petani.profile.index')">Batal</Link>
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                        </Button>
                    </div>
                </form>
            </div>
        </section>
    </PublicLayout>
</template>
