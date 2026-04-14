<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Petani Cerdas - Not Authorized" />

    <PublicLayout>
        <div class="relative min-h-screen">
            <div class="absolute z-10 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 flex flex-col items-center justify-center gap-4">
                <h1 class="mb-4 text-4xl font-bold text-[#2E7D32]">403</h1>
                <h2 class="mb-6 text-2xl font-semibold text-gray-800">Akses Ditolak</h2>
                <img src="/assets/403.png" alt="403 Not Authorized" class="mb-6 w-64">
                <p class="mb-6 text-lg text-gray-600 text-center px-4 min-w-96 ">Anda tidak memiliki izin untuk mengakses halaman <span class="font-semibold">Artikel</span>. Silakan login atau daftar terlebih dahulu untuk mendapatkan akses.</p>
                <div class="flex items-center justify-center gap-2">
                    <Button as-child class="bg-[#2E7D32] text-white hover:bg-[#256427]">
                        <Link href="/register" class="inline-flex items-center gap-2">
                            Daftar Gratis
                        </Link>
                    </Button>
                    <Button as-child variant="outline" class="text-[#2E7D32] border-[#2E7D32] hover:bg-green-50 hover:text-[#256427]">
                        <Link href="/login" class="inline-flex items-center gap-2">
                            Masuk
                        </Link>
                    </Button>
                </div>
            </div>
            <img src="/assets/FooterError.png" alt="Footer Error" class="absolute bottom-0 left-0 w-full z-0">
        </div>
    </PublicLayout>
</template>
