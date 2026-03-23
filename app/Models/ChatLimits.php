<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ChatLimits extends Model
{
    use HasFactory;

    protected $table = 'chat_limits';

    protected $fillable = [
        'user_id',
        'date',
        'limit',
    ];

    protected $casts = [
        'date' => 'date',
        'limit' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
