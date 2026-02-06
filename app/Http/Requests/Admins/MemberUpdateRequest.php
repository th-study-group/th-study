<?php

namespace App\Http\Requests\Admins;

use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class MemberUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'memo' => ['nullable', 'string', 'min:3'],
        ];
    }

    protected function failedValidation(ValidationValidator $validator): void
    {
        Log::info('[Admin][Member][Update] validation failed', [
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
