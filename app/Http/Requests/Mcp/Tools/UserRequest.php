<?php

namespace App\Http\Requests\Mcp\Tools;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\Validator;

/**
 * 사용자 MCP API 요청
 */
class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'idx' => ['nullable', 'integer', 'min:1'],
            'name' => ['nullable', 'string', 'max:30'],
            'nick_name' => ['nullable', 'string', 'max:30'],
            'birth_year' => ['nullable', 'integer', 'digits:4', 'min:1900', 'max:' . now()->year],
            'sex' => ['nullable', 'string', 'in:M,W'],
            'marketing_info_agree' => ['nullable', 'integer', 'in:1,0'],
            'level' => ['nullable', 'string', 'in:normal,admin'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'page' => $this->page ?? 1,
            'per_page' => $this->per_page ?? 20,
        ]);
    }

    protected function failedValidation(Validator $validator): void
    {
        Log::info('Mcp User index validation failed', [
            'action' => 'validate',
            'model' => 'Post',
            'ip' => $this->ip(),
            'user_idx' => $this->user()?->idx,
            'errors' => $validator->errors()->toArray(),
        ]);

        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => '유효성 검사 실패',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    public function messages(): array
    {
        return [
            'page.integer' => __('validation.mcp.integer'),
            'per_page.integer' => __('validation.mcp.integer'),

            'page.min' => __('validation.mcp.min'),
            'per_page.min' => __('validation.mcp.min'),
            'per_page.max' => __('validation.mcp.max_string'),
        ];
    }

    public function attributes(): array
    {
        return __('validation.mcp.attributes');
    }

    public function withValidator(Validator $validator)
    {
    }
}
