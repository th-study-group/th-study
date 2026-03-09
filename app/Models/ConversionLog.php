<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 사이트 전환
 */
class ConversionLog extends Model
{
    protected $table = 'conversion_logs';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'conversion_date',
        'conversion_datetime',
        'access_page',
        'conversion_type',
        'target_page',
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
        'conversion_date' => 'date',
        'conversion_datetime' => 'datetime',
        'user_idx' => 'integer',
    ];
}
