<?php

namespace App\Http\Controllers;

use App\Http\Requests\Subscription\ApproveSubscriptionRequest;
use App\Http\Requests\Subscription\RejectSubscriptionRequest;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function show(Request $request, Subscription $subscription): Response
    {
        abort_unless($subscription->user_id === $request->user()->id, 403, 'Anda tidak memiliki akses ke pembayaran ini.');

        $subscription->load('plan:id,name,price,discount_price,duration_months');

        $price = (float) $subscription->plan->price;
        $discountPrice = $subscription->plan->discount_price !== null ? (float) $subscription->plan->discount_price : null;
        $finalAmount = $discountPrice ?: $price;

        return Inertia::render('public/subscription/Payment', [
            'subscription' => [
                'id' => $subscription->id,
                'generation' => $subscription->generation,
                'status' => $subscription->status,
                'plan' => [
                    'name' => $subscription->plan->name,
                    'duration_months' => $subscription->plan->duration_months,
                    'price' => $price,
                    'discount_price' => $discountPrice,
                    'final_amount' => $finalAmount,
                ],
            ],
            'destinationAccounts' => $this->destinationAccounts(),
            'defaultPaymentMethod' => 'bank_transfer',
        ]);
    }

    public function store(Request $request, Subscription $subscription): RedirectResponse
    {
        abort_unless($subscription->user_id === $request->user()->id, 403, 'Anda tidak memiliki akses ke pembayaran ini.');

        $subscription->load('plan:id,price,discount_price');

        $validated = $request->validate([
            'payment_method' => 'required|in:bank_transfer',
            'destination_account' => 'required|string',
            'sender_name' => 'required|string|max:255',
            'notes' => 'nullable|string|max:500',
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $destinationAccounts = collect($this->destinationAccounts())->keyBy('key');
        $selectedAccount = $destinationAccounts->get((string) $validated['destination_account']);

        if (! $selectedAccount) {
            return back()->withErrors([
                'destination_account' => 'Pilihan rekening tujuan tidak valid.',
            ]);
        }

        $price = (float) $subscription->plan->price;
        $discountPrice = $subscription->plan->discount_price !== null ? (float) $subscription->plan->discount_price : null;
        $finalAmount = $discountPrice ?: $price;

        $proofPath = $request->file('payment_proof')?->store('payments/proofs', 'public');

        if (! $proofPath) {
            return back()->withErrors([
                'payment_proof' => 'Bukti pembayaran gagal diunggah. Coba lagi.',
            ]);
        }

        Payment::create([
            'subscription_id' => $subscription->id,
            'amount' => $finalAmount,
            'status' => 'pending',
            'payment_method' => $validated['payment_method'],
            'transaction_id' => strtoupper('TRX-'.now()->format('YmdHis').'-'.str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT)),
            'paid_at' => now(),
            'destination_account' => sprintf('%s - %s - %s', $selectedAccount['bank_name'], $selectedAccount['account_number'], $selectedAccount['account_holder']),
            'sender_name' => $validated['sender_name'],
            'notes' => $validated['notes'] ?? null,
            'proof_of_payment_path' => $proofPath,
        ]);

        return redirect()
            ->route('petani.subscriptions.packages.index')
            ->with('success', 'Pembayaran berhasil dikirim. Tim kami akan segera memverifikasi pembayaran Anda.');
    }

    /**
     * Admin: List pending payments for review
     */
    public function adminIndex(): Response
    {
        $payments = Payment::query()
            ->where('status', 'pending')
            ->with(['subscription' => function ($query) {
                $query->with(['plan:id,name', 'user:id,name,email']);
            }])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'transaction_id' => $payment->transaction_id,
                    'amount' => (float) $payment->amount,
                    'status' => $payment->status,
                    'paid_at' => $payment->paid_at,
                    'created_at' => $payment->created_at,
                    'subscription' => [
                        'id' => $payment->subscription->id,
                        'status' => $payment->subscription->status,
                        'plan' => [
                            'id' => $payment->subscription->plan->id,
                            'name' => $payment->subscription->plan->name,
                            'key' => $this->resolvePlanKey($payment->subscription->plan->name),
                        ],
                    ],
                    'user' => [
                        'id' => $payment->subscription->user->id,
                        'name' => $payment->subscription->user->name,
                        'email' => $payment->subscription->user->email,
                    ],
                ];
            });

        return Inertia::render('admin/subscriptions/Index', [
            'payments' => $payments,
        ]);
    }

    /**
     * Admin: Show detail of a pending payment
     */
    public function adminShow(Payment $payment): Response
    {
        abort_unless($payment->status === 'pending', 403, 'Pembayaran ini sudah diproses.');

        $payment->load(['subscription' => function ($query) {
            $query->with([
                'plan:id,name,price',
                'user:id,name,email',
                'user.profile:user_id,phone',
            ]);
        }]);

        return Inertia::render('admin/subscriptions/Show', [
            'payment' => [
                'id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'amount' => (float) $payment->amount,
                'status' => $payment->status,
                'payment_method' => $payment->payment_method,
                'destination_account' => $payment->destination_account,
                'sender_name' => $payment->sender_name,
                'sender_bank' => $payment->sender_bank,
                'paid_at' => $payment->paid_at,
                'notes' => $payment->notes,
                'proof_of_payment_path' => $payment->proof_of_payment_path ? asset('storage/'.$payment->proof_of_payment_path) : null,
                'created_at' => $payment->created_at,
                'subscription' => [
                    'id' => $payment->subscription->id,
                    'status' => $payment->subscription->status,
                    'generation' => $payment->subscription->generation,
                    'plan' => [
                        'id' => $payment->subscription->plan->id,
                        'name' => $payment->subscription->plan->name,
                        'key' => $this->resolvePlanKey($payment->subscription->plan->name),
                        'price' => (float) $payment->subscription->plan->price,
                    ],
                ],
                'user' => [
                    'id' => $payment->subscription->user->id,
                    'name' => $payment->subscription->user->name,
                    'email' => $payment->subscription->user->email,
                    'phone' => $payment->subscription->user->profile?->phone,
                ],
            ],
        ]);
    }

    /**
     * Admin: Approve a payment and activate subscription
     */
    public function approve(ApproveSubscriptionRequest $request, Payment $payment): RedirectResponse
    {
        abort_unless($payment->status === 'pending', 403, 'Pembayaran ini sudah diproses.');

        $validated = $request->validated();

        $payment->subscription->load('plan:id,duration_months');

        // Use transaction to ensure consistency
        DB::transaction(function () use ($payment, $validated, $request) {
            // Update subscription: mark as active
            $payment->subscription->update([
                'status' => 'active',
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'generation' => (string) $validated['generation'],
                'approved_by' => $request->user()->id,
            ]);

            // Update payment: mark as completed
            $payment->update(['status' => 'completed']);
        });

        return redirect()
            ->route('admin.subscriptions.payments.index')
            ->with('success', 'Pembayaran berhasil disetujui dan subscription diaktifkan.');
    }

    /**
     * Admin: Reject a payment
     */
    public function reject(RejectSubscriptionRequest $request, Payment $payment): RedirectResponse
    {
        abort_unless($payment->status === 'pending', 403, 'Pembayaran ini sudah diproses.');

        $validated = $request->validated();

        // Use transaction to ensure consistency
        DB::transaction(function () use ($payment, $validated, $request) {
            // Update subscription: mark as canceled
            $payment->subscription->update([
                'status' => 'canceled',
                'approved_by' => $request->user()->id,
            ]);

            // Update payment: mark as failed and optionally store reason
            $notes = $payment->notes ? $payment->notes."\n" : '';
            $notes .= $validated['reason'] ? 'REJECT_REASON: '.$validated['reason'] : 'REJECT_REASON: Pembayaran ditolak oleh admin';

            $payment->update([
                'status' => 'failed',
                'notes' => $notes,
            ]);
        });

        return redirect()
            ->route('admin.subscriptions.payments.index')
            ->with('success', 'Pembayaran berhasil ditolak.');
    }

    private function destinationAccounts(): array
    {
        return [
            [
                'key' => 'bca_merchant',
                'bank_name' => 'BCA',
                'account_number' => '1234567890',
                'account_holder' => 'PT Petani Cerdas Indonesia',
            ],
            [
                'key' => 'bri_merchant',
                'bank_name' => 'BRI',
                'account_number' => '987601234567890',
                'account_holder' => 'PT Petani Cerdas Indonesia',
            ],
            [
                'key' => 'mandiri_merchant',
                'bank_name' => 'Mandiri',
                'account_number' => '1320019988776',
                'account_holder' => 'PT Petani Cerdas Indonesia',
            ],
        ];
    }

    private function resolvePlanKey(?string $planName): ?string
    {
        if (! $planName) {
            return null;
        }

        $normalized = strtolower(trim($planName));

        return match (true) {
            str_contains($normalized, 'elite') => 'elite',
            str_contains($normalized, 'premium') => 'premium',
            str_contains($normalized, 'basic') => 'basic',
            default => null,
        };
    }
}
