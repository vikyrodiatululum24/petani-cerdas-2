<?php

namespace App\Http\Controllers;

use App\Http\Resources\Product\ProductCategoryResource;
use App\Http\Resources\Product\ProductResource;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicProductController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'exists:product_categories,slug'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:24'],
        ]);

        $perPage = (int) ($filters['per_page'] ?? 12);

        $products = Product::query()
            ->with(['category:id,name,slug'])
            ->search($filters['search'] ?? null)
            ->when(
                ! empty($filters['category']),
                fn ($query) => $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $filters['category']))
            )
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $categories = ProductCategory::query()
            ->withCount('products')
            ->orderBy('name')
            ->get();

        return Inertia::render('public/products/Index', [
            'products' => ProductResource::collection($products->getCollection())->resolve(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
                'links' => $products->linkCollection()->toArray(),
            ],
            'categories' => ProductCategoryResource::collection($categories)->resolve(),
            'category_counts' => $categories->mapWithKeys(
                fn (ProductCategory $category) => [$category->slug => (int) $category->products_count]
            ),
            'filters' => [
                'search' => $filters['search'] ?? '',
                'category' => $filters['category'] ?? null,
                'per_page' => $perPage,
            ],
        ]);
    }

    public function show(Product $product): Response
    {
        $product->load(['category:id,name,slug']);

        $relatedProducts = Product::query()
            ->with(['category:id,name,slug'])
            ->where('id', '!=', $product->id)
            ->when(
                $product->category_id,
                fn ($query) => $query->where('category_id', $product->category_id)
            )
            ->latest()
            ->take(4)
            ->get();

        return Inertia::render('public/products/Show', [
            'product' => ProductResource::make($product)->resolve(),
            'related_products' => ProductResource::collection($relatedProducts)->resolve(),
        ]);
    }
}
