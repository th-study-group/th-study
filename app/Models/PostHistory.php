<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 게시글 이력관리
 */
class PostHistory extends Model
{
    protected $table = 'post_histories';
    protected $primaryKey = 'idx';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    // 주석 처리한 항목은 대량할당 제외이지만 관리 목적상 기록
    protected $fillable = [
        'post_idx',
        'ip',
        'user_agent',
        //'job_type',
        //'table_name',
        //'status',
        //'post_type',
        'create_datetime',
        'create_user_idx',
    ];

    protected $casts = [
        'create_datetime' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->create_datetime = now();
        });
    }
}
