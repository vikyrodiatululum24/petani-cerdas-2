<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { getEcho } from '@/echo';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Camera, CheckCheck, MessageSquarePlus, Search, Send, X } from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

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
    last_message: string;
    last_time: string;
    unread_count: number;
    messages: ThreadMessage[];
}

interface TopicOption {
    id: number;
    title: string;
}

interface TopicCategory {
    id: number;
    name: string;
    topics: TopicOption[];
}

const props = defineProps<{
    threads: ThreadItem[];
    selectedThreadId: number | null;
    topicCategories: TopicCategory[];
    canStartTopic: boolean;
    role: 'admin' | 'mentor' | 'petani';
    chatQuota: {
        max: number;
        used: number;
        remaining: number;
    } | null;
}>();

const search = ref('');
const isMobileThreadOpen = ref(false);
const showStartTopicForm = ref(false);
const previewImageUrl = ref<string | null>(null);
const activeThreadId = ref<number | null>(props.selectedThreadId ?? props.threads[0]?.id ?? null);

const startTopicForm = useForm({
    category_id: '',
    consultation_topic_id: '',
    initial_message: '',
});

const messageForm = useForm({
    message: '',
    image: null as File | null,
});
const imageInputRef = ref<HTMLInputElement | null>(null);

const subscribedChannels: string[] = [];

