<script setup lang="ts">
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { CalendarClock, CreditCard, MessageSquareText, Sprout, UserRound, Video } from 'lucide-vue-next';

interface PetaniProfile {
    phone: string | null;
    main_crop: string | null;
    land_type: string | null;
    province: string | null;
    city: string | null;
    district: string | null;
    village: string | null;
    postal_code: string | null;
    address: string | null;
    photo_url: string | null;
}

interface Petani {
    id: number;
    name: string;
    email: string;
    created_at: string | null;
    profile: PetaniProfile;
}

interface Summary {
    subscriptions_total: number;
    subscriptions_active: number;
    payments_total: number;
    chats_total: number;
    live_sessions_total: number;
}

interface SubscriptionItem {
    id: number;
    generation: string | null;
    status: string;
    start_date: string | null;
    end_date: string | null;
    plan_name: string | null;
    plan_price: number | null;
    plan_duration_months: number | null;
    approved_by: string | null;
}

interface PaymentItem {
    id: number;
    transaction_id: string;
    amount: number;
    status: string;
    paid_at: string | null;
    plan_name: string | null;
    proof_of_payment_url: string | null;
}

interface ChatThreadItem {
    id: number;
    title: string | null;
    status: string | null;
    counterpart_name: string | null;
    mentor_name: string | null;
    total_messages: number;
    updated_at: string | null;
}

interface LiveSessionItem {
    id: number;
    title: string;
    status: string;
    scheduled_at: string | null;
    plan_name: string | null;
    stream_url: string | null;
}

const props = defineProps<{
    petani: Petani;
    summary: Summary;
    subscriptions: SubscriptionItem[];
    payments: PaymentItem[];
    chatThreads: ChatThreadItem[];
    liveSessions: LiveSessionItem[];
}>();

const formatCurrency = (amount: number | null) => {
    if (amount === null) {
        return '-';
    }

    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(amount);
};

const statusBadgeClass = (status: string | null) => {
    if (status === 'active' || status === 'completed' || status === 'live') {
        return 'border-emerald-200 bg-emerald-50 text-emerald-700';
    }

    if (status === 'pending' || status === 'scheduled') {
        return 'border-amber-200 bg-amber-50 text-amber-700';
    }

    if (status === 'canceled' || status === 'failed' || status === 'ended' || status === 'inactive') {
        return 'border-red-200 bg-red-50 text-red-700';
    }

    return 'border-gray-200 bg-gray-50 text-gray-700';
};
</script>

