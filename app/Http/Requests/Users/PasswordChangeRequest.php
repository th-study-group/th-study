<?php

namespace App\Http\Requests\Users;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class PasswordChangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'min:8',
                'max:25',
                'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/',
            ],
            'password_confirm' => ['required', 'same:password'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        Log::info('Password change validation failed', [
            'action' => 'validate',
            'model' => 'User',
            'user_id' => auth()->user()?->idx,
            'email' => auth()->user()?->email,
            'ip' => $this->ip(),
            'errors' => $validator->errors()->toArray(),
        ]);

        throw new HttpResponseException(
            to_route('password.change.form')->withErrors($validator)->withInput()
        );
    }
}
