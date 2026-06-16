<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BotAccessLog extends Model
{
    protected $table = 'bot_access_logs';
    protected $primaryKey = 'idx';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'access_date',
        'access_datetime',
        'access_page',
        'referer_host',
        'bot_name',
        'referer_url',
        'user_agent',
    ];

    protected $casts = [
        'access_date' => 'date',
        'access_datetime' => 'datetime',
    ];

    public function note(): BelongsTo
    {
        return $this->belongsTo(Note::class, 'access_page', 'access_page');
    }
}
