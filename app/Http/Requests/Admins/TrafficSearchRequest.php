<?php

namespace App\Http\Requests\Admins;

use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class TrafficSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'search_date' => $this->input('search_date') ?: now()->toDateString(),
            'search_order' => $this->input('search_order') ?: 'desc',
        ]);
    }

    public function rules(): array
    {
        $deviceKeys = array_keys(config('const.device_kind', []));

        return [
            'search_date' => ['required', 'date', 'before_or_equal:today'],
            'search_device' => ['nullable', 'in:' . implode(',', $deviceKeys)],
            'search_ip' => ['nullable', 'string', 'max:45'],
            'search_order' => ['required', 'in:asc,desc'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    protected function failedValidation(ValidationValidator $validator): void
    {
        Log::info('[Admin][Traffic][Search] validation failed', [
            'ip' => $this->ip(),
            'user_idx' => $this->user()?->idx,
            'errors' => $validator->errors()->toArray(),
        ]);

        throw new HttpResponseException(
            to_route('admins.traffics.index')
                ->withErrors($validator)
                ->withInput()
        );
    }
}
