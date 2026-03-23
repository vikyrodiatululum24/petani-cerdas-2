<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleCategory\StoreArticleCategoryRequest;
use App\Http\Requests\ArticleCategory\UpdateArticleCategoryRequest;
use App\Models\ArticleCategory;
use App\Services\SlugService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class ArticleCategoryController extends Controller
{
    public function __construct(private SlugService $slugService)
    {
    }

    public function store(StoreArticleCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['slug'] = $this->slugService->generateUnique(ArticleCategory::class, $data['name']);

        ArticleCategory::create($data);

        return back()->with('success', 'Kategori artikel berhasil ditambahkan.');
    }

    public function update(UpdateArticleCategoryRequest $request, ArticleCategory $articleCategory): RedirectResponse
    {
        $data = $request->validated();

        $data['slug'] = $this->slugService->generateUnique(
            ArticleCategory::class,
            $data['name'],
            'slug',
            $articleCategory->id
        );

        $articleCategory->update($data);

        return back()->with('success', 'Kategori artikel berhasil diperbarui.');
    }

    public function destroy(ArticleCategory $articleCategory): RedirectResponse
    {
        if ($articleCategory->articles()->exists()) {
            throw ValidationException::withMessages([
                'category' => 'Kategori artikel tidak bisa dihapus karena masih memiliki artikel terkait.',
            ]);
        }

        $articleCategory->delete();

        return back()->with('success', 'Kategori artikel berhasil dihapus.');
    }
}
