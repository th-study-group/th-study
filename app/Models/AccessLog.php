<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 사이트 유입
 */
class AccessLog extends Model
{
    protected $table = 'access_logs';
    protected $primaryKey = 'idx';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'access_date',
        'access_datetime',
        'access_page',
        'referer_host',
        'device_type',
        'device_brand',
        'device_model',
        'os',
        'browser',
        'ip',
        'referer_url',
        'user_agent',
        'session_id',
        'user_idx',
    ];

    protected $casts = [
        'access_date' => 'date',
        'access_datetime' => 'datetime',
        'user_idx' => 'integer',
    ];
}
