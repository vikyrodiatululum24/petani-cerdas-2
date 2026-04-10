<?php

namespace App\Models;

use App\Models\Concerns\DeletesPublicFiles;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use DeletesPublicFiles, HasFactory;

    private const PUBLIC_FILE_COLUMNS = [
        'image_path',
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'discount_price',
        'category_id',
        'image_path',
        'olshop_links',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'olshop_links' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

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

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when(
                ! empty($filters['category_id']),
                fn (Builder $builder) => $builder->where('category_id', $filters['category_id'])
            );
    }
}
