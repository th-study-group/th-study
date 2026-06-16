<?php

namespace App\Http\Requests\Mcp\Tools;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\Validator;

/**
 * 봇 유입 로그 MCP API 요청 검증 클래스 
 */
class BotAccessLogRequest extends FormRequest
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
            'group_code' => [
                'nullable', 
                'string', 
                'max:20', 
                'required_with:categories_code,topic_code'
            ],
            'categories_code' => [
                'nullable', 
                'string', 
                'max:20',
                'required_with:topic_code'
            ],
            'topic_code' => [
                'nullable',
                'string', 
                'max:30'
            ],
            'bot_name' => ['nullable', 'string', 'max:30'],
            'access_date' => ['nullable', 'date'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'has_note' => ['nullable', 'boolean'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
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
        Log::info('Mcp BotAccessLogs index validation failed', [
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
            'group_code.required_with' => __('validation.mcp.group_code_required_with'),
            'categories_code.required_with' => __('validation.mcp.categories_code_required_with'),
            
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
}
