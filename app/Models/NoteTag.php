<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 노트 해시태그
 */
class NoteTag extends Base
{
    protected $table = 'note_tags';

    protected $fillable = [
        'name',
        //'create_user_idx',
        //'update_user_idx',
        //'delete_user_idx',
        //'create_datetime',
        //'update_datetime',
        //'delete_datetime',
    ];

    protected $casts = [
        'create_datetime' => 'datetime',
        'update_datetime' => 'datetime',
        'delete_datetime' => 'datetime',
    ];

    public function tagMaps(): HasMany
    {
        return $this->hasMany(NoteTagMap::class, 'tag_idx', 'idx');
    }

    public function notes(): BelongsToMany
    {
        return $this->belongsToMany(
            Note::class,
            'note_tag_map',
            'tag_idx',
            'note_idx',
            'idx',
            'idx'
        );
    }

    public function createUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'create_user_idx', 'idx');
    }

    public function updateUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'update_user_idx', 'idx');
    }

    public function deleteUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delete_user_idx', 'idx');
    }
}
