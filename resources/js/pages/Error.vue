<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Head, Link } from '@inertiajs/vue3';
import { AlertTriangle, ArrowLeft, Home } from 'lucide-vue-next';

const props = defineProps<{
    status: number;
}>();

const titleMap: Record<number, string> = {
    403: 'Akses Ditolak',
    404: 'Halaman Tidak Ditemukan',
    500: 'Terjadi Kesalahan Server',
    503: 'Layanan Sementara Tidak Tersedia',
};

const imageMap: Record<number, string> = {
    403: '/assets/403.png',
    404: '/assets/404.png',
    500: '/assets/500.png',
    503: '/assets/503.png',
};

const descriptionMap: Record<number, string> = {
    403: 'Anda tidak memiliki izin untuk membuka halaman ini.',
    404: 'Halaman yang Anda cari mungkin telah dipindahkan atau tidak tersedia.',
    500: 'Kami sedang memperbaiki kendala di server. Silakan coba beberapa saat lagi.',
    503: 'Sistem sedang dalam pemeliharaan. Kami akan segera kembali online.',
};

const title = titleMap[props.status] ?? 'Terjadi Kesalahan';
const description = descriptionMap[props.status] ?? 'Permintaan Anda belum bisa diproses saat ini.';
</script>

<template>
    <Head :title="`${props.status} - ${title}`" />

    <div class="relative flex min-h-svh items-center justify-center overflow-hidden bg-[#f2f7f2] px-4 py-10">
        <div
            class="pointer-events-none absolute inset-0 opacity-60"
            style="background-image: radial-gradient(circle at 1px 1px, #2e7d3220 1px, transparent 0); background-size: 24px 24px"
        />
        <div class="pointer-events-none absolute -left-20 top-20 h-64 w-64 rounded-full bg-[#8bc34a33] blur-3xl" />
        <div class="pointer-events-none absolute -right-20 bottom-20 h-72 w-72 rounded-full bg-[#66bb6a30] blur-3xl" />

        <div class="relative w-full max-w-2xl rounded-3xl border border-green-100 bg-white p-8 text-center shadow-2xl shadow-green-900/10 md:p-10">
            <div class="mx-auto mb-5 inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                <AlertTriangle class="h-7 w-7" />
            </div>

            <p class="text-sm font-semibold uppercase tracking-wide text-[#2E7D32]">Error {{ props.status }}</p>
            <h1 class="mt-2 text-3xl font-extrabold text-gray-900 md:text-4xl">{{ title }}</h1>
            <p class="mx-auto mt-4 max-w-xl text-sm leading-relaxed text-gray-600 md:text-base">{{ description }}</p>
            <img :src="imageMap[props.status]" :alt="title" class="mx-auto mt-6 w-64" />

            <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <Button as-child class="bg-[#2E7D32] text-white hover:bg-[#256427]">
                    <Link href="/">
                        <Home class="mr-2 h-4 w-4" />
                        Kembali ke Beranda
                    </Link>
                </Button>

                <Button variant="outline" as-child>
                    <a href="javascript:history.back()">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Halaman Sebelumnya
                    </a>
                </Button>
            </div>
        </div>
    </div>
</template>
