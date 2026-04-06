<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'generation',
        'start_date',
        'end_date',
        'status',
        'approved_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function liveSessions(): HasMany
    {
        return $this->hasMany(LiveSession::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeCurrentlyActive(Builder $query, ?CarbonInterface $date = null): Builder
    {
        $targetDate = ($date ?? now())->toDateString();

        return $query
            ->active()
            ->where(function (Builder $innerQuery) use ($targetDate): void {
                $innerQuery->whereNull('start_date')->orWhereDate('start_date', '<=', $targetDate);
            })
            ->where(function (Builder $innerQuery) use ($targetDate): void {
                $innerQuery->whereNull('end_date')->orWhereDate('end_date', '>=', $targetDate);
            });
    }
}
