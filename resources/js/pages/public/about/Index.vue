<script setup lang="ts">
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Head } from '@inertiajs/vue3';
import {
    CheckCircle2,
    Handshake,
    Leaf,
    Sprout,
    Store,
    Target,
    Tractor,
    Users,
} from 'lucide-vue-next';

interface AppSettingItem {
    app_name: string;
    brand_name: string;
    app_description: string;
    logo_url: string | null;
}

const props = defineProps<{
    appSetting?: AppSettingItem;
}>();

const valueHighlights = [
    'Produk pertanian berkualitas dan terstandarisasi',
    'Sistem produksi berkelanjutan dan stabil',
    'Kemitraan petani yang adil dan professional',
    'Integrasi rantai pasok dari hulu ke hilir',
    'Praktik pertanian ramah lingkungan',
    'Distribusi efisien dan transparan',
];

const visionMission = {
    vision: 'Menjadi ekosistem pertanian berkelanjutan terintegrasi berbasis komunitas yang mampu meningkatkan kesejahteraan dan ketahanan pangan sebelum tahun 2030.',
    missions: [
        'Mengembangkan pertanian berbasis teknologi.',
        'Membangun kemitraan petani berkelanjutan',
        'Mengembangkan sistem distribusi modern',
        'Menghasilkan produk pertanian berkualitas',
        'Menjadi pusat informasi pertanian terintegrasi',
    ],
};

const productsAndServices = [
    {
        title: 'Edukasi & konsultasi pertanian',
        icon: Users,
    },
    {
        title: 'Pendampingan Smart Farming',
        icon: Tractor,
    },
    {
        title: 'Fasilitasi akses pasar dan distribusi',
        icon: Store,
    },
    {
        title: 'Pengembangan produk pertanian bernilai tambah',
        icon: Handshake,
    },
    {
        title: 'Branding dan publikasi mitra pertanian',
        icon: Target,
    },
    {
        title: 'Pusat informasi pertanian digital',
        icon: Sprout,
    },
];

const marketSegments = [
    'Konsumen rumah tangga',
    'Retail modern dan supermarket',
    'Hotel, restoran, dan katering',
    'Distributor dan pedagang besar',
    'Industri pengolahan pangan',
    'Marketplace dan platform digital',
    'Koperasi, BUMDes, dan program pemerintah',
];

const partnershipModels = [
    'Penyertaan modal (Equity Investment)',
    'Sistem bagi hasil (Profit Sharing)',
    'Investasi berbasis proyek (Project-Based Investment)',
    'Sponsorship dan kolaborasi strategis',
];

const socialImpact = [
    'Meningkatkan kesejahteraan petani',
    'Memperkuat posisi tawar petani',
    'Menciptakan lapangan ekonomi baru',
    'Mendorong regenerasi petani muda',
    'Mengubah citra pertanian menjadi modern dan profesional',
];

const initiators = [
    {
        name: 'eris riswandi',
        role: 'Chief Executive Officer',
        profile: 'Berpengalaman dalam pengembangan agribisnis berbasis komunitas, dengan fokus pada penguatan model kolaborasi petani dan akses pasar berkelanjutan.',
        photo: '/assets/01.%20Eris.png',
    },
    {
        name: 'hirlan maulana',
        role: 'Chief Financial Officer',
        profile: 'Memimpin program pendampingan budidaya dan implementasi smart farming agar praktik di lapangan lebih terukur, efisien, dan adaptif terhadap musim.',
        photo: '/assets/02.%20Hirlan.png',
    },
    {
        name: 'rian hidayat',
        role: 'Chief Operating Officer',
        profile: 'Berfokus pada penguatan jejaring petani, fasilitasi kemitraan offtaker, serta peningkatan nilai tambah hasil panen melalui strategi distribusi profesional.',
        photo: '/assets/03.%20Rian.png',
    },
];

const galleryImages = [
    {
        src: 'https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=1200&q=80',
        alt: 'Lahan pertanian hijau dengan tanaman padi',
    },
    {
        src: '/assets/aboutme1.jpg',
        alt: 'Petani bekerja di lahan hortikultura',
    },
    {
        src: 'https://images.unsplash.com/photo-1501004318641-b39e6451bec6?auto=format&fit=crop&w=1200&q=80',
        alt: 'Bibit tanaman dan proses pembibitan',
    },
];
</script>

