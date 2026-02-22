<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 노트 그룹 코드 
 */
class NoteGroup extends Base
{
    protected $table = 'note_groups';

    protected $fillable = [
        'code',
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

    public function categories(): HasMany
    {
        return $this->hasMany(NoteCategory::class, 'group_idx', 'idx');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'group_idx', 'idx');
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
