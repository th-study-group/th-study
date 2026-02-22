<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Note extends Base
{
    protected $table = 'notes';

    protected $fillable = [
        //'group_idx',
        //'categories_idx',
        'group_code',
        'categories_code',
        'subject',
        'content',
        'thumbnail_path',
        'use_flag',
        //'create_user_idx',
        //'update_user_idx',
        //'delete_user_idx',
        //'create_datetime',
        //'update_datetime',
        //'delete_datetime',
    ];

    protected $casts = [
        'use_flag' => 'integer',
        'create_datetime' => 'datetime',
        'update_datetime' => 'datetime',
        'delete_datetime' => 'datetime',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(NoteGroup::class, 'group_idx', 'idx');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(NoteCategory::class, 'categories_idx', 'idx');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(NoteHistory::class, 'note_idx', 'idx');
    }

    public function tagMaps(): HasMany
    {
        return $this->hasMany(NoteTagMap::class, 'note_idx', 'idx');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            NoteTag::class,
            'note_tag_map',
            'note_idx',
            'tag_idx',
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
