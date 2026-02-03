<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\BaseModel;

class Post extends Base
{
    protected $casts = [
        'create_datetime' => 'datetime',
    ];

    protected $fillable = [
        //'user_idx',
        'title',
        'content',
        //'status',
        //'post_type',
        //'create_datetime',
        //'create_user_idx',
        //'update_datetime',
        //'update_user_idx',
        //'delete_datetime',
        //'delete_user_idx',
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_idx', 'idx');
    }

    public function comments() : HasMany
    {
        return $this->hasMany(Comment::class, 'post_idx', 'idx');
    }
}
