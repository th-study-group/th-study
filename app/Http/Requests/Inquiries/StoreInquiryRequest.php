<?php

namespace App\Http\Requests\Inquiries;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class StoreInquiryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'min:5',
                'max:50',
            ],
            'content' => ['required', 'string', 'min:5'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        Log::info('Inquiry validation failed', [
            'action' => 'validate',
            'model' => 'Post',
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
