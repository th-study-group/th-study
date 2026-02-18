<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Web 푸시 발송 이력 
 */
class WebPushMessage extends Model
{
    protected $table = 'web_push_messages';
    protected $primaryKey = 'idx';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        //'user_idx',
        'title',
        'body',
        //'endpoint',
        //'click_token',
        //'target_url',
        //'success_flag',
        //'send_error_message',
        //'table_name',
        //'user_agent',
        //'send_datetime',
    ];

    protected $casts = [
        'send_datetime' => 'datetime',
        'click_datetime' => 'datetime',
        'success_flag' => 'integer',
        'send_error_message' => 'array',
    ];
}
