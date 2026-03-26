<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\LiveSession;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $stats = [
            'users_total' => User::query()->count(),
            'articles_published' => Article::query()->where('is_published', true)->count(),
            'products_total' => Product::query()->count(),
            'subscriptions_active' => Subscription::query()->where('status', 'active')->count(),
            'subscriptions_pending' => Subscription::query()->where('status', 'pending')->count(),
            'payments_pending' => Payment::query()->where('status', 'pending')->count(),
            'payments_completed_amount' => (float) Payment::query()->where('status', 'completed')->sum('amount'),
            'live_sessions_upcoming' => LiveSession::query()
                ->whereIn('status', ['scheduled', 'live'])
                ->where('scheduled_at', '>=', now())
                ->count(),
        ];

        $recentSubscriptions = Subscription::query()
            ->with(['user:id,name,email', 'plan:id,name'])
            ->latest()
            ->take(5)
            ->get()
            ->map(fn (Subscription $subscription): array => [
                'id' => $subscription->id,
                'user_name' => $subscription->user?->name,
                'user_email' => $subscription->user?->email,
                'plan_name' => $subscription->plan?->name,
                'status' => $subscription->status,
                'created_at' => optional($subscription->created_at)->toIso8601String(),
            ])
            ->values();

        $recentPayments = Payment::query()
            ->with(['subscription.user:id,name'])
            ->latest()
            ->take(5)
            ->get()
            ->map(fn (Payment $payment): array => [
                'id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'user_name' => $payment->subscription?->user?->name,
                'amount' => (float) $payment->amount,
                'status' => $payment->status,
                'created_at' => optional($payment->created_at)->toIso8601String(),
            ])
            ->values();

        $upcomingLiveSessions = LiveSession::query()
            ->with('user:id,name')
            ->whereIn('status', ['scheduled', 'live'])
            ->where('scheduled_at', '>=', now()->subHour())
            ->orderBy('scheduled_at')
            ->take(5)
            ->get()
            ->map(fn (LiveSession $session): array => [
                'id' => $session->id,
                'title' => $session->title,
                'mentor_name' => $session->user?->name,
                'status' => $session->status,
                'scheduled_at' => optional($session->scheduled_at)->toIso8601String(),
            ])
            ->values();

        return Inertia::render('Dashboard', [
            'summary' => $stats,
            'recentSubscriptions' => $recentSubscriptions,
            'recentPayments' => $recentPayments,
            'upcomingLiveSessions' => $upcomingLiveSessions,
            'userInfo' => [
                'name' => $user?->name,
                'roles' => $user?->getRoleNames()->values()->all() ?? [],
            ],
        ]);
    }
}
