<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { BadgeCheck, CheckCircle2, LoaderCircle } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { toast } from 'vue-sonner';

interface SubscriptionPlanItem {
    id: number;
    name: string;
    price: number;
    discount_price: number | null;
    quota: number | null;
    features: string[];
    description: string | null;
    duration_months: number;
    is_featured: boolean;
    final_price: number;
    used_slots: number;
    remaining_slots: number;
    available: boolean;
    can_checkout: boolean;
    cta: string;
}

interface ActiveSubscription {
    id: number;
    plan_name: string | null;
    status: string;
    end_date: string | null;
}

const props = defineProps<{
    plans: SubscriptionPlanItem[];
    activeSubscription: ActiveSubscription | null;
}>();

const page = usePage<{
    flash?: {
        success?: string | null;
        error?: string | null;
    };
}>();

const selectedPlanId = ref<number | null>(null);
const paymentSuccessModalOpen = ref(false);

watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) {
            paymentSuccessModalOpen.value = true;
        }

        if (flash?.error) {
            toast.error(flash.error);
        }
    },
    { immediate: true },
);

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(value);
};

const choosePlan = (planId: number) => {
    selectedPlanId.value = planId;

    router.post(
        route('petani.subscriptions.checkout', { subscriptionPlan: planId }),
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                selectedPlanId.value = null;
            },
        },
    );
};
</script>

<template>
    <Head title="Pilih Paket Subscription" />

    <PublicLayout>
        <Dialog v-model:open="paymentSuccessModalOpen">
            <DialogContent class="max-w-lg">
                <DialogHeader>
                    <div class="mb-2 flex items-center gap-2">
                        <BadgeCheck class="h-8 w-8 text-green-500" />
                        <DialogTitle>Pembayaran Berhasil Dikirim</DialogTitle>
                    </div>
                    <DialogDescription class="text-sm leading-relaxed text-gray-600">
                        Terimakasih, selamat bergabung dalam ekosistem petani cerdas. Pengajuan paket anda sedang dalam peninjauan dan akan segera
                        aktif setelah terkonfirmasi. Salam cerdas
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button type="button" class="bg-[#2E7D32] text-white hover:bg-[#256427]" @click="paymentSuccessModalOpen = false"> Tutup </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- MEMBERSHIP -->
        <section class="bg-gradient-to-b from-gray-50 to-white py-20 lg:py-24">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div
                    v-if="activeSubscription"
                    class="mx-auto mb-8 max-w-3xl rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-800"
                >
                    Membership Anda saat ini: <span class="font-bold">{{ activeSubscription.plan_name || '-' }}</span
                    >. Upgrade membership untuk mendapatkan akses ke fitur-fitur lebih lanjut.
                </div>
                <div class="mx-auto mb-14 max-w-2xl text-center">
                    <p class="mb-2 text-sm font-semibold uppercase tracking-widest text-[#2E7D32]">Membership</p>
                    <h2 class="text-3xl font-extrabold text-gray-900 lg:text-4xl">Pilih Paket yang Sesuai</h2>
                    <p class="mt-4 text-lg text-gray-500">
                        Pilih paket sesuai skala usaha tani Anda, dengan harga promo dan fasilitas yang dirancang untuk hasil nyata.
                    </p>
                </div>
                <div class="mx-auto grid max-w-6xl grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="plan in props.plans"
                        :key="plan.id"
                        :class="[
                            'relative flex flex-col rounded-2xl p-8',
                            plan.is_featured
                                ? 'bg-[#2E7D32] text-white shadow-2xl shadow-green-900/30 ring-2 ring-[#2E7D32]'
                                : 'border border-gray-200 bg-white shadow-sm',
                        ]"
                    >
                        <div
                            v-if="plan.is_featured"
                            class="absolute -top-3.5 left-1/2 -translate-x-1/2 rounded-full bg-yellow-400 px-4 py-1 text-xs font-bold text-yellow-900 shadow"
                        >
                            Paling Populer
                        </div>
                        <div class="mb-6">
                            <h3 :class="['mb-1 text-lg font-bold', plan.is_featured ? 'text-white' : 'text-gray-900']">{{ plan.name }}</h3>

                            <p
                                v-if="plan.discount_price"
                                :class="['mb-1 text-sm line-through', plan.is_featured ? 'text-green-200/80' : 'text-gray-400']"
                            >
                                {{ formatCurrency(plan.price) }}
                            </p>
                            <div class="flex items-baseline gap-1.5">
                                <span :class="['text-3xl font-extrabold', plan.is_featured ? 'text-white' : 'text-gray-900']">{{
                                    formatCurrency(plan.final_price)
                                }}</span>
                                <span :class="['text-sm', plan.is_featured ? 'text-green-200' : 'text-gray-400']"
                                    >/ {{ plan.duration_months }} bulan</span
                                >
                            </div>
                            <p
                                :class="[
                                    'mt-1 text-xs font-medium',
                                    plan.available ? (plan.is_featured ? 'text-green-100' : 'text-emerald-600') : 'text-red-500',
                                ]"
                            >
                                {{ plan.available ? `Sisa slot ${plan.remaining_slots}` : 'Kuota penuh' }}
                            </p>
                        </div>
                        <ul class="mb-8 flex-1 space-y-3">
                            <li v-for="feat in plan.features" :key="feat" class="flex items-start gap-2.5 text-sm">
                                <CheckCircle2 :class="['mt-0.5 h-4 w-4 shrink-0', plan.is_featured ? 'text-[#A5D6A7]' : 'text-[#4CAF50]']" />
                                <span :class="plan.is_featured ? 'text-green-100' : 'text-gray-600'">{{ feat }}</span>
                            </li>
                        </ul>
                        <Button
                            type="button"
                            class="w-full"
                            :class="plan.is_featured ? 'bg-white text-[#2E7D32] hover:bg-green-50' : 'bg-[#2E7D32] text-white hover:bg-[#256427]'"
                            :disabled="selectedPlanId === plan.id || !plan.can_checkout"
                            @click="choosePlan(plan.id)"
                        >
                            <LoaderCircle v-if="selectedPlanId === plan.id" class="h-4 w-4 animate-spin" />
                            {{ plan.cta }}
                        </Button>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
