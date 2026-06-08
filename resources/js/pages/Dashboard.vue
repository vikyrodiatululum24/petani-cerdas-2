<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Calendar, CreditCard, FileText, Package, Users, Wallet } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

defineProps<{
    summary: {
        users_total: number;
        articles_published: number;
        products_total: number;
        subscriptions_active: number;
        subscriptions_pending: number;
        payments_pending: number;
        payments_completed_amount: number;
        live_sessions_upcoming: number;
    };
    recentSubscriptions: Array<{
        id: number;
        user_name: string | null;
        user_email: string | null;
        plan_name: string | null;
        status: string;
        created_at: string | null;
    }>;
    recentPayments: Array<{
        id: number;
        transaction_id: string | null;
        user_name: string | null;
        amount: number;
        status: string;
        created_at: string | null;
    }>;
    upcomingLiveSessions: Array<{
        id: number;
        title: string;
        mentor_name: string | null;
        status: string;
        scheduled_at: string | null;
    }>;
    userInfo: {
        name: string | null;
        roles: string[];
    };
}>();

const formatNumber = (value: number): string => {
    return new Intl.NumberFormat('id-ID').format(value);
};

const formatCurrency = (value: number): string => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(value);
};

const formatDateTime = (value: string | null): string => {
    if (!value) {
        return '-';
    }

    return new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(value));
};

