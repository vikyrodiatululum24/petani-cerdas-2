<?php

namespace App\Http\Controllers;

use App\Models\LiveSession;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LiveSessionController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $ownedGenerations = $user->subscriptions()
            ->whereIn('status', ['active', 'inactive'])
            ->whereNotNull('generation')
            ->where('generation', '!=', '')
            ->whereHas('plan', function (Builder $planQuery): void {
                $planQuery->whereIn('name', ['premium', 'elite']);
            })
            ->pluck('generation')
            ->unique()
            ->all();

        $sessions = LiveSession::query()
            ->with([
                'subscription:id,subscription_plan_id,generation,status',
                'subscription.plan:id,name',
            ])
            ->where(function (Builder $query) use ($ownedGenerations): void {
                $query->whereIn('generation', $ownedGenerations)
                    ->orWhereHas('subscription', function (Builder $subscriptionQuery) use ($ownedGenerations): void {
                        $subscriptionQuery->whereIn('generation', $ownedGenerations);
                    });
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
                    'generation' => $session->generation ?? $session->subscription?->generation,
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
                        })
                        ->orWhere('generation', 'like', "%{$search}%");
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
                    'generation' => $session->generation ?? $session->subscription?->generation,
                ];
            })
            ->values();

        $generationOptions = Subscription::query()
            ->currentlyActive()
            ->whereNotNull('generation')
            ->where('generation', '!=', '')
            ->select('generation')
            ->distinct()
            ->orderByDesc('generation')
            ->pluck('generation')
            ->map(fn (string $generation): array => [
                'value' => $generation,
                'label' => sprintf('Generation %s', $generation),
            ])
            ->values();

        return Inertia::render('admin/live-sessions/Index', [
            'sessions' => $sessions,
            'generationOptions' => $generationOptions,
            'filters' => [
                'search' => $search,
                'status' => $status,
            ],
        ]);
    }

    public function adminStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'generation' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'scheduled_at' => 'required|date',
            'status' => 'required|in:scheduled,live,ended',
            'stream_url' => 'nullable|url|max:2048',
        ]);

        LiveSession::create([
            'user_id' => null,
            'subscription_id' => null,
            'generation' => $validated['generation'],
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
            'generation' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'scheduled_at' => 'required|date',
            'status' => 'required|in:scheduled,live,ended',
            'stream_url' => 'nullable|url|max:2048',
        ]);

        $liveSession->update([
            'user_id' => null,
            'subscription_id' => null,
            'generation' => $validated['generation'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'scheduled_at' => $validated['scheduled_at'],
            'status' => $validated['status'],
            'stream_url' => $validated['stream_url'] ?? null,
        ]);

        return redirect()->route('admin.live-sessions.index')->with('success', 'Jadwal live session berhasil diperbarui.');
    }
}
