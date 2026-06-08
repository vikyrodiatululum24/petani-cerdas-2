<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { HeartHandshake, Home, IdCard, Newspaper, ShoppingBag } from 'lucide-vue-next';

const page = usePage();

const navItems = [
    { label: 'Beranda', href: '/', icon: Home },
    { label: 'Layanan', href: '/layanan', icon: HeartHandshake },
    { label: 'Produk', href: '/products', icon: ShoppingBag },
    { label: 'Artikel', href: '/articles', icon: Newspaper },
    { label: 'Tentang Kami', href: '/about', icon: IdCard },
];

const isActive = (href: string) => {
    const url = page.url;
    if (href === '/') return url === '/';
    return url.startsWith(href);
};
</script>

<template>
    <!-- Only visible on mobile -->
    <nav
        class="fixed bottom-0 left-0 right-0 z-50 flex items-stretch border-t border-gray-200 bg-white shadow-[0_-2px_12px_rgba(0,0,0,0.08)] md:hidden"
        style="padding-bottom: env(safe-area-inset-bottom)"
    >
        <Link
            v-for="item in navItems"
            :key="item.href"
            :href="item.href"
            class="flex flex-1 flex-col items-center justify-center gap-0.5 py-2 text-xs transition-colors"
            :class="isActive(item.href) ? 'text-[#2E7D32]' : 'text-gray-400 active:text-[#2E7D32]'"
        >
            <!-- Active indicator -->
            <span
                class="absolute top-0 block h-0.5 w-8 rounded-b-full transition-all"
                :class="isActive(item.href) ? 'bg-[#2E7D32]' : 'bg-transparent'"
            />
            <component :is="item.icon" class="h-5 w-5" :stroke-width="isActive(item.href) ? 2.5 : 1.75" />
            <span class="font-medium" :class="isActive(item.href) ? 'text-[#2E7D32]' : 'text-gray-400'">{{ item.label }}</span>
        </Link>
    </nav>
</template>
