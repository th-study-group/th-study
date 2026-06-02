<?php

namespace App\Http\Requests\Mcp\Tools;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\Validator;

/**
 * 노트 태그 MCP API 요청 검증 클래스
 */
class NoteTagRequest extends FormRequest
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
     * @return array<string, array<mixed>|string>
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
            'has_thumbnail' => ['nullable', 'boolean'],
            'tag' => ['required', 'string', 'max:20'],
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
        Log::info('Mcp Blog index validation failed', [
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

            'tag.required' => __('validation.mcp.tag_required'),

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
        $validator->after(function (Validator $validator) {

            $hasSearchCondition = !empty($this->tag);

            if (!$hasSearchCondition) {

                $validator->errors()->add(
                    'search',
                    '태그 검색조건은 필수입니다.'
                );
            }
        });
    }
}
