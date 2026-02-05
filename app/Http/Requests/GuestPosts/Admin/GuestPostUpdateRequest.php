<?php

namespace App\Http\Requests\GuestPosts\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GuestPostUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'memo' => ['nullable', 'string'],
            'status' => ['required', 'in:' . implode(',', array_keys(config('board.status')))],
        ];
    }
}
