<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { getEcho } from '@/echo';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, CheckCheck, Paperclip, Search, Send, X } from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';

interface ThreadMessage {
    id: number;
    sender_name: string;
    is_me: boolean;
    message: string;
    message_type: string;
    attachment_url: string | null;
    is_read: boolean;
    time: string;
}

interface ThreadItem {
    id: number;
    topic_title: string;
    category_name: string;
    customer_name: string;
    customer_package_name: string | null;
    customer_package_plan: 'basic' | 'premium' | 'elite' | null;
    customer_package_status: 'pending' | 'active' | 'inactive' | 'canceled' | null;
    counterpart_name: string;
    status: 'new' | 'in_progress' | 'resolved';
    assigned_mentor_id: number | null;
    assigned_mentor_name: string | null;
    last_message: string;
    last_time: string;
    unread_count: number;
    messages: ThreadMessage[];
}

interface MentorOption {
    id: number;
    name: string;
}

const props = defineProps<{
    threads: ThreadItem[];
    selectedThreadId: number | null;
    role: 'admin' | 'mentor' | 'petani';
    mentors: MentorOption[];
    filters: {
        status: string;
        mentor_id: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Chat Inbox', href: '/admin/chat' },
];

const search = ref('');
const isMobileThreadOpen = ref(false);
const previewImageUrl = ref<string | null>(null);
const statusFilter = ref(props.filters.status || 'all');
const mentorFilter = ref(props.filters.mentor_id || 'all');
const activeThreadId = ref<number | null>(props.selectedThreadId ?? props.threads[0]?.id ?? null);
const selectedMentorId = ref<string>('');
const selectedStatus = ref<'new' | 'in_progress' | 'resolved'>('new');

const subscribedChannels: string[] = [];

const messageForm = useForm({
    message: '',
    image: null as File | null,
});
const imageInputRef = ref<HTMLInputElement | null>(null);

const assignMentorForm = useForm({
    mentor_id: '',
});

const updateStatusForm = useForm({
    status: 'new' as 'new' | 'in_progress' | 'resolved',
});

const filteredThreads = computed(() => {
    const keyword = search.value.trim().toLowerCase();

    if (!keyword) {
        return props.threads;
    }

    return props.threads.filter((thread) => {
        return (
            thread.topic_title.toLowerCase().includes(keyword) ||
            thread.category_name.toLowerCase().includes(keyword) ||
            thread.customer_name.toLowerCase().includes(keyword) ||
            thread.last_message.toLowerCase().includes(keyword)
        );
    });
});

const activeThread = computed(() => {
    return props.threads.find((thread) => thread.id === activeThreadId.value) ?? props.threads[0] ?? null;
});

const packageBadgeClass = (packagePlan: ThreadItem['customer_package_plan']) => {
    if (!packagePlan) {
        return 'border-gray-200 bg-gray-100 text-gray-600';
    }

    if (packagePlan === 'elite') {
        return 'border-amber-200 bg-amber-100 text-amber-800';
    }

    if (packagePlan === 'premium') {
        return 'border-sky-200 bg-sky-100 text-sky-800';
    }

    if (packagePlan === 'basic') {
        return 'border-emerald-200 bg-emerald-100 text-emerald-800';
    }

    return 'border-gray-200 bg-gray-100 text-gray-600';
};

const applyServerFilters = () => {
    router.get(
        route('admin.chat.index'),
        {
            status: statusFilter.value,
            mentor_id: mentorFilter.value,
            thread: activeThread.value?.id,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
};

const reloadChatData = () => {
    router.reload({
        only: ['threads', 'selectedThreadId', 'filters', 'mentors'],
    });
};

const subscribeRealtimeChannels = () => {
    const echo = getEcho();

    echo.private('chat.inbox').listen('.chat.message.sent', reloadChatData).listen('.chat.thread.updated', reloadChatData);
    subscribedChannels.push('chat.inbox');

    for (const thread of props.threads) {
        const channel = `chat.thread.${thread.id}`;

        echo.private(channel).listen('.chat.message.sent', reloadChatData).listen('.chat.thread.updated', reloadChatData);

        subscribedChannels.push(channel);
    }
};

const selectThread = (threadId: number) => {
    activeThreadId.value = threadId;
    isMobileThreadOpen.value = true;
};

const backToThreadList = () => {
    isMobileThreadOpen.value = false;
};

const assignMentor = () => {
    if (!activeThread.value || !selectedMentorId.value) {
        return;
    }

    assignMentorForm.mentor_id = selectedMentorId.value;
    assignMentorForm.post(route('admin.chat.assign-mentor', { thread: activeThread.value.id }), {
        preserveScroll: true,
    });
};

const updateThreadStatus = () => {
    if (!activeThread.value) {
        return;
    }

    updateStatusForm.status = selectedStatus.value;
    updateStatusForm.patch(route('admin.chat.update-status', { thread: activeThread.value.id }), {
        preserveScroll: true,
    });
};

const submitMessage = () => {
    if (!activeThread.value || (!messageForm.message.trim() && !messageForm.image)) {
        return;
    }

    messageForm.post(route('public.chat.send-message', { thread: activeThread.value.id }), {
        preserveScroll: true,
        preserveState: true,
        forceFormData: true,
        onSuccess: () => {
            messageForm.reset('message', 'image');
            if (imageInputRef.value) {
                imageInputRef.value.value = '';
            }
        },
    });
};

const openImagePicker = () => {
    imageInputRef.value?.click();
};

const onImageSelected = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0] ?? null;
    messageForm.image = file;
};

const removeSelectedImage = () => {
    messageForm.image = null;

    if (imageInputRef.value) {
        imageInputRef.value.value = '';
    }
};

const openImagePreview = (imageUrl: string | null) => {
    if (!imageUrl) {
        return;
    }

    previewImageUrl.value = imageUrl;
};

const closeImagePreview = () => {
    previewImageUrl.value = null;
};

watch(
    () => activeThread.value,
    (thread) => {
        if (!thread) {
            selectedMentorId.value = '';
            selectedStatus.value = 'new';
            return;
        }

        selectedMentorId.value = thread.assigned_mentor_id ? String(thread.assigned_mentor_id) : '';
        selectedStatus.value = thread.status;
    },
    { immediate: true },
);

watch([statusFilter, mentorFilter], () => {
    applyServerFilters();
});

onMounted(() => {
    subscribeRealtimeChannels();
});

onBeforeUnmount(() => {
    const echo = getEcho();

    for (const channel of subscribedChannels) {
        echo.leave(channel);
    }
});
</script>

<template>
    <Head title="Chat Inbox Admin" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div
                class="grid h-[calc(100vh-6rem)] grid-cols-1 overflow-hidden rounded-xl border border-sidebar-border/70 bg-white md:grid-cols-[360px_1fr]"
            >
                <aside class="flex h-full min-h-0 flex-col border-r border-gray-200 bg-gray-50" :class="isMobileThreadOpen ? 'hidden md:flex' : 'flex'">
                    <div class="border-b border-gray-200 p-4">
                        <h1 class="text-lg font-bold text-gray-900">Inbox Chat Petani</h1>
                        <p class="text-xs text-gray-500">Tangani dan balas seluruh topik masuk dari petani/customer.</p>
                        <div class="relative mt-3">
                            <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                            <Input v-model="search" placeholder="Cari topik, petani, atau pesan..." class="h-10 bg-white pl-9" />
                        </div>
                        <div class="mt-3 grid grid-cols-2 gap-2" :class="role === 'admin' ? 'md:grid-cols-2' : 'md:grid-cols-1'">
                            <select v-model="statusFilter" class="h-10 rounded-md border border-gray-300 px-2 text-xs">
                                <option value="all">Semua status</option>
                                <option value="new">Baru</option>
                                <option value="in_progress">Diproses</option>
                                <option value="resolved">Selesai</option>
                            </select>
                            <select v-if="role === 'admin'" v-model="mentorFilter" class="h-10 rounded-md border border-gray-300 px-2 text-xs">
                                <option value="all">Semua mentor</option>
                                <option v-for="mentor in mentors" :key="mentor.id" :value="String(mentor.id)">{{ mentor.name }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto">
                        <button
                            v-for="thread in filteredThreads"
                            :key="thread.id"
                            type="button"
                            class="w-full border-b border-gray-200 px-4 py-3 text-left transition-colors hover:bg-gray-100"
                            :class="activeThreadId === thread.id ? 'bg-gray-100' : 'bg-transparent'"
                            @click="selectThread(thread.id)"
                        >
                            <div class="min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="truncate text-sm font-semibold text-gray-900">{{ thread.topic_title }}</p>
                                    <p class="shrink-0 text-xs text-gray-500">{{ thread.last_time }}</p>
                                </div>
                                <p class="truncate text-xs font-medium text-[#2E7D32]">{{ thread.category_name }}</p>
                                <p class="truncate text-[11px] text-gray-500">Status: {{ thread.status }}</p>
                                <p class="truncate text-[11px] text-gray-500">Mentor: {{ thread.assigned_mentor_name ?? 'Belum diassign' }}</p>
                                <div class="mt-0.5 flex items-center gap-2">
                                    <p class="truncate text-xs text-gray-500">Petani: {{ thread.customer_name }}</p>
                                    <span
                                        class="inline-flex shrink-0 items-center rounded-full border px-2 py-0.5 text-[10px] font-semibold uppercase"
                                        :class="packageBadgeClass(thread.customer_package_plan)"
                                    >
                                        {{ thread.customer_package_plan ?? 'no-package' }}
                                    </span>
                                </div>
                                <div class="mt-1 flex items-center justify-between gap-2">
                                    <p class="truncate text-xs text-gray-500">{{ thread.last_message }}</p>
                                    <span
                                        v-if="thread.unread_count"
                                        class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-[#2E7D32] px-1.5 text-[10px] font-bold text-white"
                                    >
                                        {{ thread.unread_count }}
                                    </span>
                                </div>
                            </div>
                        </button>

                        <div v-if="!filteredThreads.length" class="p-4 text-sm text-gray-500">Belum ada thread masuk dari petani.</div>
                    </div>
                </aside>

                <main v-if="activeThread" class="h-full min-h-0 flex-col" :class="isMobileThreadOpen ? 'flex' : 'hidden md:flex'">
                    <header class="border-b border-gray-200 bg-white px-4 py-3">
                        <div class="flex items-center gap-3">
                            <Button type="button" variant="ghost" size="icon" class="h-9 w-9 rounded-full md:hidden" @click="backToThreadList">
                                <ArrowLeft class="h-4 w-4" />
                            </Button>
                            <div>
                                <h2 class="text-sm font-semibold text-gray-900">{{ activeThread.topic_title }}</h2>
                                <p class="text-xs text-gray-500">{{ activeThread.category_name }} • Petani: {{ activeThread.customer_name }}</p>
                            </div>
                        </div>
                        <div class="mt-2 grid grid-cols-1 gap-2" :class="role === 'admin' ? 'md:grid-cols-2' : 'md:grid-cols-1'">
                            <div v-if="role === 'admin'" class="flex gap-2">
                                <select v-model="selectedMentorId" class="h-9 flex-1 rounded-md border border-gray-300 px-2 text-xs">
                                    <option value="">Pilih mentor</option>
                                    <option v-for="mentor in mentors" :key="mentor.id" :value="String(mentor.id)">{{ mentor.name }}</option>
                                </select>
                                <Button
                                    type="button"
                                    size="sm"
                                    variant="outline"
                                    @click="assignMentor"
                                    :disabled="assignMentorForm.processing || !selectedMentorId"
                                >
                                    Assign
                                </Button>
                            </div>
                            <div class="flex gap-2">
                                <select v-model="selectedStatus" class="h-9 flex-1 rounded-md border border-gray-300 px-2 text-xs">
                                    <option value="new">Baru</option>
                                    <option value="in_progress">Diproses</option>
                                    <option value="resolved">Selesai</option>
                                </select>
                                <Button type="button" size="sm" variant="outline" @click="updateThreadStatus" :disabled="updateStatusForm.processing">
                                    Update
                                </Button>
                            </div>
                        </div>
                        <p v-if="assignMentorForm.errors.mentor_id" class="mt-1 text-xs text-red-600">{{ assignMentorForm.errors.mentor_id }}</p>
                        <p v-if="updateStatusForm.errors.status" class="mt-1 text-xs text-red-600">{{ updateStatusForm.errors.status }}</p>
                    </header>

                    <div class="flex-1 overflow-y-auto bg-gray-50 px-4 py-4">
                        <div class="space-y-3">
                            <div
                                v-for="message in activeThread.messages"
                                :key="message.id"
                                class="flex"
                                :class="message.is_me ? 'justify-end' : 'justify-start'"
                            >
                                <div
                                    class="max-w-[75%] rounded-2xl px-3.5 py-2 text-sm shadow-sm"
                                    :class="
                                        message.is_me
                                            ? 'rounded-br-md bg-[#DCF8C6] text-gray-800'
                                            : 'rounded-bl-md border border-gray-200 bg-white text-gray-800'
                                    "
                                >
                                    <p class="text-[11px] font-semibold text-[#2E7D32]">{{ message.sender_name }}</p>
                                    <button
                                        v-if="message.attachment_url"
                                        type="button"
                                        class="mt-1 block w-full overflow-hidden rounded-lg"
                                        @click="openImagePreview(message.attachment_url)"
                                    >
                                        <img
                                            :src="message.attachment_url"
                                            alt="Lampiran gambar"
                                            class="max-h-56 w-full rounded-lg object-cover transition hover:opacity-90"
                                        />
                                    </button>
                                    <p v-if="message.message" class="mt-1 leading-relaxed">{{ message.message }}</p>
                                    <div class="mt-1 flex items-center justify-end gap-1 text-[10px] text-gray-500">
                                        <span>{{ message.time }}</span>
                                        <CheckCheck
                                            v-if="message.is_me"
                                            class="h-3.5 w-3.5"
                                            :class="message.is_read ? 'text-sky-500' : 'text-gray-400'"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <footer class="border-t border-gray-200 bg-white p-3">
                        <form class="flex items-center gap-2" @submit.prevent="submitMessage">
                            <input
                                ref="imageInputRef"
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                                class="hidden"
                                @change="onImageSelected"
                            />
                            <Button type="button" size="icon" variant="outline" class="h-11 w-11 rounded-full" @click="openImagePicker">
                                <Paperclip class="h-4 w-4" />
                            </Button>
                            <Input v-model="messageForm.message" placeholder="Balas chat petani..." class="h-11 flex-1" />
                            <Button
                                type="submit"
                                size="icon"
                                class="h-11 w-11 bg-[#2E7D32] text-white hover:bg-[#256427]"
                                :disabled="messageForm.processing || (!messageForm.message.trim() && !messageForm.image)"
                            >
                                <Send class="h-4 w-4" />
                            </Button>
                        </form>
                        <div
                            v-if="messageForm.image"
                            class="mt-2 flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-700"
                        >
                            <span class="truncate">{{ messageForm.image.name }}</span>
                            <button type="button" class="ml-2 text-gray-500 hover:text-red-600" @click="removeSelectedImage">
                                <X class="h-4 w-4" />
                            </button>
                        </div>
                        <p v-if="messageForm.errors.message" class="mt-2 text-xs font-medium text-red-600">{{ messageForm.errors.message }}</p>
                        <p v-if="messageForm.errors.image" class="mt-2 text-xs font-medium text-red-600">{{ messageForm.errors.image }}</p>
                    </footer>
                </main>

                <main v-else class="hidden h-full items-center justify-center md:flex">
                    <p class="text-sm text-gray-500">Pilih thread untuk menangani chat petani.</p>
                </main>
            </div>
        </div>

        <div v-if="previewImageUrl" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/80 p-4" @click="closeImagePreview">
            <div class="relative w-full max-w-5xl" @click.stop>
                <button
                    type="button"
                    class="absolute -right-2 -top-2 rounded-full bg-white p-1.5 text-gray-700 shadow hover:bg-gray-100"
                    @click="closeImagePreview"
                >
                    <X class="h-4 w-4" />
                </button>
                <img :src="previewImageUrl" alt="Preview gambar" class="max-h-[85vh] w-full rounded-xl object-contain" />
            </div>
        </div>
    </AppLayout>
</template>
