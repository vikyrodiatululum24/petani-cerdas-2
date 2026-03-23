<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\ProductIndexRequest;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\Product\ProductCategoryResource;
use App\Http\Resources\Product\ProductResource;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Services\ImageCompressionService;
use App\Services\SlugService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function __construct(
        private ImageCompressionService $imageCompressionService,
        private SlugService $slugService
    )
    {
    }

    public function index(ProductIndexRequest $request): Response
    {
        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 10);

        $products = Product::query()
            ->with(['category:id,name,slug'])
            ->search($filters['search'] ?? null)
            ->filter($filters)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('products/Index', [
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
            'categories' => ProductCategoryResource::collection(
                ProductCategory::query()->orderBy('name')->get()
            )->resolve(),
            'filters' => [
                'search' => $filters['search'] ?? '',
                'category_id' => isset($filters['category_id']) ? (int) $filters['category_id'] : null,
                'per_page' => $perPage,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('products/Create', [
            'categories' => ProductCategoryResource::collection(
                ProductCategory::query()->orderBy('name')->get()
            )->resolve(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['slug'] = $this->slugService->generateUnique(Product::class, $data['name']);

        if ($request->hasFile('image_path')) {
            $data['image_path'] = $this->imageCompressionService->compressAndStore(
                $request->file('image_path'),
                'products'
            );
        }

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil dibuat.');
    }

    public function show(Product $product): Response
    {
        return Inertia::render('products/Show', [
            'product' => ProductResource::make(
                $product->load(['category:id,name,slug'])
            )->resolve(),
        ]);
    }

    public function edit(Product $product): Response
    {
        return Inertia::render('products/Edit', [
            'product' => ProductResource::make($product)->resolve(),
            'categories' => ProductCategoryResource::collection(
                ProductCategory::query()->orderBy('name')->get()
            )->resolve(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        $removeImage = (bool) ($data['remove_image'] ?? false);
        unset($data['remove_image']);

        $data['slug'] = $this->slugService->generateUnique(
            Product::class,
            $data['name'],
            'slug',
            $product->id
        );

        if ($request->hasFile('image_path')) {
            $data['image_path'] = $this->imageCompressionService->compressAndStore(
                $request->file('image_path'),
                'products',
                $product->image_path
            );
        } elseif ($removeImage) {
            $this->imageCompressionService->deleteIfExists($product->image_path);
            $data['image_path'] = null;
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->imageCompressionService->deleteIfExists($product->image_path);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
