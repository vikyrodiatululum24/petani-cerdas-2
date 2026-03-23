<?php

namespace App\Http\Requests\Sponsor;

use Illuminate\Foundation\Http\FormRequest;

class StoreSponsorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'logo_path' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama sponsor wajib diisi.',
            'name.max' => 'Nama sponsor maksimal 255 karakter.',
            'description.string' => 'Deskripsi sponsor harus berupa teks.',
            'logo_path.image' => 'Logo sponsor harus berupa file gambar.',
            'logo_path.mimes' => 'Logo sponsor harus berformat jpg, jpeg, png, webp, atau svg.',
            'logo_path.max' => 'Logo sponsor tidak boleh lebih besar dari 4MB.',
        ];
    }
}
