<script setup lang="ts">
import PublicBottomNav from '@/components/public/PublicBottomNav.vue';
import PublicTopBar from '@/components/public/PublicTopBar.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Facebook, Instagram, Leaf, Mail, MapPin, Phone, Youtube } from 'lucide-vue-next';
import { computed } from 'vue';

type PublicLayoutProps = {
    appSetting?: {
        app_name: string;
        brand_name: string;
        app_description: string;
        logo_url: string | null;
        contact_email: string | null;
        contact_phone: string | null;
        contact_address: string | null;
        instagram_url: string | null;
        youtube_url: string | null;
        facebook_url: string | null;
    };
};

const page = usePage<PublicLayoutProps>();
const appSetting = computed(() => page.props.appSetting ?? null);

const footerLinks = {
    fitur: [
        { label: 'Konsultasi', href: '/layanan#konsultasi' },
        { label: 'Coaching 1-on-1', href: '/layanan#coaching' },
        { label: 'Artikel Pertanian', href: '/articles' },
        { label: 'Toko Produk', href: '/products' },
    ],
    perusahaan: [
        { label: 'Tentang Kami', href: '/about' },
        { label: 'Blog', href: '/articles' },
        { label: 'Karir', href: '/careers' },
        { label: 'Kontak', href: '/contact' },
    ],
    legal: [
        { label: 'Kebijakan Privasi', href: '/privacy' },
        { label: 'Syarat & Ketentuan', href: '/terms' },
    ],
};
</script>

<template>
    <div class="flex min-h-screen flex-col bg-white">
        <!-- Top navigation bar -->
        <PublicTopBar />

        <!-- Page content — padded top to clear fixed topbar, padded bottom on mobile to clear bottom nav -->
        <main class="flex-1 pb-20 pt-16 md:pb-0">
            <slot />
        </main>

        <!-- Footer — hidden on mobile behind bottom nav, so we add extra spacing -->
        <footer class="hidden bg-[#1a2e1b] text-gray-300 md:block">
            <div class="mx-auto max-w-7xl px-6 py-14 lg:px-8">
                <div class="grid grid-cols-1 gap-10 md:grid-cols-4">
                    <!-- Brand column -->
                    <div class="col-span-1">
                        <Link href="/" class="mb-4 flex items-center gap-2">
                            <img v-if="appSetting?.logo_url" :src="appSetting.logo_url" alt="Logo" class="h-10 w-10 object-contain" />
                            <div v-else class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#2E7D32]">
                                <Leaf class="h-4 w-4 text-white" />
                            </div>
                            <h1 class="text-lg font-bold text-white">Petani<span class="text-[#4CAF50]">Cerdas</span></h1>
                            <!-- <span class="text-lg font-bold text-white">{{ appSetting?.brand_name || 'PetaniCerdas' }}</span> -->
                        </Link>
                        <p class="text-sm leading-relaxed text-gray-400">
                            {{
                                appSetting?.app_description ||
                                'Platform agritech digital yang menghubungkan petani dengan teknologi modern, pengetahuan ahli, dan komunitas pertanian Indonesia.'
                            }}
                        </p>
                        <div class="mt-5 flex gap-3">
                            <a
                                :href="appSetting?.instagram_url || '#'"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="Instagram"
                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 transition-colors hover:bg-[#4CAF50]"
                            >
                                <Instagram class="h-4 w-4" />
                            </a>
                            <a
                                :href="appSetting?.youtube_url || '#'"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="YouTube"
                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 transition-colors hover:bg-[#4CAF50]"
                            >
                                <Youtube class="h-4 w-4" />
                            </a>
                            <a
                                :href="appSetting?.facebook_url || '#'"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="Facebook"
                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 transition-colors hover:bg-[#4CAF50]"
                            >
                                <Facebook class="h-4 w-4" />
                            </a>
                        </div>
                    </div>

                    <!-- Fitur -->
                    <div>
                        <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-white">Fitur</h3>
                        <ul class="space-y-2.5">
                            <li v-for="link in footerLinks.fitur" :key="link.href">
                                <Link :href="link.href" class="text-sm text-gray-400 transition-colors hover:text-[#4CAF50]">
                                    {{ link.label }}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Perusahaan -->
                    <div>
                        <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-white">Perusahaan</h3>
                        <ul class="space-y-2.5">
                            <li v-for="link in footerLinks.perusahaan" :key="link.href">
                                <Link :href="link.href" class="text-sm text-gray-400 transition-colors hover:text-[#4CAF50]">
                                    {{ link.label }}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Kontak -->
                    <div>
                        <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-white">Kontak</h3>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-2.5 text-sm text-gray-400">
                                <MapPin class="mt-0.5 h-4 w-4 shrink-0 text-[#4CAF50]" />
                                {{ appSetting?.contact_address || 'Jl. Raya Pertanian No. 1, Jakarta, Indonesia' }}
                            </li>
                            <li class="flex items-center gap-2.5 text-sm text-gray-400">
                                <Mail class="h-4 w-4 shrink-0 text-[#4CAF50]" />
                                {{ appSetting?.contact_email || 'hello@petanicerdas.id' }}
                            </li>
                            <li class="flex items-center gap-2.5 text-sm text-gray-400">
                                <Phone class="h-4 w-4 shrink-0 text-[#4CAF50]" />
                                {{ appSetting?.contact_phone || '+62 812 3456 7890' }}
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Bottom bar -->
                <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-white/10 pt-6 sm:flex-row">
                    <p class="text-xs text-gray-500">
                        &copy; {{ new Date().getFullYear() }} {{ appSetting?.brand_name || 'PetaniCerdas' }}. Hak cipta dilindungi.
                    </p>
                    <div class="flex gap-4">
                        <Link
                            v-for="link in footerLinks.legal"
                            :key="link.href"
                            :href="link.href"
                            class="text-xs text-gray-500 transition-colors hover:text-[#4CAF50]"
                        >
                            {{ link.label }}
                        </Link>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Mobile bottom navigation -->
        <PublicBottomNav />
    </div>
</template>
