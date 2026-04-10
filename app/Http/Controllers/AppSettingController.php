<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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
        'tiktok_shop_url' => null,
        'payment_destination_accounts' => '[{"key":"bca_merchant","bank_name":"BCA","account_number":"1234567890","account_holder":"PT Petani Cerdas Indonesia"},{"key":"bri_merchant","bank_name":"BRI","account_number":"987601234567890","account_holder":"PT Petani Cerdas Indonesia"},{"key":"mandiri_merchant","bank_name":"Mandiri","account_number":"1320019988776","account_holder":"PT Petani Cerdas Indonesia"}]',
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
                'tiktok_shop_url' => $setting['tiktok_shop_url'] ?? null,
                'payment_destination_accounts' => $this->serializePaymentAccountsForForm($setting['payment_destination_accounts'] ?? null),
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
            'tiktok_shop_url' => 'nullable|url|max:255',
            'payment_destination_accounts' => 'nullable|string|max:5000',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $request->file('logo')?->store('settings/logo', 'public');
        }

        unset($validated['logo']);

        foreach (['app_description', 'contact_email', 'contact_phone', 'contact_address', 'instagram_url', 'youtube_url', 'facebook_url', 'tiktok_url', 'tiktok_shop_url'] as $key) {
            if (array_key_exists($key, $validated) && $validated[$key] === '') {
                $validated[$key] = null;
            }
        }

        $validated['payment_destination_accounts'] = json_encode(
            $this->parsePaymentAccountsFromForm((string) ($validated['payment_destination_accounts'] ?? '')),
            JSON_UNESCAPED_UNICODE
        );

        AppSetting::setSettings($validated);

        return redirect()->route('setting-app.edit')->with('success', 'Setting aplikasi berhasil diperbarui.');
    }

    /**
     * @return array<int, array{key: string, bank_name: string, account_number: string, account_holder: string}>
     */
    private function parsePaymentAccountsFromForm(string $rawValue): array
    {
        $lines = preg_split('/\r\n|\r|\n/', trim($rawValue)) ?: [];

        $accounts = [];
        foreach ($lines as $index => $line) {
            $trimmed = trim($line);
            if ($trimmed === '') {
                continue;
            }

            $parts = array_map('trim', explode('|', $trimmed));
            if (count($parts) !== 3 || $parts[0] === '' || $parts[1] === '' || $parts[2] === '') {
                throw ValidationException::withMessages([
                    'payment_destination_accounts' => sprintf(
                        'Format rekening tujuan tidak valid pada baris %d. Gunakan format: Nama Bank|Nomor Rekening|Nama Pemilik.',
                        $index + 1
                    ),
                ]);
            }

            $key = strtolower(preg_replace('/[^a-z0-9]+/', '_', $parts[0]) ?? 'rekening');
            $key = trim($key, '_').'_'.($index + 1);

            $accounts[] = [
                'key' => $key,
                'bank_name' => $parts[0],
                'account_number' => $parts[1],
                'account_holder' => $parts[2],
            ];
        }

        if ($accounts === []) {
            $defaultRaw = self::DEFAULT_SETTINGS['payment_destination_accounts'];
            $decodedDefault = json_decode($defaultRaw, true);

            return is_array($decodedDefault) ? $decodedDefault : [];
        }

        return $accounts;
    }

    private function serializePaymentAccountsForForm(mixed $rawValue): string
    {
        $decoded = is_string($rawValue) ? json_decode($rawValue, true) : null;
        if (! is_array($decoded)) {
            $decoded = json_decode(self::DEFAULT_SETTINGS['payment_destination_accounts'], true);
        }

        if (! is_array($decoded)) {
            return '';
        }

        return collect($decoded)
            ->filter(fn ($account) => is_array($account))
            ->map(fn ($account) => sprintf(
                '%s|%s|%s',
                (string) ($account['bank_name'] ?? ''),
                (string) ($account['account_number'] ?? ''),
                (string) ($account['account_holder'] ?? '')
            ))
            ->filter(fn (string $line) => trim($line) !== '||')
            ->implode(PHP_EOL);
    }
}
