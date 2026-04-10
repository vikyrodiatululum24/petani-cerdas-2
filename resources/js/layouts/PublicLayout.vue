<script setup lang="ts">
import PublicBottomNav from '@/components/public/PublicBottomNav.vue';
import PublicTopBar from '@/components/public/PublicTopBar.vue';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { Link, usePage } from '@inertiajs/vue3';
import AOS from 'aos';
import { Facebook, Instagram, Leaf, Mail, MapPin, Phone, ShoppingBag } from 'lucide-vue-next';
import { computed, nextTick, onMounted, watch } from 'vue';

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
        tiktok_url: string | null;
        tiktok_shop_url: string | null;
    };
};

const page = usePage<PublicLayoutProps>();
const appSetting = computed(() => page.props.appSetting ?? null);

const sectionAnimations = ['fade-up', 'fade-right', 'fade-left'];
const itemAnimations = ['fade-up', 'fade-left', 'fade-right'];

const setAnimationIfMissing = (element: Element, animation: string, delay = 0, duration = 800) => {
    if (!element.hasAttribute('data-aos')) {
        element.setAttribute('data-aos', animation);
    }

    if (!element.hasAttribute('data-aos-duration')) {
        element.setAttribute('data-aos-duration', String(duration));
    }

    if (!element.hasAttribute('data-aos-delay')) {
        element.setAttribute('data-aos-delay', String(delay));
    }

    if (!element.hasAttribute('data-aos-once')) {
        element.setAttribute('data-aos-once', 'true');
    }
};

const annotateSectionChildren = (section: Element) => {
    const itemTargets = Array.from(section.querySelectorAll('.grid > article, .grid > a, .swiper-slide, [data-aos-item]')).slice(0, 10);

    itemTargets.forEach((element, index) => {
        setAnimationIfMissing(element, itemAnimations[index % itemAnimations.length], 60 * (index % 6), 720);
    });
};

const annotatePublicSections = () => {
    const mainElement = document.querySelector('main');
    if (!mainElement) return;

    const sectionTargets = Array.from(mainElement.querySelectorAll(':scope > section, :scope > [data-aos-section]')).filter(
        (element) => element.childElementCount > 0,
    );

    if (sectionTargets.length === 0) {
        const pageRoot = mainElement.firstElementChild;
        if (pageRoot && pageRoot.childElementCount > 0) {
            setAnimationIfMissing(pageRoot, 'fade-up', 0, 780);
            annotateSectionChildren(pageRoot);
        }

        return;
    }

    sectionTargets.forEach((section, index) => {
        setAnimationIfMissing(section, sectionAnimations[index % sectionAnimations.length], 0, 780);

        if (!section.hasAttribute('data-aos-offset')) {
            section.setAttribute('data-aos-offset', '90');
        }

        annotateSectionChildren(section);
    });
};

const refreshPublicAnimations = async () => {
    await nextTick();
    annotatePublicSections();
    AOS.refreshHard();
};

onMounted(async () => {
    AOS.init({
        once: true,
        duration: 800,
        easing: 'ease-out-cubic',
        offset: 90,
    });

    await refreshPublicAnimations();
});

watch(
    () => page.url,
    async () => {
        await refreshPublicAnimations();
    },
);

const footerLinks = {
    fitur: [
        { label: 'Konsultasi', href: '/layanan#konsultasi' },
        { label: 'Live Konsultasi', href: '/layanan#coaching' },
        { label: 'Artikel Pertanian', href: '/articles' },
        { label: 'Toko Produk', href: '/products' },
    ],
    perusahaan: [
        { label: 'Tentang Kami', href: '/about' },
        { label: 'Artikel', href: '/articles' },
        { label: 'Layanan', href: '/layanan' },
    ],
    legal: [
        { label: 'Kebijakan Privasi', href: '/privacy' },
        { label: 'Syarat & Ketentuan', href: '/terms' },
    ],
};
</script>

<template>
    <div class="flex min-h-screen flex-col overflow-x-hidden bg-white">
        <!-- Top navigation bar -->
        <PublicTopBar />

        <!-- Page content — padded top to clear fixed topbar, padded bottom on mobile to clear bottom nav -->
        <main class="flex-1 overflow-x-hidden pb-20 pt-16 md:pb-0">
            <slot />
        </main>

        <!-- Footer — hidden on mobile behind bottom nav, so we add extra spacing -->
        <footer class="bg-[#1a2e1b] text-gray-300">
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
                            <TooltipProvider>
                                <Tooltip>
                                    <TooltipTrigger as-child>
                                        <a
                                            :href="appSetting?.instagram_url || '#'"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            aria-label="Instagram"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 transition-colors hover:bg-[#4CAF50]"
                                        >
                                            <Instagram class="h-4 w-4" />
                                        </a>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>Instagram</p>
                                    </TooltipContent>
                                </Tooltip>
                            </TooltipProvider>
                            <TooltipProvider>
                                <Tooltip>
                                    <TooltipTrigger as-child>
                                        <a
                                            :href="appSetting?.facebook_url || '#'"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            aria-label="Facebook"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 transition-colors hover:bg-[#4CAF50]"
                                        >
                                            <Facebook class="h-4 w-4" />
                                        </a>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>Facebook</p>
                                    </TooltipContent>
                                </Tooltip>
                            </TooltipProvider>

                            <TooltipProvider>
                                <Tooltip>
                                    <TooltipTrigger as-child>
                                        <a
                                            :href="appSetting?.tiktok_url || '#'"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            aria-label="TikTok"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 transition-colors hover:bg-[#4CAF50]"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 256 256"
                                                width="100"
                                                height="100"
                                                class="h-4 w-4"
                                                fill="currentColor"
                                            >
                                                <path
                                                    d="M208 80.6a72.3 72.3 0 0 1-42.4-13.7V144a64 64 0 1 1-64-64 62.6 62.6 0 0 1 16 .2v33.3a32 32 0 1 0 21.6 30.5V0h26.4a72.5 72.5 0 0 0 42.4 42.4Z"
                                                />
                                            </svg>
                                        </a>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>TikTok</p>
                                    </TooltipContent>
                                </Tooltip>
                            </TooltipProvider>
                            <TooltipProvider>
                                <Tooltip>
                                    <TooltipTrigger as-child>
                                        <a
                                            :href="appSetting?.tiktok_shop_url || '#'"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            aria-label="TikTok Shop"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 transition-colors hover:bg-[#4CAF50]"
                                        >
                                            <ShoppingBag class="h-4 w-4" />
                                        </a>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>TikTok Shop</p>
                                    </TooltipContent>
                                </Tooltip>
                            </TooltipProvider>
                            <TooltipProvider>
                                <Tooltip>
                                    <TooltipTrigger as-child>
                                        <a
                                            :href="appSetting?.youtube_url || '#'"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            aria-label="YouTube"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 transition-colors hover:bg-[#4CAF50]"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                                <path
                                                    d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"
                                                />
                                            </svg>
                                        </a>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>YouTube</p>
                                    </TooltipContent>
                                </Tooltip>
                            </TooltipProvider>
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
                        <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-white">Petani Cerdas Official</h3>
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
                                {{
                                    appSetting?.contact_address ||
                                    'Desa Gunung Malang, Kecamatan Cikidang, Kabupaten Sukabumi, Provinsi Jawa barat, Kode Pos 43367'
                                }}
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
