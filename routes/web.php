<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AdminSubscriptionController;
use App\Http\Controllers\AppSettingController;
use App\Http\Controllers\ArticleCategoryController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LiveSessionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PetaniProfileController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PublicArticleController;
use App\Http\Controllers\PublicProductController;
use App\Http\Controllers\SponsorController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('products', [PublicProductController::class, 'index'])->name('public.products.index');
Route::get('products/{product:slug}', [PublicProductController::class, 'show'])->name('public.products.show');

Route::get('NotAuthorized', function () {
    return Inertia::render('NotAuth');
})->name('not-authorized');

Route::get('articles', [PublicArticleController::class, 'index'])->name('public.articles.index');
Route::get('articles/{article:slug}', [PublicArticleController::class, 'show'])->name('public.articles.show');

Route::get('about', [AboutController::class, 'index'])->name('about');

Route::get('layanan', function () {
    $sponsors = \App\Models\Sponsor::latest()->get();

    return Inertia::render('public/services/Index', [
        'sponsors' => \App\Http\Resources\Sponsor\SponsorResource::collection($sponsors)->resolve(),
    ]);
})->name('public.services.index');

Route::get('pendaftaran', function () {
    return Inertia::render('public/register/Index');
})->name('public.register.index');

Route::middleware(['auth', 'verified', 'role:admin|mentor'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('admin/articles', ArticleController::class)->names('articles');
    Route::resource('admin/products', ProductController::class)->names('products');
    Route::post('article-categories', [ArticleCategoryController::class, 'store'])->name('article-categories.store');
    Route::put('article-categories/{articleCategory}', [ArticleCategoryController::class, 'update'])->name('article-categories.update');
    Route::delete('article-categories/{articleCategory}', [ArticleCategoryController::class, 'destroy'])->name('article-categories.destroy');

    Route::get('admin/chat', [ChatController::class, 'index'])->name('admin.chat.index');
    Route::post('admin/chat/{thread}/assign-mentor', [ChatController::class, 'assignMentor'])->name('admin.chat.assign-mentor');
    Route::patch('admin/chat/{thread}/status', [ChatController::class, 'updateStatus'])->name('admin.chat.update-status');

    Route::prefix('admin/live-sessions')->name('admin.live-sessions.')->group(function () {
        Route::get('/', [LiveSessionController::class, 'adminIndex'])->name('index');
        Route::post('/', [LiveSessionController::class, 'adminStore'])->name('store');
        Route::put('{liveSession}', [LiveSessionController::class, 'adminUpdate'])->name('update');
        Route::delete('{liveSession}', [LiveSessionController::class, 'adminDestroy'])->name('destroy');
    });
});

Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::resource('sponsors', SponsorController::class);
    Route::resource('admin/testimonials', TestimonialController::class)
        ->except(['show'])
        ->names('testimonials');
    Route::post('admin/testimonials/{testimonial}/publish', [TestimonialController::class, 'publish'])->name('testimonials.publish');
    Route::post('admin/testimonials/{testimonial}/unpublish', [TestimonialController::class, 'unpublish'])->name('testimonials.unpublish');
    Route::resource('subscription-plans', SubscriptionPlanController::class);
    Route::resource('subscriptions', AdminSubscriptionController::class)->only(['index', 'show', 'update']);
    Route::resource('users', UserController::class);
    Route::get('setting-app', [AppSettingController::class, 'edit'])->name('setting-app.edit');
    Route::put('setting-app', [AppSettingController::class, 'update'])->name('setting-app.update');

    Route::post('product-categories', [ProductCategoryController::class, 'store'])->name('product-categories.store');
    Route::put('product-categories/{productCategory}', [ProductCategoryController::class, 'update'])->name('product-categories.update');
    Route::delete('product-categories/{productCategory}', [ProductCategoryController::class, 'destroy'])->name('product-categories.destroy');

    // Admin subscription payment review (approval workflow)
    Route::prefix('admin/subscriptions')->name('admin.subscriptions.')->group(function () {
        Route::get('payments', [PaymentController::class, 'adminIndex'])->name('payments.index');
        Route::get('payments/{payment}', [PaymentController::class, 'adminShow'])->name('payments.show');
        Route::post('payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
        Route::post('payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
    });
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware('role:petani')->group(function () {
        Route::get('petani/profile', [PetaniProfileController::class, 'index'])->name('petani.profile.index');
        Route::get('petani/profile/edit', [PetaniProfileController::class, 'edit'])->name('petani.profile.edit');
        Route::put('petani/profile', [PetaniProfileController::class, 'update'])->name('petani.profile.update');
    });

    Route::prefix('petani/subscription')->name('petani.subscriptions.')->group(function () {
        Route::get('packages', [SubscriptionController::class, 'packageIndex'])->name('packages.index');
        Route::post('packages/{subscriptionPlan}/checkout', [SubscriptionController::class, 'checkout'])->name('checkout');

        Route::get('payments/{subscription}', [PaymentController::class, 'show'])->name('payments.show');
        Route::post('payments/{subscription}', [PaymentController::class, 'store'])->name('payments.store');
    });

    Route::middleware('subscription.package:basic,premium,elite')->group(function () {
        Route::get('chat', [ChatController::class, 'index'])->name('public.chat.index');
        Route::post('chat/start-topic', [ChatController::class, 'startTopic'])->name('public.chat.start-topic');
    });

    Route::middleware('subscription.package:premium,elite')->group(function () {
        Route::get('live-sessions', [LiveSessionController::class, 'index'])->name('public.live-sessions.index');
    });

    Route::post('chat/{thread}/message', [ChatController::class, 'sendMessage'])->name('public.chat.send-message');

    Route::prefix('petani')->name('petani.')->group(function () {
        Route::middleware('subscription.package:basic,premium,elite')->group(function () {
            Route::get('paket/basic', function () {
                return Inertia::render('Welcome');
            })->name('basic');
        });

        Route::middleware('subscription.package:premium,elite')->group(function () {
            Route::get('paket/premium', function () {
                return Inertia::render('Welcome');
            })->name('premium');
        });

        Route::middleware('subscription.package:elite')->group(function () {
            Route::get('paket/elite', function () {
                return Inertia::render('Welcome');
            })->name('elite');
        });
    });
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
