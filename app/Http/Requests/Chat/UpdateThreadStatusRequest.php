<?php

namespace App\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;

class UpdateThreadStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:new,in_progress,resolved'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status thread wajib dipilih.',
            'status.in' => 'Status thread tidak valid.',
        ];
    }
}
