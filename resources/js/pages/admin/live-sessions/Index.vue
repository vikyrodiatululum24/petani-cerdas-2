<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import DropdownMenu from '@/components/ui/dropdown-menu/DropdownMenu.vue';
import DropdownMenuContent from '@/components/ui/dropdown-menu/DropdownMenuContent.vue';
import DropdownMenuItem from '@/components/ui/dropdown-menu/DropdownMenuItem.vue';
import DropdownMenuTrigger from '@/components/ui/dropdown-menu/DropdownMenuTrigger.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { router, useForm } from '@inertiajs/vue3';
import { CalendarDays, CirclePlay, Plus, SquareArrowOutUpRight, Video } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

interface SessionItem {
    id: number;
    title: string;
    description: string | null;
    scheduled_at: string | null;
    scheduled_label: string | null;
    status: 'scheduled' | 'live' | 'ended';
    stream_url: string | null;
    subscription_id: number | null;
    user_name: string | null;
    user_email: string | null;
    plan_name: string | null;
    generation: string | null;
}

interface SubscriptionOption {
    value: string;
    label: string;
}

interface Filters {
    search: string;
    status: string;
}

const props = defineProps<{
    sessions: SessionItem[];
    generationOptions: SubscriptionOption[];
    filters: Filters;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Live Sessions',
        href: '/admin/live-sessions',
    },
];

const filterSearch = ref(props.filters.search ?? '');
const filterStatus = ref(props.filters.status ?? '');

const openCreateDialog = ref(false);
const openEditDialog = ref(false);
const editingId = ref<number | null>(null);

const createForm = useForm({
    generation: '',
    title: '',
    description: '',
    scheduled_at: '',
    status: 'scheduled',
    stream_url: '',
});

const editForm = useForm({
    generation: '',
    title: '',
    description: '',
    scheduled_at: '',
    status: 'scheduled',
    stream_url: '',
});

const statusBadgeClass = computed(() => (status: SessionItem['status']) => {
    const map: Record<string, string> = {
        scheduled: 'bg-blue-100 text-blue-700 border-blue-200',
        live: 'bg-red-100 text-red-700 border-red-200',
        ended: 'bg-gray-100 text-gray-700 border-gray-200',
    };

    return map[status] ?? 'bg-gray-100 text-gray-700 border-gray-200';
});

const applyFilters = () => {
    router.get(
        route('admin.live-sessions.index'),
        {
            search: filterSearch.value || undefined,
            status: filterStatus.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
};

let debounceTimer: ReturnType<typeof setTimeout> | null = null;
watch(filterSearch, () => {
    if (debounceTimer) {
        clearTimeout(debounceTimer);
    }

    debounceTimer = setTimeout(() => {
        applyFilters();
    }, 350);
});

watch(filterStatus, () => {
    applyFilters();
});

const resetFilters = () => {
    filterSearch.value = '';
    filterStatus.value = '';
    applyFilters();
};

const submitCreate = () => {
    createForm.post(route('admin.live-sessions.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Jadwal live session berhasil dibuat.');
            openCreateDialog.value = false;
            createForm.reset();
            createForm.status = 'scheduled';
        },
        onError: () => {
            toast.error('Gagal membuat jadwal live session.');
        },
    });
};

const openEdit = (session: SessionItem) => {
    editingId.value = session.id;
    editForm.generation = session.generation ?? '';
    editForm.title = session.title;
    editForm.description = session.description ?? '';
    editForm.scheduled_at = session.scheduled_at ? session.scheduled_at.slice(0, 16) : '';
    editForm.status = session.status;
    editForm.stream_url = session.stream_url ?? '';
    openEditDialog.value = true;
};

const submitEdit = () => {
    if (!editingId.value) {
        return;
    }

    editForm.put(route('admin.live-sessions.update', { liveSession: editingId.value }), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Jadwal live session berhasil diperbarui.');
            openEditDialog.value = false;
            editingId.value = null;
        },
        onError: () => {
            toast.error('Gagal memperbarui jadwal live session.');
        },
    });
};

