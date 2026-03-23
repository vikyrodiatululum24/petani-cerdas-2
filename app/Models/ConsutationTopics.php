<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class ConsutationTopics extends Model
{
    use HasFactory;

    protected $table = 'consutation_topics';

    protected $fillable = [
        'title',
        'description',
        'slug',
        'category_id',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ChatCategory::class, 'category_id');
    }

    public function chatTopics(): HasMany
    {
        return $this->hasMany(ChatTopics::class, 'consultation_topic_id');
    }
}
