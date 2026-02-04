<?php

namespace App\Http\Requests\Comments;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCommentRequest extends FormRequest
{
    protected $errorBag = 'commentUpdate';

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'min:2', 'max:2000'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            redirect()
                ->back()
                ->withErrors($validator, $this->errorBag)
                ->withInput()
                ->with('open_comment_edit_idx', $this->route('idx'))
        );
    }
}