const reloadChatData = () => {
    router.reload({
        only: ['threads', 'selectedThreadId', 'chatQuota'],
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

onMounted(() => {
    subscribeRealtimeChannels();
});

onBeforeUnmount(() => {
    const echo = getEcho();

    for (const channel of subscribedChannels) {
        echo.leave(channel);
    }
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

const availableTopics = computed(() => {
    if (!startTopicForm.category_id) {
        return [] as TopicOption[];
    }

    const selectedCategory = props.topicCategories.find((category) => String(category.id) === String(startTopicForm.category_id));
    return selectedCategory?.topics ?? [];
});

const selectThread = (threadId: number) => {
    activeThreadId.value = threadId;
    isMobileThreadOpen.value = true;
};

const backToThreadList = () => {
    isMobileThreadOpen.value = false;
};

const openStartTopicForm = () => {
    showStartTopicForm.value = true;
};

const closeStartTopicForm = () => {
    showStartTopicForm.value = false;
    startTopicForm.reset();
};

const submitStartTopic = () => {
    if (props.chatQuota && props.chatQuota.remaining <= 0) {
        return;
    }

    startTopicForm.post(route('public.chat.start-topic'), {
        preserveScroll: true,
        onSuccess: () => {
            closeStartTopicForm();
        },
    });
};

const submitMessage = () => {
    if (!activeThread.value || (!messageForm.message.trim() && !messageForm.image)) {
        return;
    }

    if (props.chatQuota && props.chatQuota.remaining <= 0) {
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
</script>

<template>
    <Head title="Chat Konsultasi" />

    <PublicLayout>
        <section class="bg-gradient-to-b from-[#eaf6eb] to-[#f7fbf7] py-4 md:py-6">
            <div class="mx-auto max-w-7xl px-3 sm:px-6 lg:px-8">
                <div
                    class="h-[calc(100vh-8.8rem)] overflow-hidden rounded-2xl border border-green-100 bg-white shadow-xl shadow-green-900/10 md:h-[calc(100vh-9.5rem)]"
                >
                    <div class="grid h-full grid-cols-1 md:grid-cols-[350px_1fr]">
                        <aside class="flex h-full min-h-0 flex-col border-r border-green-100 bg-[#f5fbf6]" :class="isMobileThreadOpen ? 'hidden md:flex' : 'flex'">
                            <div class="border-b border-green-100 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h1 class="text-lg font-bold text-gray-900">Topik Konsultasi</h1>
                                        <p class="text-xs text-gray-500">
                                            {{ role === 'petani' ? 'Thread milik Anda' : 'Semua thread masuk dari petani' }}
                                        </p>
                                        <p
                                            v-if="chatQuota"
                                            class="mt-1 text-[11px] font-medium"
                                            :class="chatQuota.remaining > 0 ? 'text-emerald-700' : 'text-red-600'"
                                        >
                                            Kuota chat hari ini: {{ chatQuota.used }}/{{ chatQuota.max }} (sisa {{ chatQuota.remaining }})
                                        </p>
                                    </div>
                                    <Button
                                        v-if="canStartTopic"
                                        type="button"
                                        size="icon"
                                        class="h-9 w-9 rounded-full bg-[#2E7D32] text-white hover:bg-[#256427]"
                                        :disabled="Boolean(chatQuota && chatQuota.remaining <= 0)"
                                        @click="openStartTopicForm"
                                    >
                                        <MessageSquarePlus class="h-4 w-4" />
                                    </Button>
                                </div>

                                <div class="relative mt-3">
                                    <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                                    <Input
                                        v-model="search"
                                        placeholder="Cari topik, kategori, atau pesan..."
                                        class="h-10 rounded-xl border-green-200 bg-white pl-9"
                                    />
                                </div>
                            </div>

                            <div class="flex-1 overflow-y-auto">
                                <button
                                    v-for="thread in filteredThreads"
                                    :key="thread.id"
                                    type="button"
                                    class="w-full border-b border-green-100/80 px-4 py-3 text-left transition-colors hover:bg-green-50"
                                    :class="activeThreadId === thread.id ? 'bg-green-100/70' : 'bg-transparent'"
                                    @click="selectThread(thread.id)"
                                >
                                    <div class="min-w-0">
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="truncate text-sm font-semibold text-gray-900">{{ thread.topic_title }}</p>
                                            <p class="shrink-0 text-xs text-gray-500">{{ thread.last_time }}</p>
                                        </div>
                                        <p class="truncate text-xs text-[#2E7D32]">{{ thread.category_name }}</p>
                                        <p class="mt-1 truncate text-xs text-gray-500">{{ thread.last_message }}</p>
                                        <div class="mt-1.5 flex items-center justify-between gap-2">
                                            <div class="flex min-w-0 items-center gap-2">
                                                <p class="truncate text-[11px] text-gray-400">Petani: {{ thread.customer_name }}</p>
                                                <span
                                                    v-if="role !== 'petani'"
                                                    class="inline-flex shrink-0 items-center rounded-full border px-2 py-0.5 text-[10px] font-semibold uppercase"
                                                    :class="packageBadgeClass(thread.customer_package_plan)"
                                                >
                                                    {{ thread.customer_package_plan ?? 'no-package' }}
                                                </span>
                                            </div>
                                            <span
                                                v-if="thread.unread_count"
                                                class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-[#2E7D32] px-1.5 text-[10px] font-bold text-white"
                                            >
                                                {{ thread.unread_count }}
                                            </span>
                                        </div>
                                    </div>
                                </button>

                                <div v-if="!filteredThreads.length" class="px-4 py-6 text-center text-sm text-gray-500">
                                    Belum ada thread untuk ditampilkan.
                                </div>
                            </div>
                        </aside>

                        <main v-if="activeThread" class="h-full min-h-0 flex-col" :class="isMobileThreadOpen ? 'flex' : 'hidden md:flex'">
                            <header class="flex items-center justify-between border-b border-green-100 bg-[#f1f8f2] px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="icon"
                                        class="h-9 w-9 rounded-full md:hidden"
                                        @click="backToThreadList"
                                    >
                                        <ArrowLeft class="h-4 w-4" />
                                    </Button>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ activeThread.topic_title }}</p>
                                        <p class="text-xs text-gray-500">{{ activeThread.category_name }} Petani: {{ activeThread.customer_name }}</p>
                                    </div>
                                </div>
                            </header>

                            <div class="chat-pattern min-h-0 flex-1 overflow-y-auto px-4 py-4 sm:px-6">
                                <div class="space-y-3">
                                    <div
                                        v-for="message in activeThread.messages"
                                        :key="message.id"
                                        class="flex"
                                        :class="message.is_me ? 'justify-end' : 'justify-start'"
                                    >
                                        <div
                                            class="max-w-[85%] rounded-2xl px-3.5 py-2 text-sm shadow-sm sm:max-w-[70%]"
                                            :class="
                                                message.is_me
                                                    ? 'rounded-br-md bg-[#DCF8C6] text-gray-800'
                                                    : 'rounded-bl-md border border-green-100 bg-white text-gray-800'
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

                            <footer class="shrink-0 border-t border-green-100 bg-[#f1f8f2] p-3 sm:p-4">
                                <div
                                    v-if="messageForm.image"
                                    class="mb-2 flex items-center justify-between rounded-lg border border-green-200 bg-white px-3 py-2 text-xs text-gray-700"
                                >
                                    <span class="truncate">{{ messageForm.image.name }}</span>
                                    <button type="button" class="ml-2 text-gray-500 hover:text-red-600" @click="removeSelectedImage">
                                        <X class="h-4 w-4" />
                                    </button>
                                </div>
                                <form class="flex items-center gap-2" @submit.prevent="submitMessage">
                                    <input
                                        ref="imageInputRef"
                                        type="file"
                                        accept="image/jpeg,image/png,image/webp"
                                        class="hidden"
                                        @change="onImageSelected"
                                    />
                                    <Button
                                        type="button"
                                        size="icon"
                                        variant="outline"
                                        class="h-11 w-11 rounded-full border-green-300"
                                        @click="openImagePicker"
                                    >
                                        <Camera class="h-4 w-4" />
                                    </Button>
                                    <Input
                                        v-model="messageForm.message"
                                        placeholder="Tulis pesan..."
                                        class="h-11 flex-1 rounded-full border-green-200 bg-white"
                                    />
                                    <Button
                                        type="submit"
                                        size="icon"
                                        class="h-11 w-11 rounded-full bg-[#2E7D32] text-white hover:bg-[#256427]"
                                        :disabled="
                                            messageForm.processing ||
                                            (!messageForm.message.trim() && !messageForm.image) ||
                                            Boolean(chatQuota && chatQuota.remaining <= 0)
                                        "
                                    >
                                        <Send class="h-4 w-4" />
                                    </Button>
                                </form>
                                <p v-if="chatQuota && chatQuota.remaining <= 0" class="mt-2 text-xs font-medium text-red-600">
                                    Kuota chat harian Anda sudah habis. Coba lagi besok.
                                </p>
                                <p v-if="messageForm.errors.message" class="mt-2 text-xs font-medium text-red-600">
                                    {{ messageForm.errors.message }}
                                </p>
                                <p v-if="messageForm.errors.image" class="mt-2 text-xs font-medium text-red-600">
                                    {{ messageForm.errors.image }}
                                </p>
                            </footer>
                        </main>

                        <main v-else class="hidden h-full items-center justify-center md:flex">
                            <p class="text-sm text-gray-500">Pilih topik dari daftar untuk mulai melihat percakapan.</p>
                        </main>
                    </div>
                </div>
            </div>
        </section>

        <div v-if="showStartTopicForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
                <h2 class="text-xl font-bold text-gray-900">Mulai Topik Konsultasi</h2>
                <p class="mt-1 text-sm text-gray-500">Pilih kategori dan topik, lalu kirim pesan awal agar admin bisa memproses antrian Anda.</p>

                <form class="mt-5 space-y-4" @submit.prevent="submitStartTopic">
                    <div class="grid gap-2">
                        <Label for="category">Kategori</Label>
                        <select
                            id="category"
                            v-model="startTopicForm.category_id"
                            class="h-11 rounded-xl border border-gray-200 px-3 text-sm outline-none focus:border-[#2E7D32]"
                        >
                            <option value="">Pilih kategori</option>
                            <option v-for="category in topicCategories" :key="category.id" :value="String(category.id)">
                                {{ category.name }}
                            </option>
                        </select>
                        <p v-if="startTopicForm.errors.category_id" class="text-xs font-medium text-red-600">
                            {{ startTopicForm.errors.category_id }}
                        </p>
                    </div>

                    <div class="grid gap-2">
                        <Label for="topic">Topik</Label>
                        <select
                            id="topic"
                            v-model="startTopicForm.consultation_topic_id"
                            class="h-11 rounded-xl border border-gray-200 px-3 text-sm outline-none focus:border-[#2E7D32]"
                        >
                            <option value="">Pilih topik</option>
                            <option v-for="topic in availableTopics" :key="topic.id" :value="String(topic.id)">
                                {{ topic.title }}
                            </option>
                        </select>
                        <p v-if="startTopicForm.errors.consultation_topic_id" class="text-xs font-medium text-red-600">
                            {{ startTopicForm.errors.consultation_topic_id }}
                        </p>
                    </div>

                    <div class="grid gap-2">
                        <Label for="initial_message">Pesan Awal</Label>
                        <textarea
                            id="initial_message"
                            v-model="startTopicForm.initial_message"
                            rows="4"
                            class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm text-gray-700 outline-none transition focus:border-[#2E7D32]"
                            placeholder="Jelaskan masalah Anda secara ringkas dan jelas"
                        />
                        <p v-if="startTopicForm.errors.initial_message" class="text-xs font-medium text-red-600">
                            {{ startTopicForm.errors.initial_message }}
                        </p>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <Button type="button" variant="outline" @click="closeStartTopicForm">Batal</Button>
                        <Button type="submit" class="bg-[#2E7D32] text-white hover:bg-[#256427]" :disabled="startTopicForm.processing">
                            <Send class="h-4 w-4" />
                            Buat Topik
                        </Button>
                    </div>
                </form>
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
    </PublicLayout>
</template>

<style scoped>
.chat-pattern {
    background-color: #e5ddd5;
    background-image:
        radial-gradient(circle at 1px 1px, rgb(255 255 255 / 0.35) 1px, transparent 0),
        linear-gradient(120deg, rgb(255 255 255 / 0.12), transparent 50%);
    background-size:
        22px 22px,
        100% 100%;
}
</style>
