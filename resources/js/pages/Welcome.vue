<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
    MessageSquare,
    CalendarCheck,
    Newspaper,
    ShoppingBag,
    CheckCircle2,
    ArrowRight,
    Star,
    Users,
    Leaf,
    TrendingUp,
    ShieldCheck,
    Zap,
} from 'lucide-vue-next';

type StatIconKey = 'users' | 'newspaper' | 'shield-check' | 'trending-up';

interface WelcomeStat {
    value: string;
    label: string;
    icon: StatIconKey;
}

interface WelcomeArticle {
    category: string;
    title: string;
    excerpt: string;
    date: string;
    readTime: string;
    href: string;
}

interface WelcomeProduct {
    name: string;
    price: string;
    category: string;
    rating: number;
    sold: string;
}

interface WelcomePlan {
    name: string;
    originalPrice: string | null;
    price: string;
    priceNote: string;
    quota: string;
    available: boolean;
    highlight: boolean;
    features: string[];
    cta: string;
    ctaHref: string;
}

interface WelcomeAppSetting {
    app_name: string;
    brand_name: string;
    app_description: string;
    logo_url: string | null;
}

const props = withDefaults(defineProps<{
    canRegister: boolean;
    appSetting?: WelcomeAppSetting;
    stats?: WelcomeStat[];
    latestArticles?: WelcomeArticle[];
    products?: WelcomeProduct[];
    membershipPlans?: WelcomePlan[];
}>(), { canRegister: true });

console.log('Welcome props:', props.membershipPlans);

const features = [
    {
        icon: MessageSquare,
        color: 'bg-emerald-50 text-[#2E7D32]',
        title: 'Konsultasi Realtime',
        desc: 'Chat langsung dengan petani ahli & mentor berpengalaman. Kirim teks, foto, dan video kapan saja.',
        badgeClass: 'bg-amber-50 text-amber-700 border border-amber-200',
        href: '/layanan#konsultasi',
    },
    {
        icon: CalendarCheck,
        color: 'bg-blue-50 text-blue-600',
        title: 'Coaching 1-on-1',
        desc: 'Booking sesi coaching pribadi dengan jadwal fleksibel. Sesi via Google Meet atau Zoom.',
        badgeClass: 'bg-amber-50 text-amber-700 border border-amber-200',
        href: '/layanan#coaching',
    },
    {
        icon: Newspaper,
        color: 'bg-purple-50 text-purple-600',
        title: 'Artikel & Panduan',
        desc: 'Ribuan artikel pertanian modern, tips budidaya, teknologi tani, dan berita agrikultur terkini.',
        badgeClass: 'bg-[#E8F5E9] text-[#2E7D32] border border-[#A5D6A7]',
        href: '/articles',
    },
    {
        icon: ShoppingBag,
        color: 'bg-orange-50 text-orange-600',
        title: 'Toko Produk',
        desc: 'Temukan produk pertanian berkualitas. Langsung tersambung ke Shopee, Tokopedia & TikTok Shop.',
        badgeClass: 'bg-[#E8F5E9] text-[#2E7D32] border border-[#A5D6A7]',
        href: '/products',
    },
];

const membershipPlans = props.membershipPlans ?? [];

const latestArticles = (props.latestArticles ?? []).map((article, index) => ({
    ...article,
    categoryColor: ['bg-blue-50 text-blue-700', 'bg-green-50 text-green-700', 'bg-amber-50 text-amber-700'][index % 3],
}));

const products = props.products ?? [];

const testimonials = [
    {
        name: 'Budi Santoso',
        role: 'Petani Padi, Jawa Tengah',
        avatar: 'BS',
        avatarColor: 'bg-[#2E7D32]',
        content: 'Setelah bergabung sebagai member premium, produksi padi saya meningkat 40% dalam 2 musim. Mentor di sini benar-benar ahli dan sabar menjelaskan.',
        stars: 5,
    },
    {
        name: 'Siti Rahayu',
        role: 'Petani Sayur, Jawa Barat',
        avatar: 'SR',
        avatarColor: 'bg-purple-600',
        content: 'Artikel-artikelnya sangat informatif dan mudah dipahami. Saya berhasil menerapkan teknik hidroponik berkat panduan yang ada di sini.',
        stars: 5,
    },
    {
        name: 'Ahmad Fauzi',
        role: 'Petani Organik, Bali',
        avatar: 'AF',
        avatarColor: 'bg-orange-600',
        content: 'Fitur konsultasi realtime luar biasa. Bisa langsung kirim foto tanaman yang bermasalah dan mentor langsung kasih solusi yang tepat.',
        stars: 5,
    },
];

