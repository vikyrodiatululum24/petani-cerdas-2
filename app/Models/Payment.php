<?php

namespace App\Models;

use App\Models\Concerns\DeletesPublicFiles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use DeletesPublicFiles, HasFactory;

    private const PUBLIC_FILE_COLUMNS = [
        'proof_of_payment_path',
    ];

    protected $fillable = [
        'subscription_id',
        'amount',
        'status',
        'payment_method',
        'transaction_id',
        'paid_at',
        'destination_account',
        'sender_name',
           'sender_bank',
           'notes',
        'proof_of_payment_path',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