<template>
    <Head title="Profil Petani" />

    <PublicLayout>
        <section class="bg-gradient-to-br from-[#1b5e20] via-[#2E7D32] to-[#66BB6A] py-14 text-white lg:py-16">
            <div class="mx-auto flex max-w-7xl flex-col gap-6 px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                <div class="flex items-center gap-4">
                    <div class="h-16 w-16 overflow-hidden rounded-full border-2 border-white/40 bg-white/20">
                        <img v-if="petani.profile.photo_url" :src="petani.profile.photo_url" alt="Foto profil" class="h-full w-full object-cover" />
                        <div v-else class="flex h-full w-full items-center justify-center">
                            <UserRound class="h-8 w-8" />
                        </div>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-green-100">Profil Petani</p>
                        <h1 class="mt-1 text-3xl font-black tracking-tight">{{ petani.name }}</h1>
                        <p class="mt-1 text-sm text-green-50">{{ petani.email }}</p>
                    </div>
                </div>
                <Button as-child variant="outline" class="border-white/40 bg-white/15 text-white hover:bg-white/25">
                    <Link :href="route('petani.profile.edit')">Edit Profil</Link>
                </Button>
            </div>
        </section>

        <section class="bg-[#f4f8f3] py-10">
            <div class="mx-auto grid max-w-7xl grid-cols-2 gap-4 px-6 sm:grid-cols-3 lg:grid-cols-5 lg:px-8">
                <div class="rounded-2xl border border-emerald-200 bg-white p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Total Paket</p>
                    <p class="mt-2 text-2xl font-black text-gray-900">{{ summary.subscriptions_total }}</p>
                </div>
                <div class="rounded-2xl border border-blue-200 bg-white p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Paket Aktif</p>
                    <p class="mt-2 text-2xl font-black text-gray-900">{{ summary.subscriptions_active }}</p>
                </div>
                <div class="rounded-2xl border border-amber-200 bg-white p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Pembayaran</p>
                    <p class="mt-2 text-2xl font-black text-gray-900">{{ summary.payments_total }}</p>
                </div>
                <div class="rounded-2xl border border-purple-200 bg-white p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-purple-700">Topik Chat</p>
                    <p class="mt-2 text-2xl font-black text-gray-900">{{ summary.chats_total }}</p>
                </div>
                <div class="rounded-2xl border border-pink-200 bg-white p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-pink-700">Live Session</p>
                    <p class="mt-2 text-2xl font-black text-gray-900">{{ summary.live_sessions_total }}</p>
                </div>
            </div>
        </section>

        <section class="bg-white pb-16">
            <div class="mx-auto space-y-6 px-6 pt-6 lg:max-w-7xl lg:px-8">
                <div class="rounded-2xl border border-gray-200 bg-white p-6">
                    <h2 class="text-lg font-bold text-gray-900">Data Pribadi dan Lahan</h2>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <div><p class="text-xs text-gray-500">No. Telepon</p><p class="text-sm font-semibold text-gray-800">{{ petani.profile.phone || '-' }}</p></div>
                        <div><p class="text-xs text-gray-500">Komoditas Utama</p><p class="text-sm font-semibold text-gray-800">{{ petani.profile.main_crop || '-' }}</p></div>
                        <div><p class="text-xs text-gray-500">Jenis Lahan</p><p class="text-sm font-semibold text-gray-800">{{ petani.profile.land_type || '-' }}</p></div>
                        <div><p class="text-xs text-gray-500">Provinsi</p><p class="text-sm font-semibold text-gray-800">{{ petani.profile.province || '-' }}</p></div>
                        <div><p class="text-xs text-gray-500">Kota</p><p class="text-sm font-semibold text-gray-800">{{ petani.profile.city || '-' }}</p></div>
                        <div><p class="text-xs text-gray-500">Kecamatan</p><p class="text-sm font-semibold text-gray-800">{{ petani.profile.district || '-' }}</p></div>
                        <div><p class="text-xs text-gray-500">Desa</p><p class="text-sm font-semibold text-gray-800">{{ petani.profile.village || '-' }}</p></div>
                        <div><p class="text-xs text-gray-500">Kode Pos</p><p class="text-sm font-semibold text-gray-800">{{ petani.profile.postal_code || '-' }}</p></div>
                        <div><p class="text-xs text-gray-500">Bergabung Sejak</p><p class="text-sm font-semibold text-gray-800">{{ petani.created_at || '-' }}</p></div>
                    </div>
                    <div class="mt-4">
                        <p class="text-xs text-gray-500">Alamat</p>
                        <p class="text-sm font-semibold text-gray-800">{{ petani.profile.address || '-' }}</p>
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="rounded-2xl border border-gray-200 bg-white p-6">
                        <h3 class="inline-flex items-center gap-2 text-base font-bold text-gray-900"><Sprout class="h-4 w-4 text-emerald-600" /> Riwayat Subscription</h3>
                        <div class="mt-4 space-y-3">
                            <div v-if="subscriptions.length === 0" class="text-sm text-gray-500">Belum ada data subscription.</div>
                            <div v-for="item in subscriptions" :key="item.id" class="rounded-xl border border-gray-200 p-3">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="font-semibold text-gray-900">{{ item.plan_name || '-' }} • Gen {{ item.generation || '-' }}</p>
                                    <span :class="['rounded-full border px-2.5 py-1 text-xs font-semibold', statusBadgeClass(item.status)]">{{ item.status }}</span>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">{{ item.start_date || '-' }} s/d {{ item.end_date || '-' }}</p>
                                <p class="text-xs text-gray-500">Disetujui: {{ item.approved_by || '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white p-6">
                        <h3 class="inline-flex items-center gap-2 text-base font-bold text-gray-900"><CreditCard class="h-4 w-4 text-emerald-600" /> Pembayaran Terakhir</h3>
                        <div class="mt-4 space-y-3">
                            <div v-if="payments.length === 0" class="text-sm text-gray-500">Belum ada data pembayaran.</div>
                            <div v-for="payment in payments" :key="payment.id" class="rounded-xl border border-gray-200 p-3">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="font-semibold text-gray-900">{{ payment.transaction_id }}</p>
                                    <span :class="['rounded-full border px-2.5 py-1 text-xs font-semibold', statusBadgeClass(payment.status)]">{{ payment.status }}</span>
                                </div>
                                <p class="mt-1 text-xs text-gray-600">{{ payment.plan_name || '-' }} • {{ formatCurrency(payment.amount) }}</p>
                                <p class="text-xs text-gray-500">{{ payment.paid_at || '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="rounded-2xl border border-gray-200 bg-white p-6">
                        <h3 class="inline-flex items-center gap-2 text-base font-bold text-gray-900"><MessageSquareText class="h-4 w-4 text-emerald-600" /> Aktivitas Chat</h3>
                        <div class="mt-4 space-y-3">
                            <div v-if="chatThreads.length === 0" class="text-sm text-gray-500">Belum ada topik chat.</div>
                            <div v-for="thread in chatThreads" :key="thread.id" class="rounded-xl border border-gray-200 p-3">
                                <p class="line-clamp-1 font-semibold text-gray-900">{{ thread.title || '-' }}</p>
                                <p class="mt-1 text-xs text-gray-600">Lawan bicara: {{ thread.counterpart_name || '-' }} • Mentor: {{ thread.mentor_name || '-' }}</p>
                                <p class="text-xs text-gray-500">{{ thread.total_messages }} pesan • Update {{ thread.updated_at || '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white p-6">
                        <h3 class="inline-flex items-center gap-2 text-base font-bold text-gray-900"><CalendarClock class="h-4 w-4 text-emerald-600" /> Riwayat Live Session</h3>
                        <div class="mt-4 space-y-3">
                            <div v-if="liveSessions.length === 0" class="text-sm text-gray-500">Belum ada jadwal live session.</div>
                            <div v-for="session in liveSessions" :key="session.id" class="rounded-xl border border-gray-200 p-3">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="line-clamp-1 font-semibold text-gray-900">{{ session.title }}</p>
                                    <span :class="['rounded-full border px-2.5 py-1 text-xs font-semibold', statusBadgeClass(session.status)]">{{ session.status }}</span>
                                </div>
                                <p class="mt-1 text-xs text-gray-600">{{ session.plan_name || '-' }}</p>
                                <p class="text-xs text-gray-500">{{ session.scheduled_at || '-' }}</p>
                                <a v-if="session.stream_url" :href="session.stream_url" target="_blank" rel="noopener noreferrer" class="mt-1 inline-flex text-xs font-semibold text-emerald-700 hover:text-emerald-900">Buka Link Sesi</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
