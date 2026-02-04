<?php

namespace App\Http\Requests\Inquiries\Admin;

use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Validator;

class InquirySearchRequest extends FormRequest
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
            'search_status' => ['nullable', 'in:' . implode(',', array_keys(config('board.status')))],
            'search_name' => ['nullable', 'string', 'max:50'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $start = $this->input('search_start_date');
            $end = $this->input('search_end_date');

            if ($start && $end && $start > $end) {
                $validator->errors()->add('search_end_date', '종료일은 시작일 이후여야 합니다.');
            }
        });
    }

    protected function failedValidation(ValidationValidator $validator): void
    {
        Log::info('[Admin][Inquiry][Search] validation failed', [
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
