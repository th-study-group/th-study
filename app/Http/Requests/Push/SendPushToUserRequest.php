<?php

namespace App\Http\Requests\Push;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class SendPushToUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'nullable|integer',
            'user_ids' => 'nullable|array|min:1',
            'user_ids.*' => 'integer|distinct',
            'title' => 'required|string',
            'body' => 'required|string',
            'target_url' => ['required', 'string', 'regex:/^(\\/(?!\\/).*|https?:\\/\\/.+)$/i'],
            'table_name' => 'required|string|max:64',
            'url' => 'nullable|string',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $userId = $this->input('user_id');
            $userIds = $this->input('user_ids');

            if (blank($userId) && blank($userIds)) {
                $validator->errors()->add('user_id', 'user_id 또는 user_ids 중 하나는 필수입니다.');
            }
        });
    }

    protected function failedValidation(Validator $validator): void
    {
        Log::info('[Push][SendToUser] validation failed', [
            'user_idx' => $this->user()?->idx,
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
