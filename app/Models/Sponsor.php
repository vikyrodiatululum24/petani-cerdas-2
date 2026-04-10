<?php

namespace App\Models;

use App\Models\Concerns\DeletesPublicFiles;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    use DeletesPublicFiles, HasFactory;

    private const PUBLIC_FILE_COLUMNS = [
        'logo_path',
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo_path',
    ];

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (! $search) {
            return $query;
        }

        return $query->where(function (Builder $innerQuery) use ($search): void {
            $innerQuery
                ->where('name', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }
}
