<?php

namespace App\Http\Controllers;

use App\Models\LiveSession;
use App\Models\Subscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LiveSessionController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $subscriptionIds = $user->subscriptions()
            ->pluck('subscriptions.id')
            ->all();

        $sessions = LiveSession::query()
            ->with([
                'subscription:id,subscription_plan_id,status',
                'subscription.plan:id,name',
            ])
            ->where(function ($query) use ($user, $subscriptionIds): void {
                $query->where('user_id', $user->id)
                    ->orWhereIn('subscription_id', $subscriptionIds);
            })
            ->orderByDesc('scheduled_at')
            ->get()
            ->map(function (LiveSession $session): array {
                return [
                    'id' => $session->id,
                    'title' => $session->title,
                    'description' => $session->description,
                    'scheduled_at' => optional($session->scheduled_at)->toIso8601String(),
                    'scheduled_label' => optional($session->scheduled_at)?->translatedFormat('d M Y, H:i'),
                    'status' => $session->status,
                    'stream_url' => $session->stream_url,
                    'plan_name' => $session->subscription?->plan?->name,
                ];
            })
            ->values();

        return Inertia::render('public/live-sessions/Index', [
            'sessions' => $sessions,
        ]);
    }

    public function adminIndex(Request $request): Response
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', '');

        $sessions = LiveSession::query()
            ->with([
                'subscription:id,user_id,subscription_plan_id,generation,status',
                'subscription.user:id,name,email',
                'subscription.plan:id,name',
            ])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('subscription.user', function ($userQuery) use ($search): void {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhereHas('subscription.plan', function ($planQuery) use ($search): void {
                            $planQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when(in_array($status, ['scheduled', 'live', 'ended'], true), fn ($query) => $query->where('status', $status))
            ->orderByDesc('scheduled_at')
            ->get()
            ->map(function (LiveSession $session): array {
                return [
                    'id' => $session->id,
                    'title' => $session->title,
                    'description' => $session->description,
                    'scheduled_at' => optional($session->scheduled_at)->format('Y-m-d\TH:i'),
                    'scheduled_label' => optional($session->scheduled_at)?->translatedFormat('d M Y, H:i'),
                    'status' => $session->status,
                    'stream_url' => $session->stream_url,
                    'subscription_id' => $session->subscription_id,
                    'user_name' => $session->subscription?->user?->name,
                    'user_email' => $session->subscription?->user?->email,
                    'plan_name' => $session->subscription?->plan?->name,
                    'generation' => $session->subscription?->generation,
                ];
            })
            ->values();

        $subscriptionOptions = Subscription::query()
            ->with(['user:id,name,email', 'plan:id,name'])
            ->whereIn('status', ['active', 'pending'])
            ->orderByDesc('id')
            ->get()
            ->map(function (Subscription $subscription): array {
                return [
                    'id' => $subscription->id,
                    'label' => trim(sprintf(
                        '%s - %s (%s)',
                        $subscription->user?->name ?? '-',
                        $subscription->plan?->name ?? '-',
                        $subscription->generation ?? '-'
                    )),
                    'user_id' => $subscription->user_id,
                ];
            })
            ->values();

        return Inertia::render('admin/live-sessions/Index', [
            'sessions' => $sessions,
            'subscriptionOptions' => $subscriptionOptions,
            'filters' => [
                'search' => $search,
                'status' => $status,
            ],
        ]);
    }

    public function adminStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'scheduled_at' => 'required|date',
            'status' => 'required|in:scheduled,live,ended',
            'stream_url' => 'nullable|url|max:2048',
        ]);

        $subscription = Subscription::query()->findOrFail((int) $validated['subscription_id']);

        LiveSession::create([
            'user_id' => $subscription->user_id,
            'subscription_id' => $subscription->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'scheduled_at' => $validated['scheduled_at'],
            'status' => $validated['status'],
            'stream_url' => $validated['stream_url'] ?? null,
        ]);

        return redirect()->route('admin.live-sessions.index')->with('success', 'Jadwal live session berhasil dibuat.');
    }

    public function adminUpdate(Request $request, LiveSession $liveSession): RedirectResponse
    {
        $validated = $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'scheduled_at' => 'required|date',
            'status' => 'required|in:scheduled,live,ended',
            'stream_url' => 'nullable|url|max:2048',
        ]);

        $subscription = Subscription::query()->findOrFail((int) $validated['subscription_id']);

        $liveSession->update([
            'user_id' => $subscription->user_id,
            'subscription_id' => $subscription->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'scheduled_at' => $validated['scheduled_at'],
            'status' => $validated['status'],
            'stream_url' => $validated['stream_url'] ?? null,
        ]);

        return redirect()->route('admin.live-sessions.index')->with('success', 'Jadwal live session berhasil diperbarui.');
    }
}
