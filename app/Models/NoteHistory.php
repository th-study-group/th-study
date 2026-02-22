<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 노트 이력 관리
 */
class NoteHistory extends Model
{
    protected $table = 'note_histories';
    protected $primaryKey = 'idx';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        //'note_idx',
        'job_type',
        'ip',
        'user_agent',
        'referer_url',
        //'create_user_idx',
        //'create_datetime',
    ];

    protected $casts = [
        'create_datetime' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->create_datetime)) {
                $model->create_datetime = now();
            }
        });
    }

    public function note(): BelongsTo
    {
        return $this->belongsTo(Note::class, 'note_idx', 'idx');
    }

    public function createUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'create_user_idx', 'idx');
    }
}
