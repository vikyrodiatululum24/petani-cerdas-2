<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $links = (array) $this->input('olshop_links', []);

        $this->merge([
            'remove_image' => $this->boolean('remove_image'),
            'olshop_links' => collect($links)
                ->map(fn ($value) => is_string($value) ? trim($value) : $value)
                ->filter(fn ($value) => filled($value))
                ->all(),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0'],
            'image_path' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_image' => ['sometimes', 'boolean'],
            'olshop_links' => ['nullable', 'array'],
            'olshop_links.*' => ['nullable', 'url', 'max:2048'],
            'category_id' => ['required', 'exists:product_categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama produk wajib diisi.',
            'name.max' => 'Nama produk maksimal 255 karakter.',
            'description.required' => 'Deskripsi produk wajib diisi.',
            'price.required' => 'Harga produk wajib diisi.',
            'price.numeric' => 'Harga produk harus berupa angka.',
            'price.min' => 'Harga produk tidak boleh kurang dari 0.',
            'discount_price.numeric' => 'Harga diskon harus berupa angka.',
            'discount_price.min' => 'Harga diskon tidak boleh kurang dari 0.',
            'image_path.image' => 'Gambar produk harus berupa file gambar.',
            'image_path.mimes' => 'Gambar produk harus berformat jpg, jpeg, png, atau webp.',
            'image_path.max' => 'Gambar produk tidak boleh lebih besar dari 4MB.',
            'olshop_links.array' => 'Link online shop harus berupa daftar URL.',
            'olshop_links.*.url' => 'Setiap link online shop harus berupa URL yang valid.',
            'olshop_links.*.max' => 'Panjang URL online shop maksimal 2048 karakter.',
            'category_id.required' => 'Kategori produk wajib diisi.',
            'category_id.exists' => 'Kategori produk yang dipilih tidak valid.',
        ];
    }
}
