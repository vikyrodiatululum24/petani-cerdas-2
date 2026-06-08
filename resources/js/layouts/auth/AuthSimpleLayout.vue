<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Link } from '@inertiajs/vue3';
import { Leaf, MessageCircle, ShieldCheck, Sparkles, Video } from 'lucide-vue-next';

defineProps<{
    title?: string;
    description?: string;
}>();

const authBenefits = [
    {
        title: 'Konsultasi Cepat',
        description: 'Chat langsung bersama mentor berpengalaman saat ada masalah di lahan.',
        icon: MessageCircle,
    },
    {
        title: 'Live Session Terjadwal',
        description: 'Dapatkan pembelajaran praktis melalui sesi live interaktif setiap pekan.',
        icon: Video,
    },
    {
        title: 'Aman dan Terkelola',
        description: 'Data akun dan histori aktivitas tersimpan rapi untuk memudahkan pemantauan.',
        icon: ShieldCheck,
    },
];
</script>

<template>
    <div class="relative flex min-h-svh items-center justify-center overflow-hidden bg-[#f2f7f2] p-4 md:p-8">
        <div
            class="pointer-events-none absolute inset-0 opacity-60"
            style="background-image: radial-gradient(circle at 1px 1px, #2e7d3220 1px, transparent 0); background-size: 24px 24px"
        />
        <div class="pointer-events-none absolute -left-20 top-24 h-64 w-64 rounded-full bg-[#8bc34a33] blur-3xl" />
        <div class="pointer-events-none absolute -right-20 bottom-20 h-72 w-72 rounded-full bg-[#66bb6a30] blur-3xl" />

        <div
            class="relative mx-auto grid w-full max-w-6xl overflow-hidden rounded-3xl border border-green-100 bg-white shadow-2xl shadow-green-900/10 lg:grid-cols-[1.05fr_0.95fr]"
        >
            <aside class="relative hidden overflow-hidden bg-gradient-to-br from-[#1f5a2f] via-[#2E7D32] to-[#4CAF50] p-10 text-white lg:block">
                <div class="absolute -right-8 -top-8 h-36 w-36 rounded-full border border-white/20" />
                <div class="absolute -bottom-12 -left-8 h-40 w-40 rounded-full border border-white/15" />

                <Link :href="route('home')" class="inline-flex items-center gap-3">
                    <AppLogo />
                </Link>

                <div class="mt-10">
                    <p class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-wide">
                        <Sparkles class="h-3.5 w-3.5" />
                        Member Area
                    </p>
                    <h2 class="mt-4 text-3xl font-extrabold leading-tight">Akses Ekosistem Tani Modern dalam Satu Akun</h2>
                    <p class="mt-3 text-sm leading-relaxed text-green-50/90">
                        Masuk untuk melanjutkan konsultasi, melihat jadwal live session, dan mengelola aktivitas membership Anda.
                    </p>
                </div>

                <div class="mt-8 space-y-4">
                    <article
                        v-for="benefit in authBenefits"
                        :key="benefit.title"
                        class="flex items-start gap-3 rounded-xl bg-white/10 p-3 backdrop-blur-sm"
                    >
                        <span class="mt-0.5 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/20">
                            <component :is="benefit.icon" class="h-4 w-4" />
                        </span>
                        <div>
                            <h3 class="text-sm font-semibold">{{ benefit.title }}</h3>
                            <p class="mt-1 text-xs leading-relaxed text-green-50/90">{{ benefit.description }}</p>
                        </div>
                    </article>
                </div>
            </aside>

            <section class="flex items-center justify-center bg-white p-6 md:p-10">
                <div class="w-full max-w-md">
                    <Link :href="route('home')" class="mx-auto mb-5 flex w-fit items-center gap-2 lg:hidden">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#2E7D32] shadow-md shadow-green-900/20">
                            <AppLogoIcon class="size-6 fill-current text-white" />
                        </span>
                    </Link>

                    <p
                        class="mx-auto inline-flex items-center gap-1.5 rounded-full border border-green-200 bg-green-50 px-3 py-1 text-xs font-semibold text-[#2E7D32]"
                    >
                        <Leaf class="h-3.5 w-3.5" />
                        Petani Cerdas Account
                    </p>

                    <div class="mt-4 space-y-1">
                        <h1 class="text-2xl font-bold text-gray-900">{{ title }}</h1>
                        <p class="text-sm text-gray-600">{{ description }}</p>
                    </div>

                    <div class="mt-6 rounded-2xl border border-gray-100 bg-white p-6 shadow-lg shadow-gray-900/5">
                        <slot />
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>
