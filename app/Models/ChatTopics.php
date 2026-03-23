<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ChatTopics extends Model
{
    use HasFactory;

    protected $table = 'chat_topics';

    protected $fillable = [
        'chat_id',
        'consultation_topic_id',
    ];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    public function consultationTopic(): BelongsTo
    {
        return $this->belongsTo(ConsutationTopics::class, 'consultation_topic_id');
    }
}
