<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 메일 발송 수신 로그
 */
class MailLog extends Model
{
    protected $table = 'mail_logs';
    protected $primaryKey = 'idx';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'email',
        'kind',
        'token',
        'send_datetime',
        'sender',
        'receive_datetime',
        'receive_ip',
    ];

    protected $casts = [
      'send_datetime' => 'datetime',
      'receive_datetime' => 'datetime',
    ];
}
