<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        $newLogoPathProvided = array_key_exists('logo_path', $settings);
        $oldLogoPath = null;

        if ($newLogoPathProvided) {
            $oldLogoPath = static::query()->where('key', 'logo_path')->value('value');
        }

        foreach ($settings as $key => $value) {
            static::query()->updateOrCreate(
                ['key' => (string) $key],
                ['value' => $value],
            );
        }

        if ($newLogoPathProvided) {
            $newLogoPath = $settings['logo_path'];

            if (is_string($oldLogoPath) && $oldLogoPath !== '' && $oldLogoPath !== $newLogoPath) {
                Storage::disk('public')->delete($oldLogoPath);
            }
        }
    }
}
