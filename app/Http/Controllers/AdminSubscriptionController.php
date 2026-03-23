<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminSubscriptionController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status');
        $planId = $request->query('plan_id');

        $subscriptions = Subscription::query()
            ->with([
                'user:id,name,email',
                'plan:id,name,duration_months,price',
                'approver:id,name',
                'payments' => function ($query): void {
                    $query->select(['id', 'subscription_id', 'status'])
                        ->where('status', 'pending')
                        ->latest('id');
                },
            ])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->where('generation', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search): void {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhereHas('plan', function ($planQuery) use ($search): void {
                            $planQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when(
                in_array($status, ['pending', 'active', 'inactive', 'canceled'], true),
                fn ($query) => $query->where('status', $status)
            )
            ->when(
                is_numeric($planId),
                fn ($query) => $query->where('subscription_plan_id', (int) $planId)
            )
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('subscriptions/Index', [
            'subscriptions' => $subscriptions->getCollection()->map(function (Subscription $subscription): array {
                return [
                    'id' => $subscription->id,
                    'generation' => $subscription->generation,
                    'status' => $subscription->status,
                    'start_date' => optional($subscription->start_date)->format('Y-m-d'),
                    'end_date' => optional($subscription->end_date)->format('Y-m-d'),
                    'created_at' => optional($subscription->created_at)->format('Y-m-d H:i:s'),
                    'pending_payment_id' => $subscription->payments->first()?->id,
                    'user' => [
                        'id' => $subscription->user?->id,
                        'name' => $subscription->user?->name,
                        'email' => $subscription->user?->email,
                    ],
                    'plan' => [
                        'id' => $subscription->plan?->id,
                        'name' => $subscription->plan?->name,
                        'duration_months' => $subscription->plan?->duration_months,
                        'price' => $subscription->plan ? (float) $subscription->plan->price : null,
                    ],
                    'approver' => [
                        'id' => $subscription->approver?->id,
                        'name' => $subscription->approver?->name,
                    ],
                ];
            })->values(),
            'pagination' => [
                'current_page' => $subscriptions->currentPage(),
                'last_page' => $subscriptions->lastPage(),
                'per_page' => $subscriptions->perPage(),
                'total' => $subscriptions->total(),
                'from' => $subscriptions->firstItem(),
                'to' => $subscriptions->lastItem(),
                'links' => $subscriptions->linkCollection()->toArray(),
            ],
            'filters' => [
                'search' => $search,
                'status' => $status,
                'plan_id' => is_numeric($planId) ? (int) $planId : null,
            ],
            'plans' => SubscriptionPlan::query()
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn (SubscriptionPlan $plan): array => [
                    'id' => $plan->id,
                    'name' => $plan->name,
                ])
                ->values(),
        ]);
    }

    public function show(Subscription $subscription): RedirectResponse
    {
        $pendingPayment = $subscription->payments()
            ->where('status', 'pending')
            ->latest('id')
            ->first();

        if ($pendingPayment) {
            return redirect()->route('admin.subscriptions.payments.show', ['payment' => $pendingPayment->id]);
        }

        return redirect()->route('subscriptions.index')->withErrors([
            'subscription' => 'Subscription ini tidak memiliki pembayaran pending untuk ditinjau.',
        ]);
    }

    public function update(Request $request, Subscription $subscription): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,active,inactive,canceled',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'generation' => 'nullable|integer',
        ]);

        $payload = [
            'status' => $validated['status'],
            'approved_by' => $request->user()->id,
        ];

        if (array_key_exists('start_date', $validated)) {
            $payload['start_date'] = $validated['start_date'];
        }

        if (array_key_exists('end_date', $validated)) {
            $payload['end_date'] = $validated['end_date'];
        }

        if (array_key_exists('generation', $validated) && $validated['generation'] !== null) {
            $payload['generation'] = (string) $validated['generation'];
        }

        $subscription->update($payload);

        return redirect()->route('subscriptions.index')->with('success', 'Subscription berhasil diperbarui.');
    }
}
