<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NoteCategory extends Base
{
    protected $table = 'note_categories';

    protected $fillable = [
        //'group_idx',
        'code',
        'name',
        'memo',
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

    public function group(): BelongsTo
    {
        return $this->belongsTo(NoteGroup::class, 'group_idx', 'idx');
    }

    public function topics(): HasMany
    {
        return $this->hasMany(NoteTopic::class, 'categories_idx', 'idx');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'categories_idx', 'idx');
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
