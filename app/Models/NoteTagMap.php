<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NoteTagMap extends Model
{
    protected $table = 'note_tag_map';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        //'note_idx',
        //'tag_idx',
    ];

    protected function setKeysForSaveQuery($query)
    {
        return $query
            ->where('note_idx', $this->getAttribute('note_idx'))
            ->where('tag_idx', $this->getAttribute('tag_idx'));
    }

    public function note(): BelongsTo
    {
        return $this->belongsTo(Note::class, 'note_idx', 'idx');
    }

    public function tag(): BelongsTo
    {
        return $this->belongsTo(NoteTag::class, 'tag_idx', 'idx');
    }
}
