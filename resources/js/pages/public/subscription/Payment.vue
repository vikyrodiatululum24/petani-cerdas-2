<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, BadgeCheck, FileCheck2, Landmark, LoaderCircle, UploadCloud, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface DestinationAccount {
    key: string;
    bank_name: string;
    account_number: string;
    account_holder: string;
}

interface SubscriptionPayload {
    id: number;
    generation: string | null;
    status: string;
    plan: {
        name: string;
        duration_months: number;
        price: number;
        discount_price: number | null;
        final_amount: number;
    };
}

const props = defineProps<{
    subscription: SubscriptionPayload;
    destinationAccounts: DestinationAccount[];
    defaultPaymentMethod: string;
}>();

const form = useForm({
    payment_method: props.defaultPaymentMethod,
    destination_account: props.destinationAccounts[0]?.key ?? '',
    sender_name: '',
    notes: '',
    payment_proof: null as File | null,
});

const paymentProofInput = ref<HTMLInputElement | null>(null);

const selectedProofName = computed(() => form.payment_proof?.name ?? 'Belum ada file dipilih');
const selectedProofSize = computed(() => {
    if (!form.payment_proof) {
        return null;
    }

    const sizeInKb = form.payment_proof.size / 1024;
    if (sizeInKb < 1024) {
        return `${sizeInKb.toFixed(1)} KB`;
    }

    return `${(sizeInKb / 1024).toFixed(2)} MB`;
});

const triggerPaymentProofPicker = () => {
    paymentProofInput.value?.click();
};

const clearPaymentProof = () => {
    form.payment_proof = null;
    if (paymentProofInput.value) {
        paymentProofInput.value.value = '';
    }
};

const onPaymentProofChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    form.payment_proof = target.files?.[0] ?? null;
};

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(value);
};

