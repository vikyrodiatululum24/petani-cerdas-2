<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\LiveSession;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PetaniProfileController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $user->load('profile');

        $subscriptions = $user->subscriptions()
            ->with(['plan:id,name,price,duration_months', 'approver:id,name'])
            ->latest('id')
            ->get()
            ->map(function ($subscription): array {
                return [
                    'id' => $subscription->id,
                    'generation' => $subscription->generation,
                    'status' => $subscription->status,
                    'start_date' => optional($subscription->start_date)->format('Y-m-d'),
                    'end_date' => optional($subscription->end_date)->format('Y-m-d'),
                    'plan_name' => $subscription->plan?->name,
                    'plan_price' => $subscription->plan ? (float) $subscription->plan->price : null,
                    'plan_duration_months' => $subscription->plan?->duration_months,
                    'approved_by' => $subscription->approver?->name,
                ];
            })
            ->values();

        $payments = Payment::query()
            ->whereHas('subscription', fn ($query) => $query->where('user_id', $user->id))
            ->with('subscription.plan:id,name')
            ->latest('id')
            ->limit(15)
            ->get()
            ->map(function (Payment $payment): array {
                return [
                    'id' => $payment->id,
                    'transaction_id' => $payment->transaction_id,
                    'amount' => (float) $payment->amount,
                    'status' => $payment->status,
                    'paid_at' => optional($payment->paid_at)->format('Y-m-d H:i'),
                    'plan_name' => $payment->subscription?->plan?->name,
                    'proof_of_payment_url' => $payment->proof_of_payment_path ? asset('storage/'.$payment->proof_of_payment_path) : null,
                ];
            })
            ->values();

        $chatThreads = Chat::query()
            ->rootThreads()
            ->where(function ($query) use ($user): void {
                $query->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            })
            ->with(['sender:id,name', 'receiver:id,name', 'assignedMentor:id,name'])
            ->withCount('replies')
            ->latest('updated_at')
            ->limit(10)
            ->get()
            ->map(function (Chat $thread) use ($user): array {
                $counterpart = $thread->sender_id === $user->id ? $thread->receiver : $thread->sender;

                return [
                    'id' => $thread->id,
                    'title' => $thread->message,
                    'status' => $thread->status,
                    'counterpart_name' => $counterpart?->name,
                    'mentor_name' => $thread->assignedMentor?->name,
                    'total_messages' => 1 + (int) $thread->replies_count,
                    'updated_at' => optional($thread->updated_at)->format('Y-m-d H:i'),
                ];
            })
            ->values();

        $liveSessions = LiveSession::query()
            ->where('user_id', $user->id)
            ->with('subscription.plan:id,name')
            ->orderByDesc('scheduled_at')
            ->limit(15)
            ->get()
            ->map(function (LiveSession $session): array {
                return [
                    'id' => $session->id,
                    'title' => $session->title,
                    'status' => $session->status,
                    'scheduled_at' => optional($session->scheduled_at)->translatedFormat('d M Y, H:i'),
                    'plan_name' => $session->subscription?->plan?->name,
                    'stream_url' => $session->stream_url,
                ];
            })
            ->values();

        $petani = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => optional($user->created_at)->format('Y-m-d'),
            'profile' => [
                'phone' => $user->profile?->phone,
                'main_crop' => $user->profile?->main_crop,
                'land_type' => $user->profile?->land_type,
                'province' => $user->profile?->province,
                'city' => $user->profile?->city,
                'district' => $user->profile?->district,
                'village' => $user->profile?->village,
                'postal_code' => $user->profile?->postal_code,
                'address' => $user->profile?->address,
                'photo_url' => $user->profile?->photo ? asset('storage/'.$user->profile->photo) : null,
            ],
        ];

        return Inertia::render('public/profile/Index', [
            'petani' => $petani,
            'summary' => [
                'subscriptions_total' => $subscriptions->count(),
                'subscriptions_active' => $subscriptions->where('status', 'active')->count(),
                'payments_total' => $payments->count(),
                'chats_total' => $chatThreads->count(),
                'live_sessions_total' => $liveSessions->count(),
            ],
            'subscriptions' => $subscriptions,
            'payments' => $payments,
            'chatThreads' => $chatThreads,
            'liveSessions' => $liveSessions,
        ]);
    }

    public function edit(Request $request): Response
    {
        $user = $request->user();
        $user->load('profile');

        return Inertia::render('public/profile/Edit', [
            'formData' => [
                'name' => $user->name,
                'email' => $user->email,
                'photo_url' => $user->profile?->photo ? asset('storage/'.$user->profile->photo) : null,
                'phone' => $user->profile?->phone,
                'main_crop' => $user->profile?->main_crop,
                'land_type' => $user->profile?->land_type,
                'province' => $user->profile?->province,
                'city' => $user->profile?->city,
                'district' => $user->profile?->district,
                'village' => $user->profile?->village,
                'postal_code' => $user->profile?->postal_code,
                'address' => $user->profile?->address,
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'phone' => 'nullable|string|max:30',
            'main_crop' => 'nullable|string|max:100',
            'land_type' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'village' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')?->store('users/profile-photos', 'public');
        }

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'photo' => $photoPath ?? $user->profile?->photo,
                'phone' => $validated['phone'] ?? null,
                'main_crop' => $validated['main_crop'] ?? null,
                'land_type' => $validated['land_type'] ?? null,
                'province' => $validated['province'] ?? null,
                'city' => $validated['city'] ?? null,
                'district' => $validated['district'] ?? null,
                'village' => $validated['village'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'address' => $validated['address'] ?? null,
            ]
        );

        return redirect()->route('petani.profile.index')->with('success', 'Profil petani berhasil diperbarui.');
    }
}
