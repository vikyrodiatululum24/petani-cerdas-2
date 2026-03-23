<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductCategory\StoreProductCategoryRequest;
use App\Http\Requests\ProductCategory\UpdateProductCategoryRequest;
use App\Models\ProductCategory;
use App\Services\SlugService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class ProductCategoryController extends Controller
{
    public function __construct(private SlugService $slugService)
    {
    }

    public function store(StoreProductCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['slug'] = $this->slugService->generateUnique(ProductCategory::class, $data['name']);

        ProductCategory::create($data);

        return back()->with('success', 'Kategori produk berhasil ditambahkan.');
    }

    public function update(UpdateProductCategoryRequest $request, ProductCategory $productCategory): RedirectResponse
    {
        $data = $request->validated();

        $data['slug'] = $this->slugService->generateUnique(
            ProductCategory::class,
            $data['name'],
            'slug',
            $productCategory->id
        );

        $productCategory->update($data);

        return back()->with('success', 'Kategori produk berhasil diperbarui.');
    }

    public function destroy(ProductCategory $productCategory): RedirectResponse
    {
        if ($productCategory->products()->exists()) {
            throw ValidationException::withMessages([
                'category' => 'Kategori produk tidak bisa dihapus karena masih memiliki produk terkait.',
            ]);
        }

        $productCategory->delete();

        return back()->with('success', 'Kategori produk berhasil dihapus.');
    }
}
