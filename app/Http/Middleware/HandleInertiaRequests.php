<?php

namespace App\Http\Middleware;

use App\Models\AppSetting;
use App\Models\ChatLimits;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    private const DEFAULT_SETTINGS = [
        'app_name' => 'Petani Cerdas',
        'brand_name' => 'PetaniCerdas',
        'app_description' => 'Platform agritech digital Indonesia.',
        'logo_path' => null,
        'contact_email' => null,
        'contact_phone' => null,
        'contact_address' => null,
        'instagram_url' => null,
        'youtube_url' => null,
        'facebook_url' => null,
        'tiktok_url' => null,
    ];

    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');
        $appSetting = AppSetting::getSettings(self::DEFAULT_SETTINGS);

        $chatQuota = null;
        if ($request->user()?->hasRole('petani')) {
            $used = (int) (ChatLimits::query()
                ->where('user_id', $request->user()->id)
                ->whereDate('date', now()->toDateString())
                ->value('limit') ?? 0);

            $chatQuota = [
                'max' => 5,
                'used' => $used,
                'remaining' => max(0, 5 - $used),
            ];
        }

        return array_merge(parent::share($request), [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user(),
                'roles' => $request->user()
                    ? $request->user()->getRoleNames()->values()->all()
                    : [],
            ],
            'chatQuota' => $chatQuota,
            'appSetting' => [
                'app_name' => $appSetting['app_name'],
                'brand_name' => $appSetting['brand_name'],
                'app_description' => $appSetting['app_description'],
                'logo_url' => $appSetting['logo_path'] ? asset('storage/'.$appSetting['logo_path']) : null,
                'contact_email' => $appSetting['contact_email'],
                'contact_phone' => $appSetting['contact_phone'],
                'contact_address' => $appSetting['contact_address'],
                'instagram_url' => $appSetting['instagram_url'],
                'youtube_url' => $appSetting['youtube_url'],
                'facebook_url' => $appSetting['facebook_url'],
                'tiktok_url' => $appSetting['tiktok_url'],
            ],
        ]);
    }
}