const submitStatusUpdate = (session: SessionItem, status: SessionItem['status']) => {
    editingId.value = session.id;
    editForm.generation = session.generation ?? '';
    editForm.title = session.title;
    editForm.description = session.description ?? '';
    editForm.scheduled_at = session.scheduled_at ? session.scheduled_at.slice(0, 16) : '';
    editForm.status = status;
    editForm.stream_url = session.stream_url ?? '';

    submitEdit();
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold">Jadwal Live Session</h1>
                    <p class="text-sm text-muted-foreground">Kelola jadwal sesi live berbasis generation member. Halaman ini bisa diakses admin dan mentor.</p>
                </div>
                <Button class="inline-flex items-center gap-2" @click="openCreateDialog = true">
                    <Plus class="h-4 w-4" />
                    Tambah Jadwal
                </Button>
            </div>

            <div class="overflow-x-auto rounded-lg border">
                <div class="grid gap-3 border-b bg-muted/30 p-4 md:grid-cols-3">
                    <div class="md:col-span-2">
                        <Label for="search">Search</Label>
                        <Input id="search" v-model="filterSearch" placeholder="Cari judul, generation, atau paket" />
                    </div>

                    <div>
                        <Label for="status-filter">Status</Label>
                        <select id="status-filter" v-model="filterStatus" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                            <option value="">Semua status</option>
                            <option value="scheduled">scheduled</option>
                            <option value="live">live</option>
                            <option value="ended">ended</option>
                        </select>
                    </div>

                    <div class="md:col-span-3 flex gap-2">
                        <Button variant="ghost" @click="resetFilters">Reset</Button>
                    </div>
                </div>

                <table class="w-full min-w-[980px] text-sm">
                    <thead class="bg-muted/60 text-left">
                        <tr>
                            <th class="px-4 py-3 font-medium">Sesi</th>
                            <th class="px-4 py-3 font-medium">Generation</th>
                            <th class="px-4 py-3 font-medium">Paket</th>
                            <th class="px-4 py-3 font-medium">Jadwal</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="sessions.length === 0">
                            <td colspan="6" class="px-4 py-10 text-center text-muted-foreground">
                                <Video class="mx-auto mb-2 h-5 w-5" />
                                Belum ada jadwal live session.
                            </td>
                        </tr>
                        <tr v-for="session in sessions" :key="session.id" class="border-t">
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ session.title }}</div>
                                <div class="text-xs text-muted-foreground">{{ session.description || '-' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ session.generation || '-' }}</div>
                                <div class="text-xs text-muted-foreground">Untuk seluruh member di generation ini</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ session.plan_name || 'Semua paket di generation' }}</div>
                                <div class="text-xs text-muted-foreground">Target generation: {{ session.generation || '-' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="inline-flex items-center gap-2 text-xs text-muted-foreground">
                                    <CalendarDays class="h-4 w-4" />
                                    {{ session.scheduled_label || '-' }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span :class="['inline-flex rounded-full border px-2.5 py-1 text-xs font-medium', statusBadgeClass(session.status)]">
                                    {{ session.status }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <Button size="sm" variant="outline" @click="openEdit(session)">Edit</Button>
                                    <!-- update status -->
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button size="sm" variant="outline">
                                                Ubah Status
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent>
                                            <DropdownMenuItem>
                                                <button @click="submitStatusUpdate(session, 'scheduled')">Scheduled</button>
                                            </DropdownMenuItem>
                                            <DropdownMenuItem>
                                                <button @click="submitStatusUpdate(session, 'live')">Live</button>
                                            </DropdownMenuItem>
                                            <DropdownMenuItem>
                                                <button @click="submitStatusUpdate(session, 'ended')">Ended</button>
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                    <Button v-if="session.stream_url" size="sm" variant="outline" as-child>
                                        <a :href="session.stream_url" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5">
                                            <SquareArrowOutUpRight class="h-3.5 w-3.5" />
                                            Link
                                        </a>
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <Dialog v-model:open="openCreateDialog">
            <DialogContent class="max-w-2xl">
                <DialogHeader>
                    <DialogTitle>Tambah Jadwal Live Session</DialogTitle>
                    <DialogDescription>Tentukan generation target, jadwal, dan link sesi.</DialogDescription>
                </DialogHeader>

                <div class="grid gap-4">
                    <div>
                        <Label for="create-generation">Generation Target</Label>
                        <select id="create-generation" v-model="createForm.generation" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                            <option value="">Pilih generation</option>
                            <option v-for="opt in generationOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                        </select>
                    </div>

                    <div>
                        <Label for="create-title">Judul Sesi</Label>
                        <Input id="create-title" v-model="createForm.title" placeholder="Contoh: Klinik Tanam Mingguan" />
                    </div>

                    <div>
                        <Label for="create-description">Deskripsi</Label>
                        <textarea id="create-description" v-model="createForm.description" rows="3" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="Materi atau agenda sesi" />
                    </div>

                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div>
                            <Label for="create-scheduled">Waktu Sesi</Label>
                            <Input id="create-scheduled" v-model="createForm.scheduled_at" type="datetime-local" />
                        </div>

                        <div>
                            <Label for="create-status">Status</Label>
                            <select id="create-status" v-model="createForm.status" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                <option value="scheduled">scheduled</option>
                                <option value="live">live</option>
                                <option value="ended">ended</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <Label for="create-stream">Link Stream</Label>
                        <Input id="create-stream" v-model="createForm.stream_url" placeholder="https://..." />
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="outline" @click="openCreateDialog = false">Batal</Button>
                    <Button :disabled="createForm.processing" @click="submitCreate">
                        {{ createForm.processing ? 'Menyimpan...' : 'Simpan Jadwal' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <Dialog v-model:open="openEditDialog">
            <DialogContent class="max-w-2xl">
                <DialogHeader>
                    <DialogTitle>Edit Jadwal Live Session</DialogTitle>
                    <DialogDescription>Perbarui detail jadwal, status, atau link live session.</DialogDescription>
                </DialogHeader>

                <div class="grid gap-4">
                    <div>
                        <Label for="edit-generation">Generation Target</Label>
                        <select id="edit-generation" v-model="editForm.generation" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                            <option value="">Pilih generation</option>
                            <option v-for="opt in generationOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                        </select>
                    </div>

                    <div>
                        <Label for="edit-title">Judul Sesi</Label>
                        <Input id="edit-title" v-model="editForm.title" />
                    </div>

                    <div>
                        <Label for="edit-description">Deskripsi</Label>
                        <textarea id="edit-description" v-model="editForm.description" rows="3" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" />
                    </div>

                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div>
                            <Label for="edit-scheduled">Waktu Sesi</Label>
                            <Input id="edit-scheduled" v-model="editForm.scheduled_at" type="datetime-local" />
                        </div>
                        <div>
                            <Label for="edit-status">Status</Label>
                            <select id="edit-status" v-model="editForm.status" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                <option value="scheduled">scheduled</option>
                                <option value="live">live</option>
                                <option value="ended">ended</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <Label for="edit-stream">Link Stream</Label>
                        <Input id="edit-stream" v-model="editForm.stream_url" placeholder="https://..." />
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="outline" @click="openEditDialog = false">Batal</Button>
                    <Button :disabled="editForm.processing" @click="submitEdit">
                        {{ editForm.processing ? 'Menyimpan...' : 'Perbarui Jadwal' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

    </AppLayout>
</template>
