<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 노트 주제
 */
class NoteTopic extends Base
{
    protected $table = 'note_topics';

    protected $fillable = [
        //'categories_idx',
        'name',
        'memo',
        //'usg_flag',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(NoteCategory::class, 'categories_idx', 'idx');
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