<template>
    <Head title="Tentang Petani Cerdas" />

    <PublicLayout>
        <section class="relative overflow-hidden bg-gradient-to-br from-[#1f5a2f] via-[#2E7D32] to-[#66BB6A] py-16 lg:py-24">
            <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle, #ffffff 1px, transparent 1px); background-size: 24px 24px" />
            <div class="relative mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-green-100">Tentang Kami</p>
                        <h1 class="mt-3 text-3xl font-extrabold leading-tight text-white lg:text-5xl">
                            {{ props.appSetting?.brand_name || props.appSetting?.app_name || 'Petani Cerdas' }}
                        </h1>
                        <p class="mt-2 text-base italic text-green-100 lg:text-lg">Efficiency Through Intelligence</p>
                        <p class="mt-5 text-lg leading-relaxed text-green-50">
                            Petani Cerdas adalah inisiatif agribisnis modern berbasis komunitas yang mengintegrasikan produksi pertanian, kemitraan petani, teknologi smart farming, dan sistem distribusi profesional. Kami hadir untuk meningkatkan kesejahteraan petani sekaligus memperkuat ketahanan pangan secara berkelanjutan.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-gray-50 py-14 lg:py-20">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-2xl font-extrabold text-gray-900 lg:text-3xl">Inisiator Utama</h2>
                    <p class="mt-2 text-sm text-gray-600">Tim penggerak yang menginisiasi pertumbuhan ekosistem Petani Cerdas.</p>
                </div>

                <div class="mt-8 grid gap-5 md:grid-cols-3">
                    <article
                        v-for="person in initiators"
                        :key="person.name"
                        class="group rounded-3xl border border-gray-200 bg-white p-6 text-center shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
                    >
                        <div class="mx-auto h-28 w-28 rounded-full bg-gradient-to-br from-[#d9f0db] via-white to-[#dff6e1] p-1.5">
                            <img
                                :src="person.photo"
                                :alt="`Foto ${person.name}`"
                                class="h-full w-full rounded-full object-cover ring-2 ring-white"
                            />
                        </div>
                        <h3 class="mt-4 text-base font-bold text-gray-900 uppercase">{{ person.name }}</h3>
                        <p class="mt-1 text-sm font-medium text-[#2E7D32]">{{ person.role }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="bg-white py-14 lg:py-20">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="grid gap-8 lg:grid-cols-2 lg:items-start">
                    <div>
                        <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-[#E8F5E9] px-4 py-1.5 text-sm font-semibold text-[#2E7D32]">
                            <Leaf class="h-4 w-4" /> Latar Belakang
                        </div>
                        <h2 class="text-2xl font-extrabold text-gray-900 lg:text-3xl">Mengapa Petani Cerdas Didirikan</h2>
                        <p class="mt-4 text-base leading-relaxed text-gray-600">
                            Sektor pertanian memiliki peran strategis, namun masih menghadapi tantangan seperti:
                        </p>
                        <ul class="mt-6 space-y-3 rounded-2xl border border-green-100 bg-[#f6fbf7] p-6 shadow-sm">
                            <li class="flex items-start gap-2.5">
                                <CheckCircle2 class="mt-0.5 h-4 w-4 shrink-0 text-[#2E7D32]" />
                                <span>Akses teknologi yang terbatas</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <CheckCircle2 class="mt-0.5 h-4 w-4 shrink-0 text-[#2E7D32]" />
                                <span>Fluktuasi harga dan pasar</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <CheckCircle2 class="mt-0.5 h-4 w-4 shrink-0 text-[#2E7D32]" />
                                <span>Sistem pertanian yang belum terintegrasi</span>
                            </li>
                        </ul>
                        <p class="mt-6 text-base leading-relaxed text-gray-600">
                            Petani Cerdas menjawab tantangan tersebut melalui ekosistem agribisnis kolektif yang modern, efisien, dan berdaya saing.
                        </p>
                    </div>

                    <div class="overflow-hidden rounded-3xl border border-green-100 bg-[#f6fbf7] p-2 shadow-sm">
                        <img :src="galleryImages[1].src" :alt="galleryImages[1].alt" class="h-72 w-full rounded-2xl object-cover lg:h-96" />
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-[#f7faf7] py-14 lg:py-20">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="grid gap-6 lg:grid-cols-2">
                    <article class="rounded-2xl border border-green-100 bg-white p-6 shadow-sm">
                        <div class="mb-3 inline-flex items-center gap-2 rounded-full bg-[#E8F5E9] px-3 py-1 text-xs font-semibold text-[#2E7D32]">
                            <Target class="h-4 w-4" /> Visi
                        </div>
                        <p class="text-sm leading-relaxed text-gray-700">{{ visionMission.vision }}</p>
                    </article>

                    <article class="rounded-2xl border border-green-100 bg-white p-6 shadow-sm">
                        <div class="mb-3 inline-flex items-center gap-2 rounded-full bg-[#E8F5E9] px-3 py-1 text-xs font-semibold text-[#2E7D32]">
                            <Sprout class="h-4 w-4" /> Misi
                        </div>
                        <ul class="space-y-2.5">
                            <li v-for="mission in visionMission.missions" :key="mission" class="flex items-start gap-2.5 text-sm text-gray-700">
                                <CheckCircle2 class="mt-0.5 h-4 w-4 shrink-0 text-[#2E7D32]" />
                                <span>{{ mission }}</span>
                            </li>
                        </ul>
                    </article>
                </div>
            </div>
        </section>

        <section class="bg-white py-14 lg:py-20">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-2xl font-extrabold text-gray-900 lg:text-3xl">Nilai Unggulan</h2>
                    <p class="mt-3 text-gray-600">Nilai yang menjadi fondasi budaya kerja dan pelayanan Petani Cerdas.</p>
                </div>

                <div class="mt-10 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <article v-for="item in valueHighlights" :key="item" class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="flex items-start gap-2.5">
                            <CheckCircle2 class="mt-0.5 h-5 w-5 shrink-0 text-[#2E7D32]" />
                            <h3 class="text-base font-semibold text-gray-900">{{ item }}</h3>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="bg-gray-50 py-14 lg:py-20">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="grid gap-8 lg:grid-cols-[1.3fr_1fr] lg:items-start">
                    <div>
                        <h2 class="text-2xl font-extrabold text-gray-900 lg:text-3xl">Produk dan Layanan</h2>
                        <p class="mt-3 text-sm leading-relaxed text-gray-600">
                            Solusi kami dirancang untuk menjawab kebutuhan dari tahap budidaya, pendampingan teknis,
                            hingga penguatan distribusi dan akses pasar.
                        </p>

                        <div class="mt-6 grid gap-4 sm:grid-cols-2">
                            <article v-for="service in productsAndServices" :key="service.title" class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                                <div class="mb-3 inline-flex h-9 w-9 items-center justify-center rounded-lg bg-[#E8F5E9] text-[#2E7D32]">
                                    <component :is="service.icon" class="h-5 w-5" />
                                </div>
                                <h3 class="text-base font-bold text-gray-900">{{ service.title }}</h3>
                            </article>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-3xl border border-green-100 bg-white p-2 shadow-sm">
                        <img :src="galleryImages[2].src" :alt="galleryImages[2].alt" class="h-72 w-full rounded-2xl object-cover lg:h-[28rem]" />
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white py-14 lg:py-20">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <h2 class="text-2xl font-extrabold text-gray-900 lg:text-3xl">Segmen Mitra dan Pasar</h2>
                <div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <article v-for="segment in marketSegments" :key="segment" class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-base font-semibold text-gray-900">{{ segment }}</h3>
                    </article>
                </div>
            </div>
        </section>

        <section class="bg-[#f7faf7] py-14 lg:py-20">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <h2 class="text-2xl font-extrabold text-gray-900 lg:text-3xl">Model Kemitraan</h2>
                <ul class="mt-6 space-y-3 rounded-2xl border border-green-100 bg-white p-6 shadow-sm">
                    <li v-for="model in partnershipModels" :key="model" class="flex items-start gap-2.5 text-sm text-gray-700">
                        <Handshake class="mt-0.5 h-4 w-4 shrink-0 text-[#2E7D32]" />
                        <span>{{ model }}</span>
                    </li>
                </ul>
            </div>
        </section>

        <section class="bg-white py-14 lg:py-20">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <h2 class="text-2xl font-extrabold text-gray-900 lg:text-3xl">Dampak Sosial</h2>
                <div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <article v-for="impact in socialImpact" :key="impact" class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="flex items-start gap-2.5">
                            <CheckCircle2 class="mt-0.5 h-5 w-5 shrink-0 text-[#2E7D32]" />
                            <p class="text-sm font-medium leading-relaxed text-gray-700">{{ impact }}</p>
                        </div>
                    </article>
                </div>
            </div>
        </section>


    </PublicLayout>
</template>
