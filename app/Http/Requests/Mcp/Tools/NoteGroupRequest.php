<?php

namespace App\Http\Requests\Mcp\Tools;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\Validator;


/**
 * 노트 그룹 MCP API 요청 검증 클래스
 */
class NoteGroupRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'group_code' => [
                'nullable',
                'string', 
                'max:20'
            ],
            'group_name' => [
                'nullable',
                'string', 
                'max:30'
            ],
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
        Log::info('Mcp NoteGroup index validation failed', [
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
}
