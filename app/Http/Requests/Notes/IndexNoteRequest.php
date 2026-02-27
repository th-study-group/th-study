<?php

namespace App\Http\Requests\Notes;

use Illuminate\Foundation\Http\FormRequest;

class IndexNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search_select_type' => ['nullable', 'in:title,content'],
            'search_keyword' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}

