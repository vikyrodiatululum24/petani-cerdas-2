<?php

namespace App\Http\Middleware;

use App\Models\AppSetting;
use App\Models\ChatLimits;
use App\Models\Subscription;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    private const PETANI_BASIC_DAILY_CHAT_LIMIT = 10;

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
        'tiktok_shop_url' => null,
        'payment_destination_accounts' => '[{"key":"bca_merchant","bank_name":"BCA","account_number":"1234567890","account_holder":"PT Petani Cerdas Indonesia"},{"key":"bri_merchant","bank_name":"BRI","account_number":"987601234567890","account_holder":"PT Petani Cerdas Indonesia"},{"key":"mandiri_merchant","bank_name":"Mandiri","account_number":"1320019988776","account_holder":"PT Petani Cerdas Indonesia"}]',
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
            $activeSubscription = Subscription::query()
                ->where('user_id', $request->user()->id)
                ->with('plan:id,name')
                ->currentlyActive()
                ->latest('end_date')
                ->latest('id')
                ->first();

            $activePlanName = str((string) ($activeSubscription?->plan?->name ?? ''))->lower();

            if ($activePlanName->contains('basic')) {
                $used = (int) (ChatLimits::query()
                    ->where('user_id', $request->user()->id)
                    ->whereDate('date', now()->toDateString())
                    ->value('limit') ?? 0);

                $chatQuota = [
                    'max' => self::PETANI_BASIC_DAILY_CHAT_LIMIT,
                    'used' => $used,
                    'remaining' => max(0, self::PETANI_BASIC_DAILY_CHAT_LIMIT - $used),
                ];
            }
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
                'subscriptions' => $request->user()
                    ? [
                        'active' => $request->user()->subscriptions()->currentlyActive()->exists(),
                        'plan_name' => $request->user()->subscriptions()->currentlyActive()->with('plan:id,name')->first()?->plan?->name,
                    ]
                    : null,
            ],
            'chatQuota' => $chatQuota,
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
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
                'tiktok_shop_url' => $appSetting['tiktok_shop_url'],
            ],
        ]);
    }
}
