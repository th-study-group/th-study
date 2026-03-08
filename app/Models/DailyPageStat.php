<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 일별 페이지 통계
 */
class DailyPageStat extends Model
{
    protected $table = 'daily_page_stats';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'stat_date',
        'access_page',
        'device_type',
        'total_access_count',
        'real_access_count',
        'create_datetime',
        'update_datetime',
    ];

    protected $casts = [
        'stat_date' => 'date',
        'total_access_count' => 'integer',
        'real_access_count' => 'integer',
        'create_datetime' => 'datetime',
        'update_datetime' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->create_datetime = now();
        });

        static::updating(function ($model) {
            $model->update_datetime = now();
        });
    }
}
