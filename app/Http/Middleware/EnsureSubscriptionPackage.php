<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscriptionPackage
{
    /**
     * Ensure authenticated petani has active subscription plan in allowed packages.
     */
    public function handle(Request $request, Closure $next, string ...$packages): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasRole('petani')) {
            abort(403, 'Akses hanya untuk petani.');
        }

        if (empty($packages)) {
            return $next($request);
        }

        $allowedPackages = collect($packages)
            ->map(fn (string $package) => Str::slug(Str::lower(trim($package))))
            ->filter()
            ->values();

        $activeSubscription = $user->subscriptions()
            ->with('plan:id,name')
            ->currentlyActive()
            ->latest('end_date')
            ->latest('id')
            ->first();

        if (! $activeSubscription || ! $activeSubscription->plan) {
            abort(403, 'Anda belum memiliki paket subscription aktif.');
        }

        $currentPackage = Str::slug(Str::lower((string) $activeSubscription->plan->name));

        if (! $allowedPackages->contains($currentPackage)) {
            abort(403, 'Paket subscription Anda tidak memiliki akses ke fitur ini.');
        }

        return $next($request);
    }
}
