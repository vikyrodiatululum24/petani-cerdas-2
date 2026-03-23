<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Article;
use App\Models\Product;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $setting = AppSetting::query()->first();

        $articles = Article::query()
            ->with('category:id,name')
            ->where('is_published', true)
            ->latest()
            ->take(3)
            ->get();

        $products = Product::query()
            ->with('category:id,name')
            ->latest()
            ->take(4)
            ->get();

        $plans = SubscriptionPlan::query()
            ->take(3)
            ->get();

        $stats = [
            [
                'value' => number_format(User::query()->count(), 0, ',', '.').'+',
                'label' => 'Petani Aktif',
                'icon' => 'users',
            ],
            [
                'value' => number_format(Article::query()->where('is_published', true)->count(), 0, ',', '.').'+',
                'label' => 'Artikel Pertanian',
                'icon' => 'newspaper',
            ],
            [
                'value' => number_format(User::role('mentor')->count(), 0, ',', '.').'+',
                'label' => 'Mentor Ahli',
                'icon' => 'shield-check',
            ],
            [
                'value' => number_format(SubscriptionPlan::query()->count(), 0, ',', '.').'+',
                'label' => 'Paket Membership',
                'icon' => 'trending-up',
            ],
        ];

        return Inertia::render('Welcome', [
            'canRegister' => true,
            'appSetting' => [
                'app_name' => $setting?->app_name ?? 'Petani Cerdas',
                'brand_name' => $setting?->brand_name ?? 'PetaniCerdas',
                'app_description' => $setting?->app_description ?? 'Platform agritech digital yang menghubungkan petani dengan teknologi modern.',
                'logo_url' => $setting?->logo_path ? asset('storage/'.$setting->logo_path) : null,
            ],
            'stats' => $stats,
            'latestArticles' => $articles->map(function (Article $article): array {
                return [
                    'category' => $article->category?->name ?? 'Artikel',
                    'title' => $article->title,
                    'excerpt' => Str::limit(strip_tags($article->content), 140),
                    'date' => optional($article->created_at)->translatedFormat('d M Y'),
                    'readTime' => max(1, (int) ceil(str_word_count(strip_tags($article->content)) / 180)).' menit',
                    'href' => route('public.articles.show', ['article' => $article->slug]),
                ];
            })->values(),
            'products' => $products->map(function (Product $product): array {
                $price = $product->discount_price ?? $product->price;

                return [
                    'name' => $product->name,
                    'price' => 'Rp '.number_format((float) $price, 0, ',', '.'),
                    'category' => $product->category?->name ?? 'Produk',
                    'rating' => 4.8,
                    'sold' => 'Baru',
                ];
            })->values(),
            'membershipPlans' => $plans->map(function (SubscriptionPlan $plan): array {
                $isFeatured = (bool) $plan->is_featured;
                $finalPrice = $plan->discount_price ?? $plan->price;

                return [
                    'name' => $plan->name,
                    'originalPrice' => $plan->discount_price ? 'Rp '.number_format((float) $plan->price, 0, ',', '.').' / '.$plan->duration_months.' bulan' : null,
                    'price' => 'Rp '.number_format((float) $finalPrice, 0, ',', '.'),
                    'priceNote' => '/ '.$plan->duration_months.' bulan',
                    'quota' => ($plan->quota ?? 0).' orang',
                    'available' => ($plan->quota ?? 0) > 0,
                    'highlight' => $isFeatured,
                    'features' => is_array($plan->features) ? $plan->features : [],
                    'cta' => 'Pilih Paket',
                    'ctaHref' => route('petani.subscriptions.packages.index'),
                ];
            })->values(),
        ]);
    }
}
