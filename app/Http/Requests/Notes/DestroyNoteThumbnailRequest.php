<?php

namespace App\Http\Requests\Notes;

use App\Models\Note;
use Illuminate\Foundation\Http\FormRequest;

class DestroyNoteThumbnailRequest extends FormRequest
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

    public function rules(): array
    {
        return [];
    }
}
