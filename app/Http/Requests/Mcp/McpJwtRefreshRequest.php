<?php

namespace App\Http\Requests\Mcp;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

/**
 * JWT 리프레쉬 토큰 요청
 */
class McpJwtRefreshRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'refresh_token' => ['required', 'string'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        Log::info('MCP OAuth refresh validation failed', [
            'action' => 'validate',
            'grant_type' => $this->input('grant_type'),
            'client_id' => $this->input('client_id'),
            'refresh_token_exists' => $this->filled('refresh_token'),
            'ip' => $this->ip(),
            'errors' => $validator->errors()->toArray(),
        ]);

        throw new HttpResponseException(
            response()->json([
                'error' => 'invalid_request',
                'errors' => $validator->errors(),
            ], 422, [
                'Cache-Control' => 'no-store',
                'Pragma' => 'no-cache',
            ])
        );
    }
}
