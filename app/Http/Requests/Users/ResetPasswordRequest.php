<?php

namespace App\Http\Requests\Users;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required'],
            'email' => [
                'required',
                'email:rfc',
                'max:80',
                'regex:/^[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}$/i',
            ],
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'max:25',
                'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/',
            ],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        Log::info('Reset password validation failed', [
            'action' => 'validate',
            'model' => 'User',
            'email' => $this->input('email'),
            'ip' => $this->ip(),
            'errors' => $validator->errors()->toArray(),
        ]);

        throw new HttpResponseException(
            to_route('password.reset.mail', [
                'token' => $this->input('token'),
                'email' => $this->input('email'),
            ])->withErrors($validator)->withInput()
        );
    }
}
