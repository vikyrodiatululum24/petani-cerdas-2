<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import { AlertCircle, ArrowLeft, CheckCircle2, XCircle } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Plan {
    id: number;
    name: string;
    key: 'basic' | 'premium' | 'elite' | null;
    price: number;
}

interface Subscription {
    id: number;
    status: string;
    generation: string;
    plan: Plan;
}

interface User {
    id: number;
    name: string;
    email: string;
    phone: string | null;
}

interface Payment {
    id: number;
    transaction_id: string;
    amount: number;
    status: string;
    payment_method: string;
    destination_account: string;
    sender_name: string;
    sender_bank: string | null;
    paid_at: string;
    notes: string | null;
    proof_of_payment_path: string | null;
    created_at: string;
    subscription: Subscription;
    user: User;
}

interface Props {
    payment: Payment;
}

const props = defineProps<Props>();

const showApproveForm = ref(false);
const showRejectForm = ref(false);

const approveForm = useForm({
    start_date: '',
    end_date: '',
    generation: '',
});

const rejectForm = useForm({
    reason: '',
});

const planBadgeColor = computed(() => (key: 'basic' | 'premium' | 'elite' | null) => {
    const colors: Record<string, string> = {
        basic: 'bg-emerald-100 text-emerald-800',
        premium: 'bg-sky-100 text-sky-800',
        elite: 'bg-amber-100 text-amber-800',
    };
    return key ? colors[key] || 'bg-gray-100 text-gray-800' : 'bg-gray-100 text-gray-800';
});

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

const handleApprove = () => {
    approveForm.post(route('admin.subscriptions.payments.approve', { payment: props.payment.id }));
};

const handleReject = () => {
    rejectForm.post(route('admin.subscriptions.payments.reject', { payment: props.payment.id }));
};
</script>

