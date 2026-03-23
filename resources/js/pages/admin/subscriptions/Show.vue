<script setup lang="ts">
import { ref, computed } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import InputError from '@/components/InputError.vue';
import { ArrowLeft, AlertCircle, CheckCircle2, XCircle } from 'lucide-vue-next';

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
  return key ? (colors[key] || 'bg-gray-100 text-gray-800') : 'bg-gray-100 text-gray-800';
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
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Back Button -->
      <button
        @click="router.visit(route('admin.subscriptions.payments.index'))"
        class="mb-8 flex items-center gap-2 text-blue-600 hover:text-blue-800 transition-colors"
      >
        <ArrowLeft class="h-5 w-5" />
        <span>Kembali ke Daftar</span>
      </button>

      <!-- Header -->
      <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Detail Pembayaran</h1>
            <p class="text-gray-600 mt-1">{{ payment.transaction_id }}</p>
          </div>
          <div
            class="px-4 py-2 rounded-full text-sm font-semibold"
            :class="[
              payment.status === 'pending'
                ? 'bg-amber-100 text-amber-800'
                : 'bg-gray-100 text-gray-800',
            ]"
          >
            {{ payment.status === 'pending' ? 'Menunggu Verifikasi' : payment.status }}
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
          <!-- Payment Information -->
          <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Informasi Pembayaran</h2>

            <div class="grid grid-cols-2 gap-6">
              <div>
                <p class="text-sm text-gray-600 mb-1">Nama Pengirim</p>
                <p class="text-base font-medium text-gray-900">{{ payment.sender_name }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600 mb-1">Bank Pengirim</p>
                <p class="text-base font-medium text-gray-900">
                  {{ payment.sender_bank || 'Tidak tercatat' }}
                </p>
              </div>
              <div>
                <p class="text-sm text-gray-600 mb-1">Nama Rekening Tujuan</p>
                <p class="text-base font-medium text-gray-900">{{ payment.destination_account }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600 mb-1">Nominal Pembayaran</p>
                <p class="text-lg font-semibold text-gray-900">{{ formatCurrency(payment.amount) }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600 mb-1">Metode Pembayaran</p>
                <p class="text-base font-medium text-gray-900">{{ payment.payment_method }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600 mb-1">Tanggal Pembayaran</p>
                <p class="text-base font-medium text-gray-900">{{ formatDate(payment.paid_at) }}</p>
              </div>
            </div>

            <div v-if="payment.notes" class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
              <p class="text-sm text-gray-600 mb-2">Keterangan</p>
              <p class="text-sm text-gray-900">{{ payment.notes }}</p>
            </div>
          </div>

          <!-- Petani Information -->
          <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Data Petani</h2>

            <div class="grid grid-cols-2 gap-6">
              <div>
                <p class="text-sm text-gray-600 mb-1">Nama</p>
                <p class="text-base font-medium text-gray-900">{{ payment.user.name }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600 mb-1">Email</p>
                <p class="text-base font-medium text-gray-900">{{ payment.user.email }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600 mb-1">Nomor Telepon</p>
                <p class="text-base font-medium text-gray-900">{{ payment.user.phone || '-' }}</p>
              </div>
            </div>
          </div>

          <!-- Package Information -->
          <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Paket Subscription</h2>

            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-600 mb-2">Paket yang Dibeli</p>
                <span
                  :class="[
                    'inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold',
                    planBadgeColor(payment.subscription.plan.key),
                  ]"
                >
                  {{ payment.subscription.plan.name }}
                </span>
              </div>
              <div>
                <p class="text-sm text-gray-600 mb-1">Harga Paket</p>
                <p class="text-lg font-semibold text-gray-900">
                  {{ formatCurrency(payment.subscription.plan.price) }}
                </p>
              </div>
            </div>
          </div>

          <!-- Bukti Pembayaran -->
          <div v-if="payment.proof_of_payment_path" class="bg-white rounded-lg border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Bukti Pembayaran</h2>
            <img
              :src="payment.proof_of_payment_path"
              :alt="'Bukti Pembayaran ' + payment.transaction_id"
              class="w-full rounded-lg border border-gray-200 max-h-96 object-cover"
            />
          </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="lg:col-span-1">
          <div class="sticky top-8 space-y-6">
            <!-- Approve Form -->
            <div
              v-if="!showApproveForm && !showRejectForm"
              class="bg-white rounded-lg border border-gray-200 p-6 space-y-4"
            >
              <Button
                @click="showApproveForm = true"
                class="w-full flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700"
              >
                <CheckCircle2 class="h-5 w-5" />
                <span>Setujui Pembayaran</span>
              </Button>
              <Button
                @click="showRejectForm = true"
                variant="destructive"
                class="w-full flex items-center justify-center gap-2"
              >
                <XCircle class="h-5 w-5" />
                <span>Tolak Pembayaran</span>
              </Button>
            </div>

            <!-- Approve Confirmation -->
            <form v-if="showApproveForm" @submit.prevent="handleApprove" class="bg-emerald-50 rounded-lg border border-emerald-200 p-6 space-y-6">
              <div class="flex items-start gap-3">
                <AlertCircle class="h-5 w-5 text-emerald-600 flex-shrink-0 mt-0.5" />
                <p class="text-sm text-emerald-800">Lengkapi detail subscription sebelum menyetujui pembayaran.</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-900 mb-2">Tanggal Mulai<span class="text-red-500">*</span></label>
                <Input
                  v-model="approveForm.start_date"
                  type="date"
                  :error="!!approveForm.errors.start_date"
                />
                <InputError :message="approveForm.errors.start_date" />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-900 mb-2">Tanggal Akhir<span class="text-red-500">*</span></label>
                <Input
                  v-model="approveForm.end_date"
                  type="date"
                  :error="!!approveForm.errors.end_date"
                />
                <InputError :message="approveForm.errors.end_date" />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-900 mb-2">Generation<span class="text-red-500">*</span></label>
                <Input
                  v-model="approveForm.generation"
                  type="number"
                  placeholder="Contoh: 202603"
                  :error="!!approveForm.errors.generation"
                />
                <InputError :message="approveForm.errors.generation" />
              </div>

              <div class="flex gap-3">
                <Button
                  type="button"
                  variant="outline"
                  @click="showApproveForm = false"
                  class="flex-1"
                >
                  Batal
                </Button>
                <Button
                  type="submit"
                  class="flex-1 bg-emerald-600 hover:bg-emerald-700"
                  :disabled="approveForm.processing"
                >
                  {{ approveForm.processing ? 'Memproses...' : 'Setujui' }}
                </Button>
              </div>
            </form>

            <!-- Reject Confirmation -->
            <form v-if="showRejectForm" @submit.prevent="handleReject" class="bg-red-50 rounded-lg border border-red-200 p-6 space-y-6">
              <div class="flex items-start gap-3">
                <AlertCircle class="h-5 w-5 text-red-600 flex-shrink-0 mt-0.5" />
                <p class="text-sm text-red-800">Pembayaran akan ditandai sebagai ditolak.</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-900 mb-2">Alasan Penolakan</label>
                <textarea
                  v-model="rejectForm.reason"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                  rows="3"
                  placeholder="Opsional: Jelaskan alasan penolakan pembayaran"
                ></textarea>
                <InputError :message="rejectForm.errors.reason" />
              </div>

              <div class="flex gap-3">
                <Button
                  type="button"
                  variant="outline"
                  @click="showRejectForm = false"
                  class="flex-1"
                >
                  Batal
                </Button>
                <Button
                  type="submit"
                  variant="destructive"
                  class="flex-1"
                  :disabled="rejectForm.processing"
                >
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
