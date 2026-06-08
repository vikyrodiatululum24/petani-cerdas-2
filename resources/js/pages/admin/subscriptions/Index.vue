<script setup lang="ts">
    import {
        computed,
        ref
    } from 'vue';
    import {
        Head,
        Link,
        router
    } from '@inertiajs/vue3';
    import AppLayout from '@/layouts/AppLayout.vue';
    import {
        Button
    } from '@/components/ui/button';
    import {
        ChevronRight,
        CheckCircle2,
        XCircle
    } from 'lucide-vue-next';
import { BreadcrumbItem } from '@/types';

    interface SubscriptionPlan {
        id: number;
        name: string;
        key: 'basic' | 'premium' | 'elite' | null;
    }

    interface Subscription {
        id: number;
        status: string;
        plan: SubscriptionPlan;
    }

    interface User {
        id: number;
        name: string;
        email: string;
    }

    interface Payment {
        id: number;
        transaction_id: string;
        amount: number;
        status: string;
        paid_at: string;
        created_at: string;
        subscription: Subscription;
        user: User;
    }

    interface Props {
        payments: Payment[];
    }

    const props = defineProps < Props > ();

    const selectedPayment = ref < Payment | null > (null);

    // const planBadgeColor = computed(() => (code: string) => {
    //   const colors: Record<string, string> = {
    //     basic: 'bg-emerald-100 text-emerald-800',
    //     premium: 'bg-sky-100 text-sky-800',
    //     elite: 'bg-amber-100 text-amber-800',
    //   };
    //   return colors[code] || 'bg-gray-100 text-gray-800';
    // });

    const formatCurrency = (amount: number) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(amount);
    };

    const formatDate = (date: string) => {
        return new Intl.DateTimeFormat('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        }).format(new Date(date));
    };

    const handleViewDetail = (payment: Payment) => {
        router.get(route('admin.subscriptions.payments.show', {
            payment: payment.id
        }));
    };

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Review Pembayaran Subscription',
            href: '/admin/subscriptions/payments',
        },
    ];
</script>

<template>
    <Head title="Review Pembayaran Subscription" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Review Pembayaran Subscription</h1>
                <p class="text-gray-600 mt-2">Tinjau dan proses pembayaran subscription yang masuk</p>
            </div>

            <!-- Empty State -->
            <div v-if="payments.length === 0" class="rounded-lg border-2 border-dashed border-gray-300 p-12 text-center">
                <CheckCircle2 class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak ada pembayaran yang tertunda</h3>
                <p class="mt-2 text-gray-600">Semua pembayaran subscription telah diproses.</p>
            </div>

            <!-- Payments Table -->
            <div v-else class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Transaksi</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Petani</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Paket</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Nominal</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Tanggal Bayar</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="payment in payments" :key="payment.id"
                            class="hover:bg-gray-50 transition-colors cursor-pointer"
                            @click="handleViewDetail(payment)">
                            <td class="px-6 py-4 text-sm font-mono text-gray-700">{{ payment . transaction_id }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ payment . user . name }}</div>
                                <div class="text-xs text-gray-500">{{ payment . user . email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span>
                                    {{ payment . subscription . plan . name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                {{ formatCurrency(payment . amount) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ formatDate(payment . paid_at) }}</td>
                            <td class="px-6 py-4 text-right">
                                <Button variant="outline" size="sm" class="flex items-center gap-2"
                                    @click.stop="handleViewDetail(payment)">
                                    <span>Tinjau</span>
                                    <ChevronRight class="h-4 w-4" />
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
