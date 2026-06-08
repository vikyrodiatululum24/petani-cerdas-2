<?php

namespace App\Http\Requests\SubscriptionPlan;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $features = $this->input('features', []);

        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
            'features' => is_array($features)
                ? collect($features)->map(fn ($item) => is_string($item) ? trim($item) : $item)->filter()->values()->all()
                : [],
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'jenis' => ['required', 'in:basic,premium,elite'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0', 'lte:price'],
            'quota' => ['nullable', 'integer', 'min:0'],
            'features' => ['nullable', 'array'],
            'features.*' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration_months' => ['required', 'integer', 'min:1'],
            'is_featured' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama paket wajib diisi.',
            'name.max' => 'Nama paket maksimal 255 karakter.',
            'jenis.required' => 'Jenis paket wajib diisi.',
            'jenis.in' => 'Jenis paket harus salah satu dari: basic, premium, elite.',
            'price.required' => 'Harga paket wajib diisi.',
            'price.numeric' => 'Harga paket harus berupa angka.',
            'price.min' => 'Harga paket tidak boleh kurang dari 0.',
            'discount_price.numeric' => 'Harga diskon harus berupa angka.',
            'discount_price.min' => 'Harga diskon tidak boleh kurang dari 0.',
            'discount_price.lte' => 'Harga diskon tidak boleh lebih besar dari harga normal.',
            'quota.integer' => 'Kuota harus berupa angka bulat.',
            'quota.min' => 'Kuota tidak boleh kurang dari 0.',
            'features.array' => 'Fitur harus berupa daftar.',
            'features.*.max' => 'Setiap item fitur maksimal 255 karakter.',
            'description.string' => 'Deskripsi harus berupa teks.',
            'duration_months.required' => 'Durasi wajib diisi.',
            'duration_months.integer' => 'Durasi harus berupa angka bulat.',
            'duration_months.min' => 'Durasi minimal 1 bulan.',
        ];
    }
}
