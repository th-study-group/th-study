<?php

namespace App\Http\Requests\Notes;

use App\Models\Note;

class UpdateNoteRequest extends StoreNoteRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        $group = (string) $this->route('group', '');
        $slug = (string) $this->route('slug', '');
        $idx = (int) $this->route('idx', 0);
        $resolvedGroupCode = (string) config("note.group.{$group}", $group);

        $note = Note::query()
            ->where('idx', $idx)
            ->where('group_code', $resolvedGroupCode)
            ->where('categories_code', $slug)
            ->first();

        if (! $note) {
            return false;
        }

        return $user->can('update', $note);
    }

    /**
     * 수정 검증은 등록과 동일 규칙 사용
     */
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['usg_flag'] = [
            'required',
            'in:Y,N',
        ];

        return $rules;
    }
}
