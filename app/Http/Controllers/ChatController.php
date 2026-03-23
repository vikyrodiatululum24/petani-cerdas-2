<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageSent;
use App\Events\ChatThreadUpdated;
use App\Http\Requests\Chat\AssignMentorRequest;
use App\Http\Requests\Chat\SendChatMessageRequest;
use App\Http\Requests\Chat\StartTopicChatRequest;
use App\Http\Requests\Chat\UpdateThreadStatusRequest;
use App\Models\Chat;
use App\Models\ChatAttachment;
use App\Models\ChatCategory;
use App\Models\ChatLimits;
use App\Models\ChatTopics;
use App\Models\ConsutationTopics;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ChatController extends Controller
{
    private const PETANI_DAILY_CHAT_LIMIT = 5;

    public function index(Request $request): Response
    {
        $user = $request->user();
        $statusFilter = (string) $request->query('status', 'all');
        $mentorFilter = (string) $request->query('mentor_id', 'all');

        $threads = $this->threadQueryByRole($user, $statusFilter, $mentorFilter)
            ->with([
                'sender:id,name',
                'sender.subscriptions' => function ($query): void {
                    $query->with('plan:id,name')
                        ->latest('created_at')
                        ->latest('end_date')
                        ->latest('id');
                },
                'receiver:id,name',
                'assignedMentor:id,name',
                'topics.consultationTopic:id,title,category_id',
                'topics.consultationTopic.category:id,name',
            ])
            ->get();

        $threadPayloads = $threads
            ->map(function (Chat $thread) use ($user): array {
                $messages = $this->threadMessages($thread);
                $lastMessage = $messages->last();
                $topicPivot = $thread->topics->first();
                $topic = $topicPivot?->consultationTopic;

                $counterpartName = $thread->sender_id === $user->id
                    ? $thread->receiver?->name
                    : $thread->sender?->name;

                $senderSubscriptions = $thread->sender?->subscriptions;

                $activeSubscription = $senderSubscriptions?->first(function ($subscription): bool {
                    if ($subscription->status !== 'active') {
                        return false;
                    }

                    $isStartValid = ! $subscription->start_date || $subscription->start_date->isPast() || $subscription->start_date->isToday();
                    $isEndValid = ! $subscription->end_date || $subscription->end_date->isFuture() || $subscription->end_date->isToday();

                    return $isStartValid && $isEndValid;
                });

                $resolvedSubscription = $activeSubscription ?: $senderSubscriptions?->first();
                $resolvedPackageName = $resolvedSubscription?->plan?->name;

                $unreadCount = $messages
                    ->where('receiver_id', $user->id)
                    ->where('is_read', false)
                    ->count();

                return [
                    'id' => $thread->id,
                    'topic_title' => $topic?->title ?? 'Tanpa Topik',
                    'category_name' => $topic?->category?->name ?? '-',
                    'customer_name' => $thread->sender?->name ?? '-',
                    'customer_package_name' => $resolvedPackageName,
                    'customer_package_plan' => $this->resolvePackagePlan($resolvedPackageName),
                    'customer_package_status' => $resolvedSubscription?->status,
                    'counterpart_name' => $counterpartName ?? '-',
                    'status' => (string) $thread->status,
                    'assigned_mentor_id' => $thread->assigned_mentor_id,
                    'assigned_mentor_name' => $thread->assignedMentor?->name,
                    'last_message' => $lastMessage
                        ? ($lastMessage->message !== '' ? $lastMessage->message : '[Gambar]')
                        : $thread->message,
                    'last_time' => optional($lastMessage?->created_at ?? $thread->created_at)->format('d M H:i'),
                    'unread_count' => $unreadCount,
                    'messages' => $messages->map(fn (Chat $chat): array => [
                        'id' => $chat->id,
                        'sender_name' => $chat->sender?->name ?? '-',
                        'is_me' => $chat->sender_id === $user->id,
                        'message' => $chat->message,
                        'message_type' => $chat->message_type,
                        'attachment_url' => $chat->attachments->first()
                            ? asset('storage/'.$chat->attachments->first()->file_path)
                            : null,
                        'is_read' => (bool) $chat->is_read,
                        'time' => optional($chat->created_at)->format('H:i'),
                    ])->values(),
                ];
            })
            ->sortByDesc(function (array $thread): string {
                return (string) $thread['last_time'];
            })
            ->values();

        $selectedThreadId = $request->integer('thread');
        if (! $selectedThreadId) {
            $selectedThreadId = $threadPayloads->first()['id'] ?? null;
        }

        if ($selectedThreadId) {
            $this->markThreadAsRead($selectedThreadId, $user->id);
        }

        $topicCategories = ChatCategory::query()
            ->with(['consultationTopics' => fn ($query) => $query->orderBy('title')])
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (ChatCategory $category): array => [
                'id' => $category->id,
                'name' => $category->name,
                'topics' => $category->consultationTopics->map(fn (ConsutationTopics $topic): array => [
                    'id' => $topic->id,
                    'title' => $topic->title,
                ])->values(),
            ])
            ->values();

        $view = ($user->hasRole('admin') || $user->hasRole('mentor'))
            ? 'admin/chat/Index'
            : 'public/chat/Index';

        $mentors = $user->hasRole('admin')
            ? User::role('mentor')->orderBy('name')->get(['id', 'name'])->map(fn (User $mentor): array => [
                'id' => $mentor->id,
                'name' => $mentor->name,
            ])->values()
            : collect();

        return Inertia::render($view, [
            'threads' => $threadPayloads,
            'selectedThreadId' => $selectedThreadId,
            'topicCategories' => $topicCategories,
            'canStartTopic' => $user->hasRole('petani'),
            'role' => $this->resolveRole($user),
            'chatQuota' => $user->hasRole('petani') ? $this->dailyQuotaStatus($user->id) : null,
            'mentors' => $mentors,
            'filters' => [
                'status' => $statusFilter,
                'mentor_id' => $mentorFilter,
            ],
        ]);
    }

    public function startTopic(StartTopicChatRequest $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->hasRole('petani')) {
            abort(403, 'Hanya petani/customer yang dapat memulai topik chat.');
        }

        if ($this->isPetaniQuotaExceeded($user->id)) {
            return back()->withErrors([
                'initial_message' => 'Kuota chat harian Anda sudah habis. Maksimal 5 chat per hari.',
            ]);
        }

        $validated = $request->validated();

        $topic = ConsutationTopics::query()
            ->where('id', $validated['consultation_topic_id'])
            ->where('category_id', $validated['category_id'])
            ->first();

        if (! $topic) {
            return back()->withErrors([
                'consultation_topic_id' => 'Topik tidak sesuai dengan kategori yang dipilih.',
            ]);
        }

        $admin = User::role('admin')->orderBy('id')->first();
        if (! $admin) {
            return back()->withErrors([
                'topic' => 'Admin belum tersedia. Silakan hubungi tim support.',
            ]);
        }

        $thread = DB::transaction(function () use ($user, $admin, $validated, $topic): Chat {
            $thread = Chat::create([
                'sender_id' => $user->id,
                'receiver_id' => $admin->id,
                'thread_id' => null,
                'status' => 'new',
                'assigned_mentor_id' => null,
                'message' => $validated['initial_message'],
                'message_type' => 'text',
                'is_read' => false,
            ]);

            ChatTopics::create([
                'chat_id' => $thread->id,
                'consultation_topic_id' => $topic->id,
            ]);

            $this->incrementPetaniDailyQuota($user->id);

            return $thread;
        });

        $thread->load('assignedMentor:id,name');

        event(new ChatThreadUpdated($thread, 'created'));
        event(new ChatMessageSent($thread->id, $thread));

        return redirect()
            ->route($this->resolveIndexRouteName($user), ['thread' => $thread->id])
            ->with('success', 'Topik berhasil dibuat. Anda sekarang terhubung ke antrian admin.');
    }

    public function sendMessage(SendChatMessageRequest $request, Chat $thread): RedirectResponse
    {
        $user = $request->user();
        $rootThread = $thread->thread_id ? Chat::query()->findOrFail($thread->thread_id) : $thread;

        if (! $this->canAccessThread($user, $rootThread)) {
            abort(403, 'Anda tidak memiliki akses ke thread chat ini.');
        }

        if ($user->hasRole('petani') && $this->isPetaniQuotaExceeded($user->id)) {
            return back()->withErrors([
                'message' => 'Kuota chat harian Anda sudah habis. Maksimal 5 chat per hari.',
            ]);
        }

        $receiverId = $user->hasRole('petani')
            ? ($rootThread->assigned_mentor_id ?: $rootThread->receiver_id)
            : $rootThread->sender_id;

        $validated = $request->validated();

        $message = DB::transaction(function () use ($user, $receiverId, $rootThread, $request, $validated): Chat {
            $textMessage = trim((string) ($validated['message'] ?? ''));
            $hasImage = $request->hasFile('image');

            $message = Chat::create([
                'sender_id' => $user->id,
                'receiver_id' => $receiverId,
                'thread_id' => $rootThread->id,
                'message' => $textMessage,
                'message_type' => $hasImage ? 'image' : 'text',
                'is_read' => false,
            ]);

            if ($hasImage) {
                $storedPath = $request->file('image')->store('chat-attachments', 'public');

                ChatAttachment::create([
                    'chat_id' => $message->id,
                    'file_path' => $storedPath,
                ]);
            }

            if (! $user->hasRole('petani') && $rootThread->status === 'new') {
                $rootThread->update(['status' => 'in_progress']);
            }

            if ($user->hasRole('petani')) {
                $this->incrementPetaniDailyQuota($user->id);
            }

            return $message;
        });

        $rootThread->refresh();
        event(new ChatMessageSent($rootThread->id, $message));
        event(new ChatThreadUpdated($rootThread, 'message'));

        return redirect()->route($this->resolveIndexRouteName($user), ['thread' => $rootThread->id]);
    }

    public function assignMentor(AssignMentorRequest $request, Chat $thread): RedirectResponse
    {
        $user = $request->user();
        if (! $user->hasRole('admin')) {
            abort(403, 'Hanya admin yang dapat assign mentor.');
        }

        $rootThread = $thread->thread_id ? Chat::query()->findOrFail($thread->thread_id) : $thread;
        if (! $this->canAccessThread($user, $rootThread)) {
            abort(403, 'Thread tidak dapat diakses.');
        }

        $mentor = User::role('mentor')->where('id', $request->validated()['mentor_id'])->first();
        if (! $mentor) {
            return back()->withErrors([
                'mentor_id' => 'Mentor tidak valid.',
            ]);
        }

        $rootThread->update([
            'assigned_mentor_id' => $mentor->id,
            'receiver_id' => $mentor->id,
            'status' => $rootThread->status === 'resolved' ? 'resolved' : 'in_progress',
        ]);

        event(new ChatThreadUpdated($rootThread->fresh(), 'assigned'));

        return redirect()->route('admin.chat.index', ['thread' => $rootThread->id]);
    }

    public function updateStatus(UpdateThreadStatusRequest $request, Chat $thread): RedirectResponse
    {
        $user = $request->user();
        if (! ($user->hasRole('admin') || $user->hasRole('mentor'))) {
            abort(403, 'Akses ditolak.');
        }

        $rootThread = $thread->thread_id ? Chat::query()->findOrFail($thread->thread_id) : $thread;
        if (! $this->canAccessThread($user, $rootThread)) {
            abort(403, 'Thread tidak dapat diakses.');
        }

        $rootThread->update([
            'status' => $request->validated()['status'],
        ]);

        event(new ChatThreadUpdated($rootThread->fresh(), 'status'));

        return redirect()->back();
    }

    private function threadQueryByRole(User $user, string $statusFilter = 'all', string $mentorFilter = 'all')
    {
        $query = Chat::query()->rootThreads();

        if ($user->hasRole('petani')) {
            return $query->where('sender_id', $user->id);
        }

        if ($user->hasRole('admin') || $user->hasRole('mentor')) {
            $query->whereHas('sender.roles', fn ($roleQuery) => $roleQuery->where('name', 'petani'));

            if ($statusFilter !== 'all') {
                $query->where('status', $statusFilter);
            }

            if ($mentorFilter !== 'all') {
                $query->where('assigned_mentor_id', (int) $mentorFilter);
            }

            return $query;
        }

        return $query->whereRaw('1 = 0');
    }

    private function threadMessages(Chat $thread): Collection
    {
        return Chat::query()
            ->where('id', $thread->id)
            ->orWhere('thread_id', $thread->id)
            ->with(['sender:id,name', 'receiver:id,name', 'attachments:id,chat_id,file_path'])
            ->orderBy('created_at')
            ->get();
    }

    private function canAccessThread(User $user, Chat $thread): bool
    {
        if ($user->hasRole('admin') || $user->hasRole('mentor')) {
            return $thread->sender && $thread->sender->hasRole('petani');
        }

        if ($user->hasRole('petani')) {
            return $thread->sender_id === $user->id || $thread->receiver_id === $user->id;
        }

        return false;
    }

    private function markThreadAsRead(int $threadId, int $userId): void
    {
        Chat::query()
            ->where(function ($query) use ($threadId): void {
                $query->where('id', $threadId)
                    ->orWhere('thread_id', $threadId);
            })
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    private function resolveIndexRouteName(User $user): string
    {
        if ($user->hasRole('admin') || $user->hasRole('mentor')) {
            return 'admin.chat.index';
        }

        return 'public.chat.index';
    }

    private function resolveRole(User $user): string
    {
        if ($user->hasRole('admin')) {
            return 'admin';
        }

        if ($user->hasRole('mentor')) {
            return 'mentor';
        }

        return 'petani';
    }

    private function isPetaniQuotaExceeded(int $userId): bool
    {
        $todayUsage = ChatLimits::query()
            ->where('user_id', $userId)
            ->whereDate('date', now()->toDateString())
            ->value('limit');

        return (int) ($todayUsage ?? 0) >= self::PETANI_DAILY_CHAT_LIMIT;
    }

    private function incrementPetaniDailyQuota(int $userId): void
    {
        $dailyLimit = ChatLimits::query()->firstOrCreate(
            [
                'user_id' => $userId,
                'date' => now()->toDateString(),
            ],
            [
                'limit' => 0,
            ]
        );

        $dailyLimit->increment('limit');
    }

    private function dailyQuotaStatus(int $userId): array
    {
        $used = (int) (ChatLimits::query()
            ->where('user_id', $userId)
            ->whereDate('date', now()->toDateString())
            ->value('limit') ?? 0);

        return [
            'max' => self::PETANI_DAILY_CHAT_LIMIT,
            'used' => $used,
            'remaining' => max(0, self::PETANI_DAILY_CHAT_LIMIT - $used),
        ];
    }

    private function resolvePackagePlan(?string $packageName): ?string
    {
        if (! $packageName) {
            return null;
        }

        $normalized = Str::lower($packageName);

        if (Str::contains($normalized, 'basic')) {
            return 'basic';
        }

        if (Str::contains($normalized, 'premium')) {
            return 'premium';
        }

        if (Str::contains($normalized, 'elite')) {
            return 'elite';
        }

        return null;
    }
}
