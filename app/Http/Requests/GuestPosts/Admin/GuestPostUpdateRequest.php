<?php

namespace App\Http\Requests\GuestPosts\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class GuestPostUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'memo' => ['nullable', 'string', 'min:3'],
            'status' => ['required', 'in:' . implode(',', array_keys(config('board.status')))],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        Log::info('[Admin][GuestPost][Update] validation failed', [
            'ip' => $this->ip(),
            'user_idx' => $this->user()?->idx,
            'post_type' => $this->route('post_type'),
            'post_idx' => $this->route('idx'),
            'errors' => $validator->errors()->toArray(),
        ]);

        throw new HttpResponseException(
            redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
        );
    }
}
