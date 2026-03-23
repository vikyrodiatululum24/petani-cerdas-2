<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_published' => $this->boolean('is_published'),
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'is_published' => ['sometimes', 'boolean'],
            'category_id' => ['required', 'exists:article_categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul artikel wajib diisi.',
            'content.required' => 'Konten artikel wajib diisi.',
            'featured_image.image' => 'Gambar unggulan harus berupa file gambar.',
            'featured_image.mimes' => 'Gambar unggulan harus berformat jpg, jpeg, png, atau webp.',
            'featured_image.max' => 'Gambar unggulan tidak boleh lebih besar dari 4MB.',
            'category_id.required' => 'Kategori artikel wajib diisi.',
            'category_id.exists' => 'Kategori artikel yang dipilih tidak valid.',
        ];
    }
}
