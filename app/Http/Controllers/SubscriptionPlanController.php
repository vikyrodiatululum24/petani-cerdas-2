<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscriptionPlan\StoreSubscriptionPlanRequest;
use App\Http\Requests\SubscriptionPlan\SubscriptionPlanIndexRequest;
use App\Http\Requests\SubscriptionPlan\UpdateSubscriptionPlanRequest;
use App\Http\Resources\Subscription\SubscriptionPlanResource;
use App\Models\SubscriptionPlan;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionPlanController extends Controller
{
    public function index(SubscriptionPlanIndexRequest $request): Response
    {
        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 10);

        $plans = SubscriptionPlan::query()
            ->search($filters['search'] ?? null)
            ->filter($filters)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('subscription-plans/Index', [
            'plans' => SubscriptionPlanResource::collection($plans->getCollection())->resolve(),
            'pagination' => [
                'current_page' => $plans->currentPage(),
                'last_page' => $plans->lastPage(),
                'per_page' => $plans->perPage(),
                'total' => $plans->total(),
                'from' => $plans->firstItem(),
                'to' => $plans->lastItem(),
                'links' => $plans->linkCollection()->toArray(),
            ],
            'filters' => [
                'search' => $filters['search'] ?? '',
                'featured' => $filters['featured'] ?? null,
                'per_page' => $perPage,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('subscription-plans/Create');
    }

    public function store(StoreSubscriptionPlanRequest $request): RedirectResponse
    {
        SubscriptionPlan::create($request->validated());

        return redirect()->route('subscription-plans.index')->with('success', 'Paket langganan berhasil dibuat.');
    }

    public function show(SubscriptionPlan $subscriptionPlan): Response
    {
        return Inertia::render('subscription-plans/Show', [
            'plan' => SubscriptionPlanResource::make($subscriptionPlan)->resolve(),
        ]);
    }

    public function edit(SubscriptionPlan $subscriptionPlan): Response
    {
        return Inertia::render('subscription-plans/Edit', [
            'plan' => SubscriptionPlanResource::make($subscriptionPlan)->resolve(),
        ]);
    }

    public function update(UpdateSubscriptionPlanRequest $request, SubscriptionPlan $subscriptionPlan): RedirectResponse
    {
        $subscriptionPlan->update($request->validated());

        return redirect()->route('subscription-plans.index')->with('success', 'Paket langganan berhasil diperbarui.');
    }

    public function destroy(SubscriptionPlan $subscriptionPlan): RedirectResponse
    {
        $subscriptionPlan->delete();

        return redirect()->route('subscription-plans.index')->with('success', 'Paket langganan berhasil dihapus.');
    }
}
