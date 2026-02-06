<?php

namespace App\Http\Requests\Admins;

use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class MemberSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search_name' => ['nullable', 'string', 'max:30'],
            'search_nickname' => ['nullable', 'string', 'max:30'],
            'search_gender' => ['nullable', 'in:' . implode(',', array_keys(config('const.sex')))],
            'search_marketing' => ['nullable', 'in:0,1'],
            'search_grade' => ['nullable', 'in:' . implode(',', array_keys(config('member.levels')))],
            'search_status' => ['nullable', 'in:email_pending,password_reset'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    protected function failedValidation(ValidationValidator $validator): void
    {
        Log::info('[Admin][Member][Search] validation failed', [
            'ip' => $this->ip(),
            'user_idx' => $this->user()?->idx,
            'errors' => $validator->errors()->toArray(),
        ]);

        throw new HttpResponseException(
                to_route('admins.members.index')
                ->withErrors($validator)
                ->withInput()
        );
    }
}
