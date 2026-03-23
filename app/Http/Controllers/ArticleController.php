<?php

namespace App\Http\Controllers;

use App\Http\Requests\Article\ArticleIndexRequest;
use App\Http\Requests\Article\StoreArticleRequest;
use App\Http\Requests\Article\UpdateArticleRequest;
use App\Http\Resources\Article\ArticleCategoryResource;
use App\Http\Resources\Article\ArticleResource;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Services\ImageCompressionService;
use App\Services\SlugService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ArticleController extends Controller
{
    public function __construct(
        private ImageCompressionService $imageCompressionService,
        private SlugService $slugService
    )
    {
    }

    public function index(ArticleIndexRequest $request): Response
    {
        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 10);

        $articles = Article::query()
            ->with(['category:id,name,slug', 'author:id,name'])
            ->search($filters['search'] ?? null)
            ->filter($filters)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('articles/Index', [
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
            'categories' => ArticleCategoryResource::collection(
                ArticleCategory::query()->orderBy('name')->get()
            )->resolve(),
            'filters' => [
                'search' => $filters['search'] ?? '',
                'category_id' => isset($filters['category_id']) ? (int) $filters['category_id'] : null,
                'status' => $filters['status'] ?? null,
                'per_page' => $perPage,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('articles/Create', [
            'categories' => ArticleCategoryResource::collection(
                ArticleCategory::query()->orderBy('name')->get()
            )->resolve(),
        ]);
    }

    public function store(StoreArticleRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['slug'] = $this->slugService->generateUnique(Article::class, $data['title']);
        $data['author_id'] = $request->user()->id;

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $this->imageCompressionService->compressAndStore(
                $request->file('featured_image'),
                'articles'
            );
        }

        Article::create($data);

        return redirect()->route('articles.index')->with('success', 'Artikel berhasil dibuat.');
    }

    public function show(Article $article): Response
    {
        return Inertia::render('articles/Show', [
            'article' => ArticleResource::make(
                $article->load(['category:id,name,slug', 'author:id,name'])
            )->resolve(),
        ]);
    }

    public function edit(Article $article): Response
    {
        return Inertia::render('articles/Edit', [
            'article' => ArticleResource::make($article)->resolve(),
            'categories' => ArticleCategoryResource::collection(
                ArticleCategory::query()->orderBy('name')->get()
            )->resolve(),
        ]);
    }

    public function update(UpdateArticleRequest $request, Article $article): RedirectResponse
    {
        $data = $request->validated();
        $removeFeaturedImage = (bool) ($data['remove_featured_image'] ?? false);
        unset($data['remove_featured_image']);

        $data['slug'] = $this->slugService->generateUnique(
            Article::class,
            $data['title'],
            'slug',
            $article->id
        );

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $this->imageCompressionService->compressAndStore(
                $request->file('featured_image'),
                'articles',
                $article->featured_image
            );
        } elseif ($removeFeaturedImage) {
            $this->imageCompressionService->deleteIfExists($article->featured_image);
            $data['featured_image'] = null;
        }

        $article->update($data);

        return redirect()->route('articles.index')->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $this->imageCompressionService->deleteIfExists($article->featured_image);
        $article->delete();

        return redirect()->route('articles.index')->with('success', 'Artikel berhasil dihapus.');
    }
}
