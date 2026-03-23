<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Link } from '@inertiajs/vue3';
import { Leaf } from 'lucide-vue-next';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="relative flex min-h-svh flex-col items-center justify-center overflow-hidden bg-gradient-to-br from-[#f4fbf5] via-[#e8f5e9] to-[#d5ecd7] p-6 md:p-10">
        <div class="pointer-events-none absolute inset-0 opacity-50" style="background-image: radial-gradient(circle, #2e7d3233 1px, transparent 1px); background-size: 24px 24px;" />

        <div class="relative w-full max-w-sm">
            <div class="flex flex-col gap-8">
                <div class="flex flex-col items-center gap-4">
                    <Link :href="route('home')" class="flex flex-col items-center gap-2 font-medium">
                        <div class="mb-1 flex h-12 w-12 items-center justify-center rounded-xl bg-[#2E7D32] shadow-md shadow-green-900/20">
                            <AppLogoIcon class="size-7 fill-current text-white" />
                        </div>
                        <span class="sr-only">{{ title }}</span>
                    </Link>

                    <div class="inline-flex items-center gap-1.5 rounded-full border border-green-200 bg-white/80 px-3 py-1 text-xs font-semibold text-[#2E7D32] backdrop-blur-sm">
                        <Leaf class="h-3.5 w-3.5" />
                        Petani Cerdas Account
                    </div>

                    <div class="space-y-2 text-center">
                        <h1 class="text-xl font-bold text-gray-900">{{ title }}</h1>
                        <p class="text-center text-sm text-gray-600">{{ description }}</p>
                    </div>
                </div>

                <div class="rounded-2xl border border-green-100 bg-white/95 p-6 shadow-xl shadow-green-900/10 backdrop-blur-sm">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
