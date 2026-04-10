<?php

namespace App\Models;

use App\Models\Concerns\DeletesPublicFiles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use DeletesPublicFiles, HasFactory;

    private const PUBLIC_FILE_COLUMNS = [
        'featured_image',
    ];

    protected $fillable = [
        'title',
        'slug',
        'content',
        'featured_image',
        'is_published',
        'author_id',
        'category_id',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'category_id');
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (! $search) {
            return $query;
        }

        return $query->where(function (Builder $innerQuery) use ($search): void {
            $innerQuery
                ->where('title', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%")
                ->orWhere('content', 'like', "%{$search}%");
        });
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when(
                ! empty($filters['category_id']),
                fn (Builder $builder) => $builder->where('category_id', $filters['category_id'])
            )
            ->when(
                ! empty($filters['status']) && $filters['status'] === 'published',
                fn (Builder $builder) => $builder->where('is_published', true)
            )
            ->when(
                ! empty($filters['status']) && $filters['status'] === 'draft',
                fn (Builder $builder) => $builder->where('is_published', false)
            );
    }
}