const submitPayment = () => {
    form.post(route('petani.subscriptions.payments.store', { subscription: props.subscription.id }), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Pembayaran Subscription" />

    <PublicLayout>
        <section class="bg-gradient-to-b from-[#f5fbf6] to-[#ffffff] py-10 md:py-14">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                <Link :href="route('petani.subscriptions.packages.index')" class="inline-flex items-center gap-2 text-sm font-semibold text-[#2E7D32]">
                    <ArrowLeft class="h-4 w-4" /> Kembali ke Pilihan Paket
                </Link>

                <div class="mt-4 grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h1 class="text-2xl font-extrabold text-gray-900">Pembayaran Paket {{ subscription.plan.name }}</h1>
                        <p class="mt-2 text-sm text-gray-500">Pilih rekening tujuan transfer, lalu isi data pengirim pembayaran Anda.</p>

                        <form class="mt-6 space-y-5" @submit.prevent="submitPayment">
                            <div>
                                <Label class="text-sm font-semibold text-gray-700">Rekening Tujuan</Label>
                                <div class="mt-3 space-y-3">
                                    <label
                                        v-for="account in destinationAccounts"
                                        :key="account.key"
                                        class="flex cursor-pointer items-start gap-3 rounded-xl border p-3"
                                        :class="form.destination_account === account.key ? 'border-[#2E7D32] bg-emerald-50' : 'border-gray-200 bg-white'"
                                    >
                                        <input v-model="form.destination_account" type="radio" :value="account.key" class="mt-1 h-4 w-4" />
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ account.bank_name }} - {{ account.account_number }}</p>
                                            <p class="text-sm text-gray-500">a.n {{ account.account_holder }}</p>
                                        </div>
                                    </label>
                                </div>
                                <InputError :message="form.errors.destination_account" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="sender_name">Nama Pengirim</Label>
                                <Input id="sender_name" v-model="form.sender_name" placeholder="Contoh: Budi Santoso" />
                                <InputError :message="form.errors.sender_name" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="notes">Catatan (opsional)</Label>
                                <textarea
                                    id="notes"
                                    v-model="form.notes"
                                    rows="4"
                                    class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm text-gray-700 outline-none transition focus:border-[#2E7D32]"
                                    placeholder="Tambahkan catatan jika diperlukan"
                                />
                                <InputError :message="form.errors.notes" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="payment_proof">Bukti Pembayaran</Label>
                                <input
                                    id="payment_proof"
                                    ref="paymentProofInput"
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    class="hidden"
                                    @change="onPaymentProofChange"
                                >

                                <div
                                    class="rounded-2xl border border-dashed p-4 transition"
                                    :class="form.payment_proof ? 'border-emerald-400 bg-emerald-50/60' : 'border-gray-300 bg-gray-50'"
                                >
                                    <div class="flex items-start gap-3">
                                        <div class="mt-0.5 rounded-xl p-2" :class="form.payment_proof ? 'bg-emerald-100 text-emerald-700' : 'bg-white text-gray-500'">
                                            <FileCheck2 v-if="form.payment_proof" class="h-5 w-5" />
                                            <UploadCloud v-else class="h-5 w-5" />
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-semibold text-gray-900">{{ selectedProofName }}</p>
                                            <p class="mt-1 text-xs text-gray-500">
                                                <span v-if="selectedProofSize">{{ selectedProofSize }} • </span>
                                                Format: JPG, JPEG, PNG, PDF. Maksimal 5MB.
                                            </p>

                                            <div class="mt-3 flex flex-wrap gap-2">
                                                <Button type="button" variant="outline" class="h-9 border-[#2E7D32] text-[#2E7D32]" @click="triggerPaymentProofPicker">
                                                    {{ form.payment_proof ? 'Ganti File' : 'Pilih File' }}
                                                </Button>
                                                <Button
                                                    v-if="form.payment_proof"
                                                    type="button"
                                                    variant="ghost"
                                                    class="h-9 text-gray-500 hover:text-red-600"
                                                    @click="clearPaymentProof"
                                                >
                                                    <X class="h-4 w-4" />
                                                    Hapus
                                                </Button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <InputError :message="form.errors.payment_proof" />
                            </div>

                            <Button type="submit" class="w-full bg-[#2E7D32] text-white hover:bg-[#256427]" :disabled="form.processing">
                                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                                Konfirmasi Pembayaran
                            </Button>
                        </form>
                    </div>

                    <aside class="rounded-2xl border border-emerald-100 bg-emerald-50 p-6">
                        <div class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-semibold text-[#2E7D32]">
                            <BadgeCheck class="h-3.5 w-3.5" /> Ringkasan Tagihan
                        </div>

                        <div class="mt-4 space-y-3 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Paket</span>
                                <span class="font-semibold text-gray-900">{{ subscription.plan.name }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Durasi</span>
                                <span class="font-semibold text-gray-900">{{ subscription.plan.duration_months }} bulan</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Nomor Subscription</span>
                                <span class="font-semibold text-gray-900">#{{ subscription.id }}</span>
                            </div>
                            <div class="flex items-center justify-between border-t border-emerald-200 pt-3">
                                <span class="text-gray-700">Total Bayar</span>
                                <span class="text-xl font-extrabold text-[#2E7D32]">{{ formatCurrency(subscription.plan.final_amount) }}</span>
                            </div>
                        </div>

                        <div class="mt-5 rounded-xl border border-emerald-200 bg-white p-3 text-xs text-gray-600">
                            <p class="font-semibold text-gray-800">Instruksi:</p>
                            <ul class="mt-2 space-y-1">
                                <li class="flex gap-2"><Landmark class="h-3.5 w-3.5 shrink-0 text-[#2E7D32]" />Transfer sesuai nominal total bayar.</li>
                                <li class="flex gap-2"><Landmark class="h-3.5 w-3.5 shrink-0 text-[#2E7D32]" />Pastikan nama pengirim sesuai dengan rekening pengirim.</li>
                                <li class="flex gap-2"><Landmark class="h-3.5 w-3.5 shrink-0 text-[#2E7D32]" />Verifikasi dilakukan maksimal 1x24 jam.</li>
                            </ul>
                        </div>
                    </aside>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
