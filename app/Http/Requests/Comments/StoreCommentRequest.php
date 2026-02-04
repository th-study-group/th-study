<?php

namespace App\Http\Requests\Comments;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

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
        throw new HttpResponseException(
            redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
        );
    }
}
