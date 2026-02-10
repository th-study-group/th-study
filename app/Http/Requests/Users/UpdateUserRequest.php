<?php

namespace App\Http\Requests\Users;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userIdx = $this->user()?->idx;

        return [
            'name' => [
                'required',
                'min:2',
                'max:15',
                'regex:/^[A-Za-z가-힣\\s-]+$/u',
            ],
            'nick_name' => [
                'required',
                'min:2',
                'string',
                'max:10',
                Rule::unique('users', 'nick_name')->ignore($userIdx, 'idx'),
            ],
            'birth_date' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'sex' => ['required', 'in:M,W'],
            'phone' => [
                'required',
                'regex:/^(010\\d{8}|01[16789]\\d{7})$/',
            ],
            'address' => ['nullable', 'string', 'max:80'],
            'personal_info_agree' => ['required', 'string', 'in:Y'],
            'marketing_info_agree' => ['nullable', 'string', 'in:Y,N'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        Log::info('User update validation failed', [
            'action' => 'validate',
            'model' => 'User',
            'user_idx' => $this->user()?->idx,
            'email' => $this->user()?->email,
            'ip' => $this->ip(),
            'errors' => $validator->errors()->toArray(),
        ]);

        throw new HttpResponseException(
            back()->withErrors($validator)->withInput()
        );
    }
}
