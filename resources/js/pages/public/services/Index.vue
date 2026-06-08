<script setup lang="ts">
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Archive, ArrowRight, Globe, Image, MessageSquare, Send, Sparkles, Video } from 'lucide-vue-next';
import { computed } from 'vue';

interface ServiceItem {
    id: string;
    title: string;
    description: string;
    bullets: string[];
    imageUrl: string;
    icon: typeof MessageSquare;
}

interface LiveSessionItem {
    day: string;
    date: string;
    time: string;
    topic: string;
    mentor: string;
    quota: string;
    platform: string;
}

interface PlanComparison {
    name: string;
    price: string;
    quota: string;
    supportSpeed: string;
    oneOnOneChat: string;
    liveSessionAccess: string;
    strategyReport: string;
    communityAccess: string;
    priorityBadge: string;
    bestFor: string;
}

interface SponsorLogo {
    name: string;
    initials: string;
}

const liveSessions: LiveSessionItem[] = [
    {
        day: 'Senin',
        date: '24 Maret 2026',
        time: '19:30 - 21:00 WIB',
        topic: 'Strategi Tanam Cabai Saat Curah Hujan Tinggi',
        mentor: 'Pak Rudi Hidayat',
        quota: '80 peserta',
        platform: 'Zoom Meeting',
    },
    {
        day: 'Rabu',
        date: '26 Maret 2026',
        time: '20:00 - 21:30 WIB',
        topic: 'Bedah Hama Daun dan Akar + Simulasi Penanganan',
        mentor: 'Bu Sari Lestari',
        quota: '60 peserta',
        platform: 'Google Meet',
    },
    {
        day: 'Sabtu',
        date: '29 Maret 2026',
        time: '09:00 - 10:30 WIB',
        topic: 'Optimasi Biaya Produksi untuk Petani Skala Kecil',
        mentor: 'Pak Ahmad Firman',
        quota: '100 peserta',
        platform: 'Zoom Meeting',
    },
    {
        day: 'Selasa',
        date: '1 April 2026',
        time: '19:00 - 20:30 WIB',
        topic: 'From Farm to Market: Menjual Hasil Panen Lebih Cepat',
        mentor: 'Bu Dewi Kusuma',
        quota: '75 peserta',
        platform: 'Google Meet',
    },
];

const plans: PlanComparison[] = [
    {
        name: 'Basic',
        price: 'Rp 149.000 / bulan',
        quota: '30 member',
        supportSpeed: '2x24 jam',
        oneOnOneChat: 'Terbatas (antrian)',
        liveSessionAccess: '1x / bulan',
        strategyReport: 'Tidak tersedia',
        communityAccess: 'Ya',
        priorityBadge: 'Tidak',
        bestFor: 'Pemula yang baru mulai budidaya',
    },
    {
        name: 'Pro',
        price: 'Rp 199.000 / bulan',
        quota: '20 member',
        supportSpeed: 'Prioritas',
        oneOnOneChat: 'Ya (interaktif)',
        liveSessionAccess: '2x / bulan',
        strategyReport: 'Bulanan',
        communityAccess: 'Ya',
        priorityBadge: 'Ya',
        bestFor: 'Petani aktif yang butuh growth cepat',
    },
    {
        name: 'Elite',
        price: 'Rp 799.000 / 3 bulan',
        quota: '5 member',
        supportSpeed: 'Fast direct',
        oneOnOneChat: 'Ya (intensif)',
        liveSessionAccess: 'Weekly privat',
        strategyReport: 'Mingguan + review personal',
        communityAccess: 'Ya',
        priorityBadge: 'Ya (Premium)',
        bestFor: 'Petani/produsen yang scale-up bisnis',
    },
];

const props = defineProps<{
    sponsors: { id: number; name: string; logo_url: string | null }[];
}>();

const mappedSponsors = computed(() => {
    return props.sponsors.map((sponsor) => ({
        name: sponsor.name,
        initials: sponsor.name.substring(0, 2).toUpperCase(),
        logo: sponsor.logo_url,
    }));
});

const duplicatedTop = computed(() => [...mappedSponsors.value, ...mappedSponsors.value]);
// const duplicatedBottom = [...sponsorRowBottom, ...sponsorRowBottom];
const page = usePage<{ auth?: { user?: unknown | null } }>();

