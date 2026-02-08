<?php

namespace App\Http\Requests\Users;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email:rfc',
                'max:80',
                'unique:users,email',
                'regex:/^[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}$/i',
            ],
            'password' => [
                'required',
                'min:8',
                'max:25',
                'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/',
            ],
            'password_confirm' => ['required', 'same:password'],
            'name' => [
                'required', 
                'min:2', 
                'max:15', 
                'regex:/^[A-Za-z가-힣\\s-]+$/u'
            ],
            'nick_name' => [
                'required', 
                'min:2', 
                'string',
                'max:10',
                'unique:users,nick_name',
            ],
            'birth_date' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'sex' => ['required', 'in:M,W'],
            'phone' => [
                'required',
                'regex:/^(010\\d{8}|01[16789]\\d{7})$/',
            ],
            'address' => ['nullable', 'string', 'max:30'],
            'personal_info_agree' => ['required', 'string', 'in:Y'],
            'marketing_info_agree' => ['nullable', 'string', 'in:Y,N'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        Log::info('User register validation failed', [
            'action' => 'validate',
            'model' => 'User',
            'email' => $this->input('email'),
            'ip' => $this->ip(),
            'errors' => $validator->errors()->toArray(),
        ]);

        throw new HttpResponseException(
            to_route('register.form')->withErrors($validator)->withInput()
        );
    }
}
