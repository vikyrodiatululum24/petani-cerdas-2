<?php

namespace App\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;

class AssignMentorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mentor_id' => ['required', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'mentor_id.required' => 'Mentor wajib dipilih.',
            'mentor_id.exists' => 'Mentor yang dipilih tidak valid.',
        ];
    }
}
