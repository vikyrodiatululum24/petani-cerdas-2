<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};
</script>

<template>
    <AuthLayout title="Verifikasi Email (OTP)" description="Masukkan atau klik tautan OTP/verifikasi yang telah dikirim ke email Anda.">
        <Head title="Verifikasi Email" />

        <div v-if="status === 'verification-link-sent'" class="mb-4 text-center text-sm font-medium text-green-600">
            Link verifikasi baru telah dikirim ke email Anda.
        </div>

        <form @submit.prevent="submit" class="space-y-6 text-center">
            <Button :disabled="form.processing" class="bg-[#2E7D32] text-white hover:bg-[#256427]">
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                Kirim Ulang Verifikasi
            </Button>

            <TextLink :href="route('logout')" method="post" as="button" class="mx-auto block text-sm text-[#2E7D32]"> Keluar </TextLink>
        </form>
    </AuthLayout>
</template>
