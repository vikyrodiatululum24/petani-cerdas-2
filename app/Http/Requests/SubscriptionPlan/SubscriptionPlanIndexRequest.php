<?php

namespace App\Http\Requests\SubscriptionPlan;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionPlanIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'featured' => ['nullable', 'in:featured,regular'],
            'per_page' => ['nullable', 'integer', 'in:10,15,25,50'],
        ];
    }
}
