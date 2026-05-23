<?php

namespace App\Http\Requests\Mcp;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

/**
 * JWT 로그인 유효성 검사 및 권한 검증을 담당하는 FormRequest 클래스
 */
class McpJwtLoginRequest extends FormRequest
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
            'email' => [
                'required',
                'email:rfc',
                'max:80',
                'regex:/^[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}$/i',
            ],
            'password' => [
                'required',
                'min:8',
                'max:25',
                'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/',
            ],
        ];
    }

    protected function failedAuthorization(): void
    {
        Log::channel('mcp')->warning('MCP direct JWT login authorization failed', [
            'action' => 'authorize',
            'email' => $this->input('email'),
            'ip' => $this->ip(),
        ]);

        throw new AuthorizationException;
    }

    protected function failedValidation(Validator $validator): void
    {
        Log::channel('mcp')->warning('MCP direct JWT login validation failed', [
            'action' => 'validate',
            'email' => $this->input('email'),
            'ip' => $this->ip(),
            'errors' => $validator->errors()->toArray(),
        ]);

        parent::failedValidation($validator);
    }
}
