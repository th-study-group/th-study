<?php

namespace App\Http\Requests\Posts;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class GuestPostRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'min:2', 'max:15', 'regex:/^[A-Za-z가-힣]+$/u'],
            'contact_method' => ['required', 'in:phone,email'],
            'phone' => [
                'nullable',
                'required_if:contact_method,phone',
                'regex:/^(010\\d{8}|01[16789]\\d{7})$/',
            ],
            'email' => [
                'nullable',
                'required_if:contact_method,email',
                'email:rfc',
                'max:80',
            ],
            'inquiry_memo' => ['required', 'string', 'min:10', 'max:1000'],
            'personal_info_agree' => ['required', 'string', 'in:Y'],
            'marketing_info_agree' => ['nullable', 'string', 'in:Y,N'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        Log::info('Guest post validation failed', [
            'action' => 'validate',
            'model' => 'GuestPost',
            'ip' => $this->ip(),
            'contact_method' => $this->input('contact_method'),
            'errors' => $validator->errors()->toArray(),
        ]);

        throw new HttpResponseException(
            response()->json([
                'result' => false,
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
