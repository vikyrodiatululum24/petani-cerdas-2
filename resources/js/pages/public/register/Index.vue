<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    phone: '',
    main_crop: '',
    land_type: '',
    address: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Pendaftaran Customer" />

    <PublicLayout>
        <section class="relative overflow-hidden bg-gradient-to-br from-[#1f5a2f] via-[#2E7D32] to-[#5fae62] py-16 lg:py-24">
            <div
                class="absolute inset-0 opacity-15"
                style="background-image: radial-gradient(circle, #ffffff 1px, transparent 1px); background-size: 26px 26px"
            />
            <div class="relative mx-auto max-w-6xl px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-2 lg:items-center">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.16em] text-green-100">Pendaftaran Customer</p>
                        <h1 class="mt-3 text-3xl font-extrabold leading-tight text-white lg:text-5xl">
                            Daftar Sekarang dan Buat
                            <span class="text-[#D6F5D0]">Akun</span>
                        </h1>
                        <p class="mt-4 text-lg text-green-50">
                            Satu langkah untuk mulai akses coaching 1-on-1, live session, dan konten strategi budidaya di Petani Cerdas.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-white/30 bg-white p-6 shadow-2xl lg:p-8">
                        <h2 class="text-xl font-bold text-gray-900">Form Pendaftaran</h2>
                        <p class="mt-1 text-sm text-gray-500">Lengkapi data berikut untuk membuat akun customer baru.</p>

                        <form @submit.prevent="submit" class="mt-6 flex flex-col gap-4">
                            <div class="grid gap-2">
                                <Label for="phone">No. WhatsApp</Label>
                                <Input
                                    id="phone"
                                    type="text"
                                    required
                                    autofocus
                                    tabindex="1"
                                    autocomplete="tel"
                                    v-model="form.phone"
                                    placeholder="Masukkan nomor WhatsApp aktif"
                                />
                                <InputError :message="form.errors.phone" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="name">Nama Lengkap</Label>
                                <Input
                                    id="name"
                                    type="text"
                                    required
                                    autofocus
                                    tabindex="1"
                                    autocomplete="name"
                                    v-model="form.name"
                                    placeholder="Masukkan nama lengkap"
                                />
                                <InputError :message="form.errors.name" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="email">Email</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    required
                                    tabindex="2"
                                    autocomplete="email"
                                    v-model="form.email"
                                    placeholder="nama@email.com"
                                />
                                <InputError :message="form.errors.email" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="main_crop">Jenis Tanaman</Label>
                                <Input
                                    id="main_crop"
                                    type="text"
                                    tabindex="3"
                                    autocomplete="organization"
                                    v-model="form.main_crop"
                                    placeholder="Masukkan jenis tanaman yang Anda budidayakan (opsional)"
                                />
                                <InputError :message="form.errors.main_crop" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="land_type">Jenis Tanah</Label>
                                <Input
                                    id="land_type"
                                    type="text"
                                    tabindex="3"
                                    autocomplete="organization"
                                    v-model="form.land_type"
                                    placeholder="Masukkan jenis tanah atau media tanam yang Anda gunakan (opsional)"
                                />
                                <InputError :message="form.errors.land_type" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="address">Alamat</Label>
                                <Input
                                    id="address"
                                    type="text"
                                    tabindex="3"
                                    autocomplete="street-address"
                                    v-model="form.address"
                                    placeholder="Masukkan alamat atau lokasi budidaya Anda (opsional)"
                                />
                                <InputError :message="form.errors.address" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="password">Password</Label>
                                <Input
                                    id="password"
                                    type="password"
                                    required
                                    tabindex="3"
                                    autocomplete="new-password"
                                    v-model="form.password"
                                    placeholder="Masukkan password"
                                />
                                <InputError :message="form.errors.password" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="password_confirmation">Konfirmasi Password</Label>
                                <Input
                                    id="password_confirmation"
                                    type="password"
                                    required
                                    tabindex="4"
                                    autocomplete="new-password"
                                    v-model="form.password_confirmation"
                                    placeholder="Ulangi password"
                                />
                                <InputError :message="form.errors.password_confirmation" />
                            </div>

                            <Button
                                type="submit"
                                class="mt-2 w-full bg-[#2E7D32] text-white hover:bg-[#256427]"
                                tabindex="5"
                                :disabled="form.processing"
                            >
                                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                                Daftar
                            </Button>
                        </form>

                        <p class="mt-4 text-center text-sm text-gray-500">
                            Sudah punya akun?
                            <Link :href="route('login')" class="font-semibold text-[#2E7D32] underline underline-offset-4">Masuk di sini</Link>
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
