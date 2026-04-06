<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionController extends Controller
{
    public function packageIndex(Request $request): Response
    {
        $activeSubscription = $request->user()
            ->subscriptions()
            ->with('plan:id,name')
            ->currentlyActive()
            ->latest('end_date')
            ->latest('id')
            ->first();

        $activePlanRank = $activeSubscription?->plan ? $this->resolvePlanRank($activeSubscription->plan->name) : null;

        $usedByPlan = Subscription::query()
            ->selectRaw('subscription_plan_id, COUNT(*) as used_count')
            ->currentlyActive()
            ->groupBy('subscription_plan_id')
            ->pluck('used_count', 'subscription_plan_id');

        $plans = SubscriptionPlan::query()
            ->get(['id', 'name', 'price', 'discount_price', 'quota', 'features', 'description', 'duration_months', 'is_featured'])
            ->map(function (SubscriptionPlan $plan) use ($usedByPlan, $activePlanRank): array {
                $price = (float) $plan->price;
                $discountPrice = $plan->discount_price !== null ? (float) $plan->discount_price : null;

                $planKey = Str::lower(trim($plan->name));

                $maxByPackage = match ($planKey) {
                    'basic' => 30,
                    'premium' => 20,
                    'elite' => 5,
                    default => (int) ($plan->quota ?? 0),
                };

                $usedSlots = (int) ($usedByPlan[$plan->id] ?? 0);
                $remainingSlots = max(0, $maxByPackage - $usedSlots);
                $isAvailable = $remainingSlots > 0;
                $planRank = $this->resolvePlanRank($plan->name);
                $canUpgradeFromActive = $activePlanRank === null
                    || ($planRank > $activePlanRank);
                $canCheckout = $isAvailable && $canUpgradeFromActive;
                $cta = ! $isAvailable
                    ? 'Sold Out'
                    : (! $canUpgradeFromActive ? 'Tidak bisa dipilih' : 'pilih paket');

                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'price' => $price,
                    'discount_price' => $discountPrice,
                    'quota' => $maxByPackage,
                    'features' => $plan->features ?? [],
                    'description' => $plan->description,
                    'duration_months' => $plan->duration_months,
                    'is_featured' => (bool) $plan->is_featured,
                    'final_price' => $discountPrice ?: $price,
                    'used_slots' => $usedSlots,
                    'remaining_slots' => $remainingSlots,
                    'available' => $isAvailable,
                    'can_checkout' => $canCheckout,
                    'cta' => $cta,
                ];
            })
            ->values();

        return Inertia::render('public/subscription/Packages', [
            'plans' => $plans,
            'activeSubscription' => $activeSubscription ? [
                'id' => $activeSubscription->id,
                'plan_name' => $activeSubscription->plan?->name,
                'status' => $activeSubscription->status,
                'end_date' => optional($activeSubscription->end_date)->format('Y-m-d'),
            ] : null,
        ]);
    }

    public function checkout(Request $request, SubscriptionPlan $subscriptionPlan): RedirectResponse
    {
        $activeSubscription = $request->user()
            ->subscriptions()
            ->with('plan:id,name')
            ->currentlyActive()
            ->latest('end_date')
            ->latest('id')
            ->first();

        if ($activeSubscription && $activeSubscription->plan) {
            $activeRank = $this->resolvePlanRank($activeSubscription->plan->name);
            $targetRank = $this->resolvePlanRank($subscriptionPlan->name);

            if ($targetRank <= $activeRank) {
                return back()->withErrors([
                    'subscription' => 'Anda masih memiliki paket aktif. Pembelian hanya diizinkan untuk upgrade ke paket yang lebih tinggi.',
                ]);
            }
        }

        $planKey = Str::lower(trim($subscriptionPlan->name));
        $maxQuota = match ($planKey) {
            'basic' => 30,
            'premium' => 20,
            'elite' => 5,
            default => (int) ($subscriptionPlan->quota ?? 0),
        };

        $usedQuota = Subscription::query()
            ->where('subscription_plan_id', $subscriptionPlan->id)
            ->currentlyActive()
            ->count();

        if ($usedQuota >= $maxQuota) {
            return back()->withErrors([
                'subscription' => 'Kuota paket sudah penuh. Silakan pilih paket lain.',
            ]);
        }

        $subscription = Subscription::create([
            'user_id' => $request->user()->id,
            'subscription_plan_id' => $subscriptionPlan->id,
            'generation' => now()->format('Ym'),
            'status' => 'pending',
            'start_date' => null,
            'end_date' => null,
            'approved_by' => null,
        ]);

        return redirect()
            ->route('petani.subscriptions.payments.show', ['subscription' => $subscription->id])
            ->with('success', 'Paket berhasil dipilih. Silakan lanjutkan ke pembayaran.');
    }

    private function resolvePlanRank(string $planName): int
    {
        return match (Str::lower(trim($planName))) {
            'basic' => 1,
            'premium' => 2,
            'elite' => 3,
            default => 0,
        };
    }

}
