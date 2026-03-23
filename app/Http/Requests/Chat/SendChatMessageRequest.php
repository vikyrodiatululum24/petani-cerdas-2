<?php

namespace App\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;

class SendChatMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => ['nullable', 'string', 'max:5000', 'required_without:image'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096', 'required_without:message'],
        ];
    }

    public function messages(): array
    {
        return [
            'message.required_without' => 'Pesan wajib diisi jika tidak mengirim gambar.',
            'message.max' => 'Pesan maksimal 5000 karakter.',
            'image.required_without' => 'Gambar wajib dipilih jika pesan kosong.',
            'image.image' => 'File yang diunggah harus berupa gambar.',
            'image.mimes' => 'Format gambar harus jpg, jpeg, png, atau webp.',
            'image.max' => 'Ukuran gambar maksimal 4MB.',
        ];
    }
}
