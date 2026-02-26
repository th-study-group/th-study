<?php

namespace App\Http\Requests\Notes;

use App\Models\Note;
use App\Models\NoteTopic;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator as ValidationValidator;

class StoreNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        return $user->can('create', Note::class);
    }

    public function rules(): array
    {
        return [
            'subject' => [
                'required',
                'string',
                'min:5',
                'max:100',
            ],
            'topic' => [
                'required',
                'integer',
                Rule::exists('note_topics', 'idx')
                    ->where('use_flag', 1)
                    ->whereNull('delete_datetime'),
            ],
            'content' => [
                'required',
                'string',
                'min:10',
            ],
            'thumbnail_path' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:51200',
            ],
            'tags' => [
                'nullable',
                'string',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $tags = array_values(array_filter(array_map('trim', explode(',', (string) $value)), function ($tag) {
                        return $tag !== '';
                    }));

                    if (count($tags) > 10) {
                        $fail('해시태그는 최대 10개까지 등록할 수 있습니다.');
                    }

                    foreach ($tags as $tag) {
                        if (mb_strlen($tag) > 20) {
                            $fail('해시태그는 항목당 20자 이하만 입력할 수 있습니다.');
                            break;
                        }
                    }
                },
            ],
        ];
    }

    public function withValidator(ValidationValidator $validator): void
    {
        $validator->after(function (ValidationValidator $validator): void {
            $content = (string) $this->input('content', '');
            $plainText = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($content), ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?? '');

            if (mb_strlen($plainText) < 10) {
                $validator->errors()->add('content', '내용은 10자 이상 입력해 주세요.');
            }

            $topicIdx = $this->input('topic');
            $routeGroup = (string) $this->route('group', '');
            $routeSlug = (string) $this->route('slug', '');
            $resolvedGroupCode = (string) config("note.group.{$routeGroup}", $routeGroup);

            if (empty($topicIdx) || $routeSlug === '' || $resolvedGroupCode === '') {
                return;
            }

            $exists = NoteTopic::query()
                ->where('idx', $topicIdx)
                ->where('use_flag', 1)
                ->whereHas('category', function ($categoryQuery) use ($routeSlug, $resolvedGroupCode) {
                    $categoryQuery->where('code', $routeSlug)
                        ->whereHas('group', function ($groupQuery) use ($resolvedGroupCode) {
                            $groupQuery->where('code', $resolvedGroupCode);
                        });
                })
                ->exists();

            if (! $exists) {
                $validator->errors()->add('topic', '해당 카테고리에서 사용 가능한 주제를 선택해 주세요.');
            }
        });
    }

    protected function failedValidation(Validator $validator): void
    {
        Log::info('Note validation failed', [
            'action' => 'validate',
            'model' => 'Note',
            'ip' => $this->ip(),
            'user_idx' => $this->user()?->idx,
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
