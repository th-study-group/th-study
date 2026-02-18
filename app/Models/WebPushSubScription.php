<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 웹 푸시 토큰 구독 정보
 */
class WebPushSubScription extends Model
{
    protected $table = 'web_push_subscriptions';
    protected $primaryKey = 'idx';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    // 주석 처리한 항목은 대량할당 제외이지만 관리 목적상 기록
    protected $fillable = [
        'user_idx',
        'endpoint',
        'p256dh',
        'auth',
        //'last_seen_datetime'
        //'user_agent'
        //'create_datetime',
    ];

    protected $casts = [
        'last_seen_datetime' =>'datetime',
  ];
}
