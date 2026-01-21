<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\BaseModel;

class Comment extends Base
{
    protected $fillable = [
        //'user_idx',
        //'post_idx',
        'content',
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

    public function post() : BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_idx', 'idx');
    }
}
