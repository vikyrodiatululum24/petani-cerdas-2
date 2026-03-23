<?php

namespace App\Http\Controllers;

use App\Http\Resources\Article\ArticleCategoryResource;
use App\Http\Resources\Article\ArticleResource;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicArticleController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'exists:article_categories,slug'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:24'],
        ]);

        $perPage = (int) ($filters['per_page'] ?? 9);

        $articles = Article::query()
            ->with(['category:id,name,slug', 'author:id,name'])
            ->where('is_published', true)
            ->search($filters['search'] ?? null)
            ->when(
                ! empty($filters['category']),
                fn ($query) => $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $filters['category']))
            )
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $categories = ArticleCategory::query()
            ->withCount([
                'articles as published_articles_count' => fn ($query) => $query->where('is_published', true),
            ])
            ->orderBy('name')
            ->get();

        return Inertia::render('public/articles/Index', [
            'articles' => ArticleResource::collection($articles->getCollection())->resolve(),
            'pagination' => [
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'per_page' => $articles->perPage(),
                'total' => $articles->total(),
                'from' => $articles->firstItem(),
                'to' => $articles->lastItem(),
                'links' => $articles->linkCollection()->toArray(),
            ],
            'categories' => ArticleCategoryResource::collection($categories)->resolve(),
            'category_counts' => $categories->mapWithKeys(
                fn (ArticleCategory $category) => [$category->slug => (int) $category->published_articles_count]
            ),
            'filters' => [
                'search' => $filters['search'] ?? '',
                'category' => $filters['category'] ?? null,
                'per_page' => $perPage,
            ],
        ]);
    }

    public function show(Article $article): Response
    {
        abort_unless($article->is_published, 404);

        $article->load(['category:id,name,slug', 'author:id,name']);

        $relatedArticles = Article::query()
            ->with(['category:id,name,slug', 'author:id,name'])
            ->where('is_published', true)
            ->where('id', '!=', $article->id)
            ->when(
                $article->category_id,
                fn ($query) => $query->where('category_id', $article->category_id)
            )
            ->latest()
            ->take(3)
            ->get();

        return Inertia::render('public/articles/Show', [
            'article' => ArticleResource::make($article)->resolve(),
            'related_articles' => ArticleResource::collection($relatedArticles)->resolve(),
        ]);
    }
}