const statusClass = (status: string): string => {
    const map: Record<string, string> = {
        active: 'bg-emerald-50 text-emerald-700 border-emerald-200',
        pending: 'bg-amber-50 text-amber-700 border-amber-200',
        completed: 'bg-emerald-50 text-emerald-700 border-emerald-200',
        failed: 'bg-rose-50 text-rose-700 border-rose-200',
        canceled: 'bg-gray-100 text-gray-700 border-gray-200',
        scheduled: 'bg-sky-50 text-sky-700 border-sky-200',
        live: 'bg-violet-50 text-violet-700 border-violet-200',
    };

    return map[status] ?? 'bg-gray-100 text-gray-700 border-gray-200';
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <div class="rounded-2xl border bg-gradient-to-r from-emerald-50 to-white p-6">
                <h1 class="text-xl font-bold text-gray-900 md:text-2xl">Dashboard Operasional</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Selamat datang, {{ userInfo.name || 'Tim Petani Cerdas' }}
                    <span v-if="userInfo.roles.length" class="font-medium text-emerald-700">({{ userInfo.roles.join(', ') }})</span>
                </p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl border bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-500">Total Pengguna</p>
                        <Users class="h-5 w-5 text-emerald-600" />
                    </div>
                    <p class="mt-3 text-2xl font-bold text-gray-900">{{ formatNumber(summary.users_total) }}</p>
                </div>

                <div class="rounded-2xl border bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-500">Artikel Terbit</p>
                        <FileText class="h-5 w-5 text-emerald-600" />
                    </div>
                    <p class="mt-3 text-2xl font-bold text-gray-900">{{ formatNumber(summary.articles_published) }}</p>
                </div>

                <div class="rounded-2xl border bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-500">Produk Terdaftar</p>
                        <Package class="h-5 w-5 text-emerald-600" />
                    </div>
                    <p class="mt-3 text-2xl font-bold text-gray-900">{{ formatNumber(summary.products_total) }}</p>
                </div>

                <div class="rounded-2xl border bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-500">Revenue Terkonfirmasi</p>
                        <Wallet class="h-5 w-5 text-emerald-600" />
                    </div>
                    <p class="mt-3 text-2xl font-bold text-gray-900">{{ formatCurrency(summary.payments_completed_amount) }}</p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl border bg-white p-5">
                    <p class="text-sm text-gray-500">Subscription Aktif</p>
                    <p class="mt-2 text-xl font-bold text-gray-900">{{ formatNumber(summary.subscriptions_active) }}</p>
                </div>
                <div class="rounded-2xl border bg-white p-5">
                    <p class="text-sm text-gray-500">Subscription Pending</p>
                    <p class="mt-2 text-xl font-bold text-amber-700">{{ formatNumber(summary.subscriptions_pending) }}</p>
                </div>
                <div class="rounded-2xl border bg-white p-5">
                    <p class="text-sm text-gray-500">Pembayaran Pending</p>
                    <p class="mt-2 text-xl font-bold text-amber-700">{{ formatNumber(summary.payments_pending) }}</p>
                </div>
                <div class="rounded-2xl border bg-white p-5">
                    <p class="text-sm text-gray-500">Live Session Mendatang</p>
                    <p class="mt-2 text-xl font-bold text-sky-700">{{ formatNumber(summary.live_sessions_upcoming) }}</p>
                </div>
            </div>

            <div class="grid gap-4 xl:grid-cols-3">
                <div class="rounded-2xl border bg-white p-5 xl:col-span-1">
                    <div class="mb-4 flex items-center gap-2">
                        <Calendar class="h-4 w-4 text-emerald-600" />
                        <h2 class="text-sm font-semibold text-gray-900">Live Session Mendatang</h2>
                    </div>
                    <div class="space-y-3">
                        <div v-if="upcomingLiveSessions.length === 0" class="rounded-lg bg-gray-50 p-3 text-sm text-gray-500">
                            Belum ada sesi live mendatang.
                        </div>
                        <div v-for="session in upcomingLiveSessions" :key="session.id" class="rounded-lg border p-3">
                            <p class="text-sm font-semibold text-gray-900">{{ session.title }}</p>
                            <p class="mt-1 text-xs text-gray-500">Mentor: {{ session.mentor_name || '-' }}</p>
                            <div class="mt-2 flex items-center justify-between">
                                <span :class="['rounded-full border px-2 py-0.5 text-[11px] font-semibold', statusClass(session.status)]">{{
                                    session.status
                                }}</span>
                                <span class="text-xs text-gray-500">{{ formatDateTime(session.scheduled_at) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border bg-white p-5 xl:col-span-1">
                    <div class="mb-4 flex items-center gap-2">
                        <Users class="h-4 w-4 text-emerald-600" />
                        <h2 class="text-sm font-semibold text-gray-900">Subscription Terbaru</h2>
                    </div>
                    <div class="space-y-3">
                        <div v-if="recentSubscriptions.length === 0" class="rounded-lg bg-gray-50 p-3 text-sm text-gray-500">
                            Belum ada data subscription.
                        </div>
                        <div v-for="item in recentSubscriptions" :key="item.id" class="rounded-lg border p-3">
                            <p class="text-sm font-semibold text-gray-900">{{ item.user_name || '-' }}</p>
                            <p class="text-xs text-gray-500">{{ item.user_email || '-' }}</p>
                            <div class="mt-2 flex items-center justify-between">
                                <span class="text-xs text-gray-600">{{ item.plan_name || '-' }}</span>
                                <span :class="['rounded-full border px-2 py-0.5 text-[11px] font-semibold', statusClass(item.status)]">{{
                                    item.status
                                }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border bg-white p-5 xl:col-span-1">
                    <div class="mb-4 flex items-center gap-2">
                        <CreditCard class="h-4 w-4 text-emerald-600" />
                        <h2 class="text-sm font-semibold text-gray-900">Pembayaran Terbaru</h2>
                    </div>
                    <div class="space-y-3">
                        <div v-if="recentPayments.length === 0" class="rounded-lg bg-gray-50 p-3 text-sm text-gray-500">
                            Belum ada data pembayaran.
                        </div>
                        <div v-for="payment in recentPayments" :key="payment.id" class="rounded-lg border p-3">
                            <p class="text-sm font-semibold text-gray-900">{{ payment.user_name || '-' }}</p>
                            <p class="text-xs text-gray-500">{{ payment.transaction_id || '-' }}</p>
                            <div class="mt-2 flex items-center justify-between">
                                <span class="text-xs font-medium text-gray-700">{{ formatCurrency(payment.amount) }}</span>
                                <span :class="['rounded-full border px-2 py-0.5 text-[11px] font-semibold', statusClass(payment.status)]">{{
                                    payment.status
                                }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