const statIconMap: Record<StatIconKey, unknown> = {
    users: Users,
    newspaper: Newspaper,
    'shield-check': ShieldCheck,
    'trending-up': TrendingUp,
};

const stats = (props.stats ?? []).map((item) => ({
    ...item,
    icon: statIconMap[item.icon],
}));

</script>

<template>
    <Head :title="`${props.appSetting?.brand_name || 'PetaniCerdas'} - Platform Agritech Digital Indonesia`" />

    <PublicLayout>
        <!-- HERO -->
        <section class="relative overflow-hidden bg-gradient-to-br from-[#1b4332] via-[#2E7D32] to-[#4CAF50]">
            <div class="absolute inset-0 opacity-10"
                 style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 32px 32px;"></div>
            <div class="relative mx-auto max-w-7xl px-6 py-20 lg:px-8 lg:py-28">
                <div class="grid grid-cols-1 gap-12 lg:grid-cols-2 lg:items-center">
                    <div class="text-white">
                        <div class="mb-5 inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-1.5 text-sm font-medium backdrop-blur-sm">
                            <Zap class="h-3.5 w-3.5 text-yellow-300" />
                            Platform #1 Agritech Indonesia
                        </div>
                        <h1 class="text-4xl font-extrabold leading-tight tracking-tight lg:text-5xl xl:text-6xl">
                            Tani Lebih Cerdas,<br>
                            <span class="text-[#A5D6A7]">Panen Lebih Optimal</span>
                        </h1>
                        <p class="mt-5 text-lg leading-relaxed text-green-100 lg:text-xl max-w-lg">
                            {{ props.appSetting?.app_description || 'Bergabung dengan ribuan petani Indonesia yang sudah memanfaatkan teknologi modern, konsultasi ahli, dan komunitas pertanian terbaik di satu platform.' }}
                        </p>
                        <div class="mt-8 flex flex-wrap gap-3">
                            <Button
                                v-if="canRegister"
                                as-child
                                size="lg"
                                class="rounded-xl bg-white px-6 text-sm font-semibold text-[#2E7D32] shadow-md hover:bg-green-50"
                            >
                                <Link href="/pendaftaran">
                                    Mulai Gratis Sekarang
                                    <ArrowRight class="h-4 w-4" />
                                </Link>
                            </Button>
                            <Button
                                as-child
                                size="lg"
                                variant="outline"
                                class="rounded-xl border-white/30 bg-white/10 px-6 text-sm font-semibold text-white backdrop-blur-sm hover:bg-white/20 hover:text-white"
                            >
                                <Link href="/articles">Lihat Artikel</Link>
                            </Button>
                        </div>
                        <div class="mt-10 flex flex-wrap items-center gap-6 text-green-100 text-sm">
                            <span class="flex items-center gap-1.5"><CheckCircle2 class="h-4 w-4 text-[#A5D6A7]" />Gratis bergabung</span>
                            <span class="flex items-center gap-1.5"><CheckCircle2 class="h-4 w-4 text-[#A5D6A7]" />Tanpa syarat kartu kredit</span>
                            <span class="flex items-center gap-1.5"><CheckCircle2 class="h-4 w-4 text-[#A5D6A7]" />Batalkan kapan saja</span>
                        </div>
                    </div>
                    <!-- Decorative dashboard card (desktop only) -->
                    <div class="hidden lg:flex items-center justify-center">
                        <div class="relative w-full max-w-sm">
                            <div class="rounded-2xl bg-white/15 backdrop-blur-sm border border-white/20 p-6 shadow-2xl">
                                <div class="flex items-center gap-3 mb-5">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#4CAF50]">
                                        <Leaf class="h-5 w-5 text-white" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-white">Dashboard Petani</p>
                                        <p class="text-xs text-green-200">Selamat datang, Budi!</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3 mb-4">
                                    <div class="rounded-xl bg-white/10 p-3">
                                        <p class="text-2xl font-bold text-white">4.2t</p>
                                        <p class="text-xs text-green-200">Estimasi Panen</p>
                                    </div>
                                    <div class="rounded-xl bg-white/10 p-3">
                                        <p class="text-2xl font-bold text-white">+18%</p>
                                        <p class="text-xs text-green-200">Dibanding tahun lalu</p>
                                    </div>
                                </div>
                                <div class="rounded-xl bg-white/10 p-3">
                                    <p class="text-xs text-green-200 mb-2">Pesan dari Coach Ahmad</p>
                                    <p class="text-sm text-white">"Kondisi tanah sudah optimal untuk mulai tanam minggu ini..."</p>
                                </div>
                            </div>
                            <div class="absolute -top-4 -right-4 rounded-full bg-yellow-400 px-3 py-1.5 text-xs font-bold text-yellow-900 shadow-md">⭐ 4.9/5</div>
                            <div class="absolute -bottom-4 -left-4 rounded-xl bg-white px-3 py-2 text-xs font-semibold text-gray-800 shadow-md">👥 12.000+ petani aktif</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0">
                <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full" preserveAspectRatio="none">
                    <path d="M0 60L1440 60L1440 20C1200 50 960 0 720 20C480 40 240 0 0 20L0 60Z" fill="white"/>
                </svg>
            </div>
        </section>

        <!-- STATS -->
        <section class="border-b border-gray-100 bg-white py-12">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="grid grid-cols-2 gap-6 lg:grid-cols-4">
                    <div v-for="stat in stats" :key="stat.label" class="flex flex-col items-center text-center">
                        <component :is="stat.icon" class="mb-2 h-6 w-6 text-[#4CAF50]" />
                        <p class="text-2xl font-extrabold text-gray-900 lg:text-3xl">{{ stat.value }}</p>
                        <p class="mt-0.5 text-sm text-gray-500">{{ stat.label }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- FEATURES -->
        <section class="bg-white py-20 lg:py-24">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center mb-14">
                    <h2 class="text-3xl font-extrabold text-gray-900 lg:text-4xl">Semua yang Dibutuhkan Petani Modern</h2>
                    <p class="mt-4 text-lg text-gray-500">Dari konsultasi ahli hingga belanja produk tani, semua tersedia dalam satu platform.</p>
                </div>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <Card
                        v-for="feature in features"
                        :key="feature.title"
                        class="group relative flex h-full flex-col rounded-2xl border-gray-100 bg-white shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md"
                    >
                        <CardContent class="flex h-full flex-col p-6">
                            <div class="mb-4 flex items-start justify-between">
                                <div :class="['flex h-12 w-12 items-center justify-center rounded-xl', feature.color]">
                                    <component :is="feature.icon" class="h-6 w-6" />
                                </div>
                            </div>
                            <h3 class="mb-2 text-base font-semibold text-gray-900">{{ feature.title }}</h3>
                            <p class="flex-1 text-sm leading-relaxed text-gray-500">{{ feature.desc }}</p>
                            <Link :href="feature.href" class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-[#2E7D32] transition-all hover:gap-2">
                                Pelajari lebih lanjut <ArrowRight class="h-3.5 w-3.5" />
                            </Link>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </section>

        <!-- ARTICLES -->
        <section class="bg-gray-50 py-20 lg:py-24">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="flex items-end justify-between mb-10">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-widest text-[#2E7D32] mb-1">Artikel Terbaru</p>
                        <h2 class="text-2xl font-extrabold text-gray-900 lg:text-3xl">Pengetahuan Tani Terkini</h2>
                    </div>
                    <Link href="/articles" class="hidden sm:inline-flex items-center gap-1 text-sm font-medium text-[#2E7D32] hover:gap-2 transition-all">
                        Lihat semua artikel <ArrowRight class="h-4 w-4" />
                    </Link>
                </div>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <article
                        v-for="article in latestArticles"
                        :key="article.title"
                        class="group flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm hover:shadow-md transition-all duration-200"
                    >
                        <div class="aspect-video w-full bg-gradient-to-br from-[#E8F5E9] to-[#C8E6C9] flex items-center justify-center">
                            <Leaf class="h-12 w-12 text-[#A5D6A7]" />
                        </div>
                        <div class="flex flex-1 flex-col p-5">
                            <div class="mb-3 flex items-center gap-2">
                                <span :class="['rounded-full px-2.5 py-0.5 text-xs font-medium', article.categoryColor]">{{ article.category }}</span>
                                <span class="text-xs text-gray-400">{{ article.readTime }} baca</span>
                            </div>
                            <h3 class="mb-2 font-semibold text-gray-900 group-hover:text-[#2E7D32] transition-colors leading-snug">{{ article.title }}</h3>
                            <p class="flex-1 text-sm text-gray-500 leading-relaxed line-clamp-2">{{ article.excerpt }}</p>
                            <p class="mt-4 text-xs text-gray-400">{{ article.date }}</p>
                        </div>
                    </article>
                </div>
                <div class="mt-8 text-center sm:hidden">
                    <Button as-child variant="outline" class="rounded-xl border-gray-200 bg-white text-sm font-medium text-gray-700 shadow-sm">
                        <Link href="/articles">
                            Lihat semua artikel <ArrowRight class="h-4 w-4" />
                        </Link>
                    </Button>
                </div>
            </div>
        </section>

        <!-- PRODUCTS -->
        <section class="bg-white py-20 lg:py-24">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="flex items-end justify-between mb-10">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-widest text-[#2E7D32] mb-1">Produk Unggulan</p>
                        <h2 class="text-2xl font-extrabold text-gray-900 lg:text-3xl">Belanja Produk Pertanian Terbaik</h2>
                    </div>
                    <Link href="/products" class="hidden sm:inline-flex items-center gap-1 text-sm font-medium text-[#2E7D32] hover:gap-2 transition-all">
                        Lihat semua produk <ArrowRight class="h-4 w-4" />
                    </Link>
                </div>
                <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                    <div
                        v-for="product in products"
                        :key="product.name"
                        class="group rounded-2xl border border-gray-100 bg-white p-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200"
                    >
                        <div class="mb-4 aspect-square w-full overflow-hidden rounded-xl bg-gradient-to-br from-[#E8F5E9] to-[#C8E6C9] flex items-center justify-center">
                            <ShoppingBag class="h-10 w-10 text-[#A5D6A7]" />
                        </div>
                        <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-500">{{ product.category }}</span>
                        <h3 class="mt-2 text-sm font-semibold text-gray-900 leading-snug group-hover:text-[#2E7D32] transition-colors line-clamp-2">{{ product.name }}</h3>
                        <div class="mt-2 flex items-center gap-1">
                            <Star class="h-3.5 w-3.5 fill-yellow-400 text-yellow-400" />
                            <span class="text-xs font-medium text-gray-700">{{ product.rating }}</span>
                            <span class="text-xs text-gray-400">({{ product.sold }} terjual)</span>
                        </div>
                        <p class="mt-2 font-semibold text-[#2E7D32]">{{ product.price }}</p>
                        <Button as-child size="sm" variant="outline" class="mt-3 w-full border-[#A5D6A7] bg-[#E8F5E9] text-xs font-semibold text-[#2E7D32] hover:bg-[#2E7D32] hover:text-white">
                            <Link href="/products">Lihat Toko</Link>
                        </Button>
                    </div>
                </div>
            </div>
        </section>

        <!-- MEMBERSHIP -->
        <section class="bg-gradient-to-b from-gray-50 to-white py-20 lg:py-24">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center mb-14">
                    <p class="text-sm font-semibold uppercase tracking-widest text-[#2E7D32] mb-2">Membership</p>
                    <h2 class="text-3xl font-extrabold text-gray-900 lg:text-4xl">Pilih Paket yang Sesuai</h2>
                    <p class="mt-4 text-lg text-gray-500">Pilih paket sesuai skala usaha tani Anda, dengan harga promo dan fasilitas yang dirancang untuk hasil nyata.</p>
                </div>
                <div class="mx-auto grid max-w-6xl grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="plan in membershipPlans"
                        :key="plan.name"
                        :class="['relative flex flex-col rounded-2xl p-8', plan.highlight ? 'bg-[#2E7D32] text-white shadow-2xl shadow-green-900/30 ring-2 ring-[#2E7D32]' : 'bg-white border border-gray-200 shadow-sm']"
                    >
                        <div v-if="plan.highlight" class="absolute -top-3.5 left-1/2 -translate-x-1/2 rounded-full bg-yellow-400 px-4 py-1 text-xs font-bold text-yellow-900 shadow">
                            Paling Populer
                        </div>
                        <div class="mb-6">
                            <h3 :class="['text-lg font-bold mb-1', plan.highlight ? 'text-white' : 'text-gray-900']">{{ plan.name }}</h3>
                            <p :class="['mb-2 inline-flex rounded-full px-2.5 py-1 text-xs font-semibold', plan.highlight ? 'bg-white/20 text-green-50' : 'bg-amber-50 text-amber-700 border border-amber-200']">
                                Kuota: {{ plan.quota }}
                            </p>
                            <p :class="['text-sm line-through mb-1', plan.highlight ? 'text-green-200/80' : 'text-gray-400']">{{ plan.originalPrice || '-' }}</p>
                            <div class="flex items-baseline gap-1.5">
                                <span :class="['text-3xl font-extrabold', plan.highlight ? 'text-white' : 'text-gray-900']">{{ plan.price }}</span>
                                <span :class="['text-sm', plan.highlight ? 'text-green-200' : 'text-gray-400']">{{ plan.priceNote }}</span>
                            </div>
                        </div>
                        <ul class="flex-1 space-y-3 mb-8">
                            <li v-for="feat in plan.features" :key="feat" class="flex items-start gap-2.5 text-sm">
                                <CheckCircle2 :class="['mt-0.5 h-4 w-4 shrink-0', plan.highlight ? 'text-[#A5D6A7]' : 'text-[#4CAF50]']" />
                                <span :class="plan.highlight ? 'text-green-100' : 'text-gray-600'">{{ feat }}</span>
                            </li>
                        </ul>
                        <Link
                            v-if="plan.available"
                            :href="plan.ctaHref"
                            :class="['block w-full rounded-xl py-3 text-center text-sm font-semibold transition-all', plan.highlight ? 'bg-white text-[#2E7D32] hover:bg-green-50 shadow-md' : 'bg-[#2E7D32] text-white hover:bg-[#256427] shadow-sm']"
                        >{{ plan.cta }}</Link>
                        <button
                            v-else
                            type="button"
                            disabled
                            :class="['block w-full rounded-xl py-3 text-center text-sm font-semibold shadow-sm cursor-not-allowed', plan.highlight ? 'bg-white/30 text-green-50' : 'bg-gray-100 text-gray-400 border border-gray-200']"
                        >{{ plan.cta }}</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- TESTIMONIALS -->
        <section class="bg-white py-20 lg:py-24">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center mb-14">
                    <p class="text-sm font-semibold uppercase tracking-widest text-[#2E7D32] mb-2">Testimoni</p>
                    <h2 class="text-3xl font-extrabold text-gray-900 lg:text-4xl">Kata Mereka yang Sudah Bergabung</h2>
                </div>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <div
                        v-for="testimonial in testimonials"
                        :key="testimonial.name"
                        class="flex flex-col rounded-2xl bg-gray-50 p-6 hover:bg-[#E8F5E9]/60 transition-colors"
                    >
                        <div class="mb-4 flex gap-0.5">
                            <Star v-for="i in testimonial.stars" :key="i" class="h-4 w-4 fill-yellow-400 text-yellow-400" />
                        </div>
                        <p class="flex-1 text-sm leading-relaxed text-gray-700 italic">"{{ testimonial.content }}"</p>
                        <div class="mt-5 flex items-center gap-3">
                            <div :class="['flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-white text-sm font-bold', testimonial.avatarColor]">{{ testimonial.avatar }}</div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ testimonial.name }}</p>
                                <p class="text-xs text-gray-500">{{ testimonial.role }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="bg-gradient-to-r from-[#1b4332] to-[#2E7D32] py-16 lg:py-20">
            <div class="mx-auto max-w-3xl px-6 text-center lg:px-8">
                <h2 class="text-3xl font-extrabold text-white lg:text-4xl">Siap Bertani Lebih Cerdas?</h2>
                <p class="mt-4 text-lg text-green-100">Bergabung gratis hari ini. Lebih dari 12.000 petani Indonesia sudah membuktikannya.</p>
                <div class="mt-8 flex flex-wrap justify-center gap-4">
                    <Button
                        v-if="canRegister"
                        as-child
                        size="lg"
                        class="rounded-xl bg-white px-8 text-sm font-bold text-[#2E7D32] shadow-lg hover:bg-green-50"
                    >
                        <Link href="/pendaftaran">
                            Daftar Gratis Sekarang <ArrowRight class="h-4 w-4" />
                        </Link>
                    </Button>
                    <Button
                        as-child
                        size="lg"
                        variant="outline"
                        class="rounded-xl border-white/30 bg-white/10 px-8 text-sm font-semibold text-white backdrop-blur-sm hover:bg-white/20 hover:text-white"
                    >
                        <Link href="/login">Sudah punya akun? Masuk</Link>
                    </Button>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
