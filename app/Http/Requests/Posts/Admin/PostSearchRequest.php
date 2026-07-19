<?php

namespace App\Http\Requests\Posts\Admin;

use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Validator;

class PostSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search_start_date' => ['nullable', 'date'],
            'search_end_date' => ['nullable', 'date'],
            'search_name' => ['nullable', 'string', 'max:50'],
            'search_title' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'page' => $this->page ?? 1,
            'per_page' => $this->per_page ?? 10,
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $start = $this->input('search_start_date');
            $end = $this->input('search_end_date');

            if (!$start && $end) {
                $validator->errors()->add('search_start_date', '시작일을 입력해 주세요.');
            }

            if ($start && $end && $start > $end) {
                $validator->errors()->add('search_end_date', '종료일은 시작일 이후여야 합니다.');
            }
        });
    }

    protected function failedValidation(ValidationValidator $validator): void
    {
        Log::info('[Admin][Post][Search] validation failed', [
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