const membershipCtaHref = computed(() => {
    return page.props.auth?.user ? route('petani.subscriptions.packages.index') : route('public.register.index');
});

const scrollToSection = (id: string) => {
    const section = document.getElementById(id);

    if (!section) {
        return;
    }

    section.scrollIntoView({
        behavior: 'smooth',
        block: 'start',
    });
};
</script>

<template>
    <Head title="Layanan Petani Cerdas" />

    <PublicLayout>
        <section class="relative overflow-hidden bg-gradient-to-br from-[#1f5a2f] via-[#2E7D32] to-[#5fae62] py-16 lg:py-24">
            <div
                class="absolute inset-0 opacity-15"
                style="background-image: radial-gradient(circle, #ffffff 1px, transparent 1px); background-size: 26px 26px"
            />
            <div class="relative mx-auto max-w-7xl px-6 lg:px-8">
                <div class="grid grid-cols-1 items-center gap-8 lg:grid-cols-2">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.16em] text-green-100">Layanan Kami</p>
                        <h1 class="mt-3 max-w-3xl text-3xl font-extrabold leading-tight text-white lg:text-5xl">
                            Pendampingan Tani yang Praktikal,
                            <span class="text-[#D6F5D0]">Bukan Sekadar Teori</span>
                        </h1>
                        <p class="mt-4 max-w-3xl text-lg text-green-50">
                            Petani Cerdas membantu Anda mengambil keputusan budidaya melalui coaching 1-on-1 by chat, live session terjadwal, dan
                            sistem membership yang jelas manfaatnya.
                        </p>
                        <div class="mt-8 flex flex-wrap gap-3">
                            <Button as-child size="lg" class="bg-white text-[#2E7D32] hover:bg-green-50">
                                <Link href="#konsultasi" @click.prevent="scrollToSection('konsultasi')">
                                    Lihat Detail Layanan
                                    <ArrowRight class="h-4 w-4" />
                                </Link>
                            </Button>
                            <Button
                                as-child
                                size="lg"
                                variant="outline"
                                class="border-white/30 bg-white/10 text-white hover:bg-white/20 hover:text-white"
                            >
                                <Link href="#subscription-plan" @click.prevent="scrollToSection('subscription-plan')"
                                    >Bandingkan Subscription Plan</Link
                                >
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="konsultasi" class="bg-white py-14 lg:py-20">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="grid gap-12 lg:grid-cols-2 lg:items-center">
                    <!-- Content -->
                    <div>
                        <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-emerald-50 px-4 py-1.5 text-sm font-semibold text-[#2E7D32]">
                            <MessageSquare class="h-4 w-4" /> Fitur Premium
                        </div>
                        <h2 class="text-3xl font-extrabold text-gray-900 lg:text-4xl">Konsultasi Realtime</h2>
                        <p class="mt-4 text-lg leading-relaxed text-gray-500">
                            Chat langsung dengan mentor dan petani ahli berpengalaman. Dapatkan jawaban atas pertanyaan pertanian Anda kapan saja dan
                            di mana saja, secara real-time.
                        </p>

                        <ul class="mt-8 space-y-4">
                            <li class="flex items-start gap-3">
                                <div class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100">
                                    <Send class="h-3.5 w-3.5 text-[#2E7D32]" />
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">Chat Teks Realtime</p>
                                    <p class="text-sm text-gray-500">Kirim pesan dan dapatkan balasan instan dari mentor ahli.</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100">
                                    <Image class="h-3.5 w-3.5 text-[#2E7D32]" />
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">Kirim Foto & Video</p>
                                    <p class="text-sm text-gray-500">Upload foto tanaman yang bermasalah untuk diagnosa langsung dari ahli.</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100">
                                    <Globe class="h-3.5 w-3.5 text-[#2E7D32]" />
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">Akses 24/7</p>
                                    <p class="text-sm text-gray-500">Konsultasi kapan saja tanpa terbatas jam kerja.</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100">
                                    <Archive class="h-3.5 w-3.5 text-[#2E7D32]" />
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">Riwayat Chat Tersimpan</p>
                                    <p class="text-sm text-gray-500">Akses kembali semua riwayat percakapan dan saran yang diberikan.</p>
                                </div>
                            </li>
                        </ul>

                        <div class="mt-10">
                            <Button as-child size="lg" class="bg-[#2E7D32] text-white hover:bg-[#256427]">
                                <Link :href="membershipCtaHref" class="inline-flex items-center gap-2">
                                    Daftar Sekarang <ArrowRight class="h-4 w-4" />
                                </Link>
                            </Button>
                        </div>
                    </div>

                    <!-- Mockup Card -->
                    <div class="flex justify-center">
                        <div class="w-full max-w-sm overflow-hidden rounded-2xl border bg-white shadow-2xl">
                            <!-- Header -->
                            <div class="flex items-center gap-3 border-b bg-[#2E7D32] px-4 py-3">
                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-white/20 font-bold text-white">A</div>
                                <div>
                                    <p class="text-sm font-semibold text-white">Ahmad (Mentor)</p>
                                    <p class="flex items-center gap-1 text-xs text-green-200">
                                        <span class="inline-block h-1.5 w-1.5 rounded-full bg-green-300"></span> Online
                                    </p>
                                </div>
                            </div>
                            <!-- Messages -->
                            <div class="space-y-3 bg-gray-50 p-4">
                                <div class="flex justify-start">
                                    <div class="max-w-[220px] rounded-2xl rounded-bl-sm bg-white px-3.5 py-2.5 text-sm text-gray-800 shadow-sm">
                                        Halo, tanaman cabai saya daunnya menguning. Ini normal?
                                    </div>
                                </div>
                                <div class="flex justify-end">
                                    <div class="max-w-[220px] rounded-2xl rounded-br-sm bg-[#2E7D32] px-3.5 py-2.5 text-sm text-white">
                                        Bisa kirim fotonya? Kemungkinan kekurangan nitrogen atau serangan penyakit 🌿
                                    </div>
                                </div>
                                <div class="flex justify-start">
                                    <div class="max-w-[220px] overflow-hidden rounded-2xl rounded-bl-sm shadow-sm">
                                        <div class="flex h-16 w-full items-center justify-center rounded-lg bg-green-100 text-2xl">📷</div>
                                    </div>
                                </div>
                                <div class="flex justify-end">
                                    <div class="max-w-[220px] rounded-2xl rounded-br-sm bg-[#2E7D32] px-3.5 py-2.5 text-sm text-white">
                                        Terima kasih, ini gejala defisiensi magnesium. Saya rekomendasikan pupuk MgSO4 ya 👍
                                    </div>
                                </div>
                            </div>
                            <!-- Input -->
                            <div class="flex items-center gap-2 border-t bg-white px-3 py-3">
                                <div class="flex-1 rounded-full bg-gray-100 px-4 py-2 text-xs text-gray-400">Ketik pesan...</div>
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#2E7D32]">
                                    <Send class="h-3.5 w-3.5 text-white" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- section live session -->
        <section class="bg-[#f7fbf7] py-14 lg:py-20">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="grid gap-12 lg:grid-cols-2 lg:items-start">
                    <div class="overflow-hidden rounded-2xl border border-white/25 bg-white/10 backdrop-blur-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 1200 700" fill="none">
                            <defs>
                                <linearGradient id="bg2" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0" stop-color="#EEF5FF" />
                                    <stop offset="1" stop-color="#DDEBFF" />
                                </linearGradient>
                            </defs>
                            <rect width="1200" height="700" rx="34" fill="url(#bg2)" />
                            <rect x="120" y="110" width="740" height="470" rx="30" fill="#ffffff" stroke="#CADDFE" stroke-width="4" />
                            <rect x="120" y="110" width="740" height="86" rx="30" fill="#1F3B7F" />
                            <circle cx="200" cy="153" r="15" fill="#7FB1FF" />
                            <circle cx="250" cy="153" r="15" fill="#6ED8A1" />
                            <circle cx="300" cy="153" r="15" fill="#FFD467" />
                            <circle cx="490" cy="348" r="115" fill="#2E7D32" />
                            <polygon points="460,300 560,348 460,396" fill="#ffffff" />
                            <rect x="905" y="165" width="170" height="135" rx="18" fill="#ffffff" stroke="#CADDFE" stroke-width="3" />
                            <rect x="905" y="330" width="170" height="135" rx="18" fill="#ffffff" stroke="#CADDFE" stroke-width="3" />
                            <rect x="905" y="495" width="170" height="85" rx="18" fill="#ffffff" stroke="#CADDFE" stroke-width="3" />
                            <text x="170" y="250" font-family="Arial, sans-serif" font-size="28" font-weight="700" fill="#1E356E">
                                Live Session with Mentors
                            </text>
                        </svg>
                        <div class="px-4 py-3">
                            <p class="text-xs uppercase tracking-wider text-green-100">Live Session</p>
                            <p class="mt-1 text-sm font-semibold text-white">Diskusi langsung dengan mentor ahli</p>
                        </div>
                    </div>
                    <div class="px-4 py-3">
                        <p class="text-sm font-semibold uppercase tracking-widest text-[#2E7D32]">Live Session</p>
                        <h2 class="mt-2 text-2xl font-extrabold text-gray-900 lg:text-3xl">Sesi Live Interaktif dengan Mentor</h2>
                        <p class="mt-3 text-gray-600">
                            Ikuti sesi live terjadwal untuk diskusi mendalam tentang masalah budidaya, tren pasar, dan strategi tanam bersama mentor
                            ahli.
                        </p>
                        <ul class="space-y-4 px-2 py-3">
                            <li class="flex items-start gap-3">
                                <div class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-green-100">
                                    <Video class="h-3.5 w-3.5 text-[#2E7D32]" />
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">Topik Praktikal Sesuai Musim</p>
                                    <p class="text-sm text-gray-500">Sesi live dengan topik yang relevan dengan musim tanam dan kebutuhan petani.</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-green-100">
                                    <MessageSquare class="h-3.5 w-3.5 text-[#2E7D32]" />
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">Diskusi Dua Arah dengan Mentor</p>
                                    <p class="text-sm text-gray-500">
                                        Tanya jawab langsung dengan mentor yang aktif di lapangan untuk solusi praktis.
                                    </p>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-green-100">
                                    <Archive class="h-3.5 w-3.5 text-[#2E7D32]" />
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">Rekaman & Ringkasan Materi</p>
                                    <p class="text-sm text-gray-500">Setiap sesi live direkam dan disertai ringkasan materi untuk referensi Anda.</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section id="subscription-plan" class="bg-white py-14 lg:py-20">
            <div class="mx-auto max-w-4xl px-6 text-center lg:px-8">
                <h2 class="text-3xl font-extrabold text-gray-900">Perbandingan Paket Membership Terbaru</h2>
                <p class="mt-3 text-gray-500">Bandingkan paket Basic, Pro, dan Elite untuk memilih yang paling sesuai dengan skala usaha Anda.</p>

                <div class="mt-10 overflow-x-auto rounded-2xl border shadow-sm">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-4 text-left font-semibold text-gray-700">Fitur</th>
                                <th class="px-6 py-4 text-center font-semibold text-gray-700">Basic</th>
                                <th class="bg-green-50 px-6 py-4 text-center font-semibold text-[#2E7D32]">Pro</th>
                                <th class="px-6 py-4 text-center font-semibold text-gray-700">Elite</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr
                                v-for="(row, i) in [
                                    ['Kuota', '30 orang', '20 orang', '5 orang'],
                                    ['Guide book dasar pertanian', 'Ya', 'Ya', 'Ya'],
                                    [
                                        'Konsultasi live chat',
                                        'Edukatif 2x24 jam (antrian)',
                                        'Interaktif + historical live chat',
                                        'Fast direct konsultasi prioritas',
                                    ],
                                    ['Akses artikel & informasi harian', 'Ya', 'Ya', 'Ya'],
                                    ['Live Session Klinik Tanam', '-', '2x / bulan', 'Weekly privat live session'],
                                    ['Strategi musim tanam', '-', 'ya', 'ya'],
                                    ['Analisa bisnis personal + highlight promosi', '-', '-', 'ya'],
                                ]"
                                :key="i"
                                :class="['hover:bg-gray-50', i % 2 === 0 ? '' : 'bg-gray-50/50']"
                            >
                                <td class="px-6 py-3.5 text-left text-gray-700">{{ row[0] }}</td>
                                <td class="px-6 py-3.5 text-center text-gray-600">{{ row[1] }}</td>
                                <td class="bg-green-50/50 px-6 py-3.5 text-center text-gray-700">{{ row[2] }}</td>
                                <td class="px-6 py-3.5 text-center text-gray-600">{{ row[3] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-10">
                    <Button as-child size="lg" class="bg-[#2E7D32] text-white hover:bg-[#256427]">
                        <Link :href="membershipCtaHref" class="flex items-center gap-2"> Daftar & Pilih Paket <ArrowRight class="h-5 w-5" /> </Link>
                    </Button>
                    <p class="mt-3 text-sm text-gray-400">Skema angkatan kuota terbatas: Basic 30, Pro 20, Elite 5 peserta.</p>
                </div>
            </div>
        </section>

        <section class="bg-[#f7fbf7] py-14 lg:py-20">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mb-7 text-center">
                    <p class="inline-flex items-center gap-2 rounded-full bg-[#E8F5E9] px-3 py-1 text-xs font-semibold text-[#2E7D32]">
                        <Sparkles class="h-3.5 w-3.5" />
                        Mitra Strategis
                    </p>
                    <h2 class="mt-3 text-2xl font-extrabold text-gray-900 lg:text-3xl">Didukung oleh Mitra Strategis</h2>
                </div>

                <div class="space-y-4 overflow-hidden rounded-2xl border border-green-100 bg-white p-4 md:p-6">
                    <div class="sponsor-track-wrapper">
                        <div class="sponsor-track sponsor-track-right">
                            <div v-for="sponsor in duplicatedTop" :key="`top-${sponsor.name}-${sponsor.initials}`" class="sponsor-item">
                                <div v-if="sponsor.logo" class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-100">
                                    <img :src="sponsor.logo" :alt="sponsor.name" class="h-6 w-6 object-contain" />
                                </div>
                                <span v-else class="sponsor-badge">{{ sponsor.initials }}</span>
                                <span class="font-semibold text-gray-700">{{ sponsor.name }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="sponsor-track-wrapper">
                        <div class="sponsor-track sponsor-track-left">
                            <div v-for="sponsor in duplicatedBottom" :key="`bottom-${sponsor.name}-${sponsor.initials}`" class="sponsor-item">
                                <span class="sponsor-badge">{{ sponsor.initials }}</span>
                                <span class="font-semibold text-gray-700">{{ sponsor.name }}</span>
                            </div>
                        </div>
                    </div> -->
                </div>

                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <Button as-child size="lg" class="bg-[#2E7D32] text-white hover:bg-[#256427]">
                        <Link :href="membershipCtaHref">Mulai Jadi Member</Link>
                    </Button>
                    <Button as-child variant="outline" size="lg" class="border-[#2E7D32] text-[#2E7D32] hover:bg-[#2E7D32]/10">
                        <Link href="/products">Lihat Produk Pilihan</Link>
                    </Button>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>

<style scoped>
.sponsor-track-wrapper {
    overflow: hidden;
    position: relative;
}

.sponsor-track {
    display: flex;
    width: max-content;
    gap: 0.75rem;
}

.sponsor-track-right {
    animation: marquee-right 28s linear infinite;
}

.sponsor-track-left {
    animation: marquee-left 28s linear infinite;
}

.sponsor-item {
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    border: 1px solid #dcefe0;
    border-radius: 9999px;
    background: #f9fdf9;
    padding: 0.5rem 0.85rem;
    white-space: nowrap;
}

.sponsor-badge {
    display: inline-flex;
    width: 1.85rem;
    height: 1.85rem;
    align-items: center;
    justify-content: center;
    border-radius: 9999px;
    background: #2e7d32;
    color: #ffffff;
    font-size: 0.7rem;
    font-weight: 700;
}

@keyframes marquee-right {
    from {
        transform: translateX(-50%);
    }

    to {
        transform: translateX(0%);
    }
}

@keyframes marquee-left {
    from {
        transform: translateX(0%);
    }

    to {
        transform: translateX(-50%);
    }
}
</style>
