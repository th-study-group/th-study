<?php

namespace App\Http\Requests\Comments;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class StoreCommentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'post_idx' => ['required', 'integer', 'exists:posts,idx'],
            'content' => ['required', 'string', 'min:2', 'max:2000'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        Log::info('[Comment][Store] validation failed', [
            'ip' => $this->ip(),
            'user_idx' => $this->user()?->idx,
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
