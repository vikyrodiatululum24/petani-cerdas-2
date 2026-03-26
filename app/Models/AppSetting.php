<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    public static function getSettings(array $defaults = []): array
    {
        $settings = static::query()->pluck('value', 'key')->toArray();

        return array_replace($defaults, $settings);
    }

    public static function setSettings(array $settings): void
    {
        foreach ($settings as $key => $value) {
            static::query()->updateOrCreate(
                ['key' => (string) $key],
                ['value' => $value],
            );
        }
    }
}
