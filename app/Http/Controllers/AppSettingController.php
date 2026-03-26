<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AppSettingController extends Controller
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

    public function edit(): Response
    {
        $setting = AppSetting::getSettings(self::DEFAULT_SETTINGS);

        return Inertia::render('setting-app/Edit', [
            'setting' => [
                'app_name' => $setting['app_name'],
                'brand_name' => $setting['brand_name'],
                'app_description' => $setting['app_description'],
                'logo_url' => $setting['logo_path'] ? asset('storage/'.$setting['logo_path']) : null,
                'contact_email' => $setting['contact_email'],
                'contact_phone' => $setting['contact_phone'],
                'contact_address' => $setting['contact_address'],
                'instagram_url' => $setting['instagram_url'],
                'youtube_url' => $setting['youtube_url'],
                'facebook_url' => $setting['facebook_url'],
                'tiktok_url' => $setting['tiktok_url'],
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:100',
            'brand_name' => 'required|string|max:100',
            'app_description' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'contact_email' => 'nullable|email|max:100',
            'contact_phone' => 'nullable|string|max:50',
            'contact_address' => 'nullable|string|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            'facebook_url' => 'nullable|url|max:255',
            'tiktok_url' => 'nullable|url|max:255',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $request->file('logo')?->store('settings/logo', 'public');
        }

        unset($validated['logo']);

        foreach (['app_description', 'contact_email', 'contact_phone', 'contact_address', 'instagram_url', 'youtube_url', 'facebook_url', 'tiktok_url'] as $key) {
            if (array_key_exists($key, $validated) && $validated[$key] === '') {
                $validated[$key] = null;
            }
        }

        AppSetting::setSettings($validated);

        return redirect()->route('setting-app.edit')->with('success', 'Setting aplikasi berhasil diperbarui.');
    }
}
