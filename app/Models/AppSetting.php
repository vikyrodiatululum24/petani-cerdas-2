<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_name',
        'brand_name',
        'app_description',
        'logo_path',
        'contact_email',
        'contact_phone',
        'contact_address',
        'instagram_url',
        'youtube_url',
        'facebook_url',
    ];
}
