<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;

class ArticleIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'exists:article_categories,id'],
            'status' => ['nullable', 'in:published,draft'],
            'per_page' => ['nullable', 'integer', 'in:10,15,25,50'],
        ];
    }
}
