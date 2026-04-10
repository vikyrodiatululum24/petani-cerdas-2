<?php

namespace App\Models;

use App\Models\Concerns\DeletesPublicFiles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ChatAttachment extends Model
{
    use DeletesPublicFiles, HasFactory;

    private const PUBLIC_FILE_COLUMNS = [
        'file_path',
    ];

    protected $fillable = [
        'chat_id',
        'file_path',
    ];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }
}
