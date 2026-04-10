<?php

namespace App\Models;

use App\Models\Concerns\DeletesPublicFiles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use DeletesPublicFiles, HasFactory;

    private const PUBLIC_FILE_COLUMNS = [
        'photo',
    ];

    protected $fillable = [
        'user_id',
        'phone',
        'main_crop',
        'land_type',
        'province',
        'city',
        'district',
        'village',
        'postal_code',
        'address',
        'photo',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
