<script setup lang="ts">
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Head } from '@inertiajs/vue3';
import { CalendarDays, CirclePlay, Clock3, Video } from 'lucide-vue-next';
import { computed } from 'vue';

interface LiveSessionItem {
    id: number;
    title: string;
    description: string | null;
    scheduled_at: string | null;
    scheduled_label: string | null;
    status: 'scheduled' | 'live' | 'ended';
    stream_url: string | null;
    plan_name: string | null;
}

const props = defineProps<{
    sessions: LiveSessionItem[];
}>();

const liveNowSessions = computed(() => props.sessions.filter((item) => item.status === 'live'));
const upcomingSessions = computed(() => props.sessions.filter((item) => item.status === 'scheduled'));
const endedSessions = computed(() => props.sessions.filter((item) => item.status === 'ended'));

const statusBadgeClass = (status: LiveSessionItem['status']) => {
    if (status === 'live') {
        return 'bg-red-100 text-red-700 border-red-200';
    }

    if (status === 'scheduled') {
        return 'bg-blue-100 text-blue-700 border-blue-200';
    }

    return 'bg-gray-100 text-gray-700 border-gray-200';
};

const statusLabel = (status: LiveSessionItem['status']) => {
    if (status === 'live') {
        return 'Sedang Live';
    }

    if (status === 'scheduled') {
        return 'Terjadwal';
    }

    return 'Selesai';
};
</script>

<template>
    <Head title="Live Session" />

    <PublicLayout>
        <section class="relative overflow-hidden bg-gradient-to-br from-[#0f5f2b] via-[#2E7D32] to-[#66BB6A] text-white">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.22),transparent_45%)]" />
            <div class="relative mx-auto max-w-7xl px-6 py-14 lg:px-8 lg:py-16">
                <p class="text-xs font-semibold tracking-[0.18em] text-green-100 uppercase">Petani Cerdas</p>
                <h1 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">Live Session Petani</h1>
                <p class="mt-4 max-w-2xl text-sm text-green-50 sm:text-base">
                    Ikuti sesi pembinaan langsung bersama mentor. Pantau sesi yang sedang berlangsung,
                    jadwal berikutnya, dan riwayat sesi yang sudah selesai.
                </p>
            </div>
        </section>

        <section class="bg-[#f5f8f3] py-10 lg:py-12">
            <div class="mx-auto grid max-w-7xl grid-cols-1 gap-4 px-6 lg:grid-cols-3 lg:px-8">
                <div class="rounded-2xl border border-emerald-200 bg-white p-5">
                    <p class="text-xs font-semibold tracking-wide text-emerald-700 uppercase">Sedang Live</p>
                    <p class="mt-2 text-3xl font-black text-gray-900">{{ liveNowSessions.length }}</p>
                </div>
                <div class="rounded-2xl border border-blue-200 bg-white p-5">
                    <p class="text-xs font-semibold tracking-wide text-blue-700 uppercase">Akan Datang</p>
                    <p class="mt-2 text-3xl font-black text-gray-900">{{ upcomingSessions.length }}</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-5">
                    <p class="text-xs font-semibold tracking-wide text-gray-700 uppercase">Selesai</p>
                    <p class="mt-2 text-3xl font-black text-gray-900">{{ endedSessions.length }}</p>
                </div>
            </div>
        </section>

        <section class="bg-white pb-20 pt-6">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div v-if="sessions.length === 0" class="rounded-2xl border-2 border-dashed border-gray-200 p-12 text-center">
                    <Video class="mx-auto h-12 w-12 text-gray-300" />
                    <h2 class="mt-4 text-xl font-bold text-gray-900">Belum Ada Live Session</h2>
                    <p class="mt-2 text-sm text-gray-500">Jadwal sesi akan muncul di sini ketika mentor menjadwalkan sesi baru.</p>
                </div>

                <div v-else class="space-y-5">
                    <article
                        v-for="session in sessions"
                        :key="session.id"
                        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm"
                    >
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <div class="mb-3 flex flex-wrap items-center gap-2">
                                    <span :class="['inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold', statusBadgeClass(session.status)]">
                                        {{ statusLabel(session.status) }}
                                    </span>
                                    <span v-if="session.plan_name" class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                        {{ session.plan_name }}
                                    </span>
                                </div>

                                <h3 class="text-xl font-bold text-gray-900">{{ session.title }}</h3>
                                <p class="mt-2 text-sm text-gray-600">{{ session.description || 'Sesi live bersama mentor Petani Cerdas.' }}</p>

                                <div class="mt-4 flex flex-wrap items-center gap-4 text-sm text-gray-600">
                                    <div class="inline-flex items-center gap-2">
                                        <CalendarDays class="h-4 w-4 text-emerald-600" />
                                        <span>{{ session.scheduled_label || '-' }}</span>
                                    </div>
                                    <div class="inline-flex items-center gap-2">
                                        <Clock3 class="h-4 w-4 text-emerald-600" />
                                        <span>{{ session.status === 'ended' ? 'Sesi berakhir' : 'Persiapkan 10 menit sebelum sesi' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="sm:w-[210px] sm:shrink-0">
                                <Button
                                    v-if="session.status === 'live' && session.stream_url"
                                    variant="solid"
                                    color="green"
                                    as-child
                                    class="w-full bg-[#2E7D32] hover:bg-[#256427]"
                                >
                                    <a :href="session.stream_url" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-2">
                                        <CirclePlay class="h-4 w-4" />
                                        Gabung Sesi
                                    </a>
                                </Button>
                                <Button v-else variant="outline" class="w-full" disabled>
                                    Live Belum dimulai
                                </Button>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
