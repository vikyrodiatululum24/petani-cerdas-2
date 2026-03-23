<?php

namespace App\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;

class StartTopicChatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:chat_categories,id'],
            'consultation_topic_id' => ['required', 'exists:consutation_topics,id'],
            'initial_message' => ['required', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'consultation_topic_id.required' => 'Topik wajib dipilih.',
            'consultation_topic_id.exists' => 'Topik yang dipilih tidak valid.',
            'initial_message.required' => 'Pesan awal wajib diisi.',
            'initial_message.max' => 'Pesan awal maksimal 5000 karakter.',
        ];
    }
}
