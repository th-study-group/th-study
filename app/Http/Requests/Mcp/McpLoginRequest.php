<?php

namespace App\Http\Requests\Mcp;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class McpLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

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
            'client_id' => ['required', 'string'],
            'redirect_uri' => ['required', 'string'],
            'state' => ['nullable', 'string'],
            'code_challenge' => ['nullable', 'string'],
            'code_challenge_method' => ['nullable', 'string'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        Log::info('MCP OAuth login validation failed', [
            'action' => 'validate',
            'email' => $this->input('email'),
            'client_id' => $this->input('client_id'),
            'redirect_uri' => $this->input('redirect_uri'),
            'ip' => $this->ip(),
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
