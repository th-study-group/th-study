<?php

namespace App\Http\Requests\Traffics;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class ConversionLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url' => ['required', 'string', 'url', 'max:1000'],
            'conversion_type' => [
                'required',
                'string',
                Rule::in(config('traffic.conversion_types', [])),
            ],
            'source_page' => [
                'nullable', 
                'string', 
                'max:255', 
                'regex:/^\//',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $sourcePage = trim((string) $value);

                    if ($sourcePage === '') {
                        return;
                    }

                    if (preg_match('#/(create|edit)(?:\?.*)?$#', $sourcePage)) {
                        $fail('create/edit 페이지 전환은 허용되지 않습니다.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'url.required' => '이동 URL은 필수입니다.',
            'url.url' => '올바른 URL 형식이 아닙니다.',
            'conversion_type.required' => '전환 타입은 필수입니다.',
            'conversion_type.in' => '허용되지 않은 전환 타입입니다.',
            'source_page.regex' => '원본 페이지는 /로 시작해야 합니다.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        Log::warning('ConversionLogRequest validation failed', [
            'path' => $this->path(),
            'method' => $this->method(),
            'ip' => $this->ip(),
            'url' => $this->input('url'),
            'conversion_type' => $this->input('conversion_type'),
            'source_page' => $this->input('source_page'),
            'errors' => $validator->errors()->toArray(),
        ]);

        parent::failedValidation($validator);
    }
}
