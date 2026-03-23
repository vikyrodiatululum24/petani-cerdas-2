<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'thread_id',
        'status',
        'assigned_mentor_id',
        'message',
        'message_type',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function assignedMentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_mentor_id');
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(self::class, 'thread_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'thread_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ChatAttachment::class);
    }

    public function topics(): HasMany
    {
        return $this->hasMany(ChatTopics::class);
    }

    public function scopeRootThreads(Builder $query): Builder
    {
        return $query->whereNull('thread_id');
    }
}