<template>
    <AppLayout title="Detail Pembayaran Subscription">
        <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Back Button -->
            <button
                @click="router.visit(route('admin.subscriptions.payments.index'))"
                class="mb-8 flex items-center gap-2 text-blue-600 transition-colors hover:text-blue-800"
            >
                <ArrowLeft class="h-5 w-5" />
                <span>Kembali ke Daftar</span>
            </button>

            <!-- Header -->
            <div class="mb-8">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Detail Pembayaran</h1>
                        <p class="mt-1 text-gray-600">{{ payment.transaction_id }}</p>
                    </div>
                    <div
                        class="rounded-full px-4 py-2 text-sm font-semibold"
                        :class="[payment.status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800']"
                    >
                        {{ payment.status === 'pending' ? 'Menunggu Verifikasi' : payment.status }}
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Main Content -->
                <div class="space-y-8 lg:col-span-2">
                    <!-- Payment Information -->
                    <div class="rounded-lg border border-gray-200 bg-white p-6">
                        <h2 class="mb-6 text-lg font-semibold text-gray-900">Informasi Pembayaran</h2>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="mb-1 text-sm text-gray-600">Nama Pengirim</p>
                                <p class="text-base font-medium text-gray-900">{{ payment.sender_name }}</p>
                            </div>
                            <div>
                                <p class="mb-1 text-sm text-gray-600">Bank Pengirim</p>
                                <p class="text-base font-medium text-gray-900">
                                    {{ payment.sender_bank || 'Tidak tercatat' }}
                                </p>
                            </div>
                            <div>
                                <p class="mb-1 text-sm text-gray-600">Nama Rekening Tujuan</p>
                                <p class="text-base font-medium text-gray-900">{{ payment.destination_account }}</p>
                            </div>
                            <div>
                                <p class="mb-1 text-sm text-gray-600">Nominal Pembayaran</p>
                                <p class="text-lg font-semibold text-gray-900">{{ formatCurrency(payment.amount) }}</p>
                            </div>
                            <div>
                                <p class="mb-1 text-sm text-gray-600">Metode Pembayaran</p>
                                <p class="text-base font-medium text-gray-900">{{ payment.payment_method }}</p>
                            </div>
                            <div>
                                <p class="mb-1 text-sm text-gray-600">Tanggal Pembayaran</p>
                                <p class="text-base font-medium text-gray-900">{{ formatDate(payment.paid_at) }}</p>
                            </div>
                        </div>

                        <div v-if="payment.notes" class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-4">
                            <p class="mb-2 text-sm text-gray-600">Keterangan</p>
                            <p class="text-sm text-gray-900">{{ payment.notes }}</p>
                        </div>
                    </div>

                    <!-- Petani Information -->
                    <div class="rounded-lg border border-gray-200 bg-white p-6">
                        <h2 class="mb-6 text-lg font-semibold text-gray-900">Data Petani</h2>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="mb-1 text-sm text-gray-600">Nama</p>
                                <p class="text-base font-medium text-gray-900">{{ payment.user.name }}</p>
                            </div>
                            <div>
                                <p class="mb-1 text-sm text-gray-600">Email</p>
                                <p class="text-base font-medium text-gray-900">{{ payment.user.email }}</p>
                            </div>
                            <div>
                                <p class="mb-1 text-sm text-gray-600">Nomor Telepon</p>
                                <p class="text-base font-medium text-gray-900">{{ payment.user.phone || '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Package Information -->
                    <div class="rounded-lg border border-gray-200 bg-white p-6">
                        <h2 class="mb-6 text-lg font-semibold text-gray-900">Paket Subscription</h2>

                        <div class="flex items-center justify-between">
                            <div>
                                <p class="mb-2 text-sm text-gray-600">Paket yang Dibeli</p>
                                <span
                                    :class="[
                                        'inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold',
                                        planBadgeColor(payment.subscription.plan.key),
                                    ]"
                                >
                                    {{ payment.subscription.plan.name }}
                                </span>
                            </div>
                            <div>
                                <p class="mb-1 text-sm text-gray-600">Harga Paket</p>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ formatCurrency(payment.subscription.plan.price) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Bukti Pembayaran -->
                    <div v-if="payment.proof_of_payment_path" class="rounded-lg border border-gray-200 bg-white p-6">
                        <h2 class="mb-6 text-lg font-semibold text-gray-900">Bukti Pembayaran</h2>
                        <img
                            :src="payment.proof_of_payment_path"
                            :alt="'Bukti Pembayaran ' + payment.transaction_id"
                            class="max-h-96 w-full rounded-lg border border-gray-200 object-cover"
                        />
                    </div>
                </div>

                <!-- Sidebar Actions -->
                <div class="lg:col-span-1">
                    <div class="sticky top-8 space-y-6">
                        <!-- Approve Form -->
                        <div v-if="!showApproveForm && !showRejectForm" class="space-y-4 rounded-lg border border-gray-200 bg-white p-6">
                            <Button
                                @click="showApproveForm = true"
                                class="flex w-full items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700"
                            >
                                <CheckCircle2 class="h-5 w-5" />
                                <span>Setujui Pembayaran</span>
                            </Button>
                            <Button @click="showRejectForm = true" variant="destructive" class="flex w-full items-center justify-center gap-2">
                                <XCircle class="h-5 w-5" />
                                <span>Tolak Pembayaran</span>
                            </Button>
                        </div>

                        <!-- Approve Confirmation -->
                        <form
                            v-if="showApproveForm"
                            @submit.prevent="handleApprove"
                            class="space-y-6 rounded-lg border border-emerald-200 bg-emerald-50 p-6"
                        >
                            <div class="flex items-start gap-3">
                                <AlertCircle class="mt-0.5 h-5 w-5 flex-shrink-0 text-emerald-600" />
                                <p class="text-sm text-emerald-800">Lengkapi detail subscription sebelum menyetujui pembayaran.</p>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-900">Tanggal Mulai<span class="text-red-500">*</span></label>
                                <Input v-model="approveForm.start_date" type="date" :error="!!approveForm.errors.start_date" />
                                <InputError :message="approveForm.errors.start_date" />
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-900">Tanggal Akhir<span class="text-red-500">*</span></label>
                                <Input v-model="approveForm.end_date" type="date" :error="!!approveForm.errors.end_date" />
                                <InputError :message="approveForm.errors.end_date" />
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-900">Generation<span class="text-red-500">*</span></label>
                                <Input
                                    v-model="approveForm.generation"
                                    type="number"
                                    placeholder="Contoh: 202603"
                                    :error="!!approveForm.errors.generation"
                                />
                                <InputError :message="approveForm.errors.generation" />
                            </div>

                            <div class="flex gap-3">
                                <Button type="button" variant="outline" @click="showApproveForm = false" class="flex-1"> Batal </Button>
                                <Button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700" :disabled="approveForm.processing">
                                    {{ approveForm.processing ? 'Memproses...' : 'Setujui' }}
                                </Button>
                            </div>
                        </form>

                        <!-- Reject Confirmation -->
                        <form v-if="showRejectForm" @submit.prevent="handleReject" class="space-y-6 rounded-lg border border-red-200 bg-red-50 p-6">
                            <div class="flex items-start gap-3">
                                <AlertCircle class="mt-0.5 h-5 w-5 flex-shrink-0 text-red-600" />
                                <p class="text-sm text-red-800">Pembayaran akan ditandai sebagai ditolak.</p>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-900">Alasan Penolakan</label>
                                <textarea
                                    v-model="rejectForm.reason"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500"
                                    rows="3"
                                    placeholder="Opsional: Jelaskan alasan penolakan pembayaran"
                                ></textarea>
                                <InputError :message="rejectForm.errors.reason" />
                            </div>

                            <div class="flex gap-3">
                                <Button type="button" variant="outline" @click="showRejectForm = false" class="flex-1"> Batal </Button>
                                <Button type="submit" variant="destructive" class="flex-1" :disabled="rejectForm.processing">
                                    {{ rejectForm.processing ? 'Memproses...' : 'Tolak' }}
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
