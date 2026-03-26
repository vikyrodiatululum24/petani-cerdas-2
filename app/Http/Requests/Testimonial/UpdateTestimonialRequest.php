<?php

namespace App\Http\Requests\Testimonial;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTestimonialRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:120'],
            'role' => ['nullable', 'string', 'max:150'],
            'content' => ['required', 'string', 'min:20', 'max:1000'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'avatar_color' => ['nullable', 'string', 'max:32'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama pemberi testimoni wajib diisi.',
            'name.max' => 'Nama pemberi testimoni maksimal 120 karakter.',
            'role.max' => 'Peran atau jabatan maksimal 150 karakter.',
            'content.required' => 'Isi testimoni wajib diisi.',
            'content.min' => 'Isi testimoni minimal 20 karakter.',
            'content.max' => 'Isi testimoni maksimal 1000 karakter.',
            'rating.required' => 'Rating wajib diisi.',
            'rating.between' => 'Rating harus antara 1 sampai 5 bintang.',
            'sort_order.min' => 'Urutan tampil tidak boleh kurang dari 0.',
        ];
    }
}
