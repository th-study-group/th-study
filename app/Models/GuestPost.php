<?php

namespace App\Models;

class GuestPost extends Base
{
    protected $fillable = [
        //'user_idx',
        'title',
        'content',
        //'status',
        //'post_type',
        'personal_info_agree',
        'marketing_info_agree',
        'contact_method',
        'contact_value',
        'memo',
        'writer',
        'ip',
        //'user_agent',
        //referer_url
        //'update_user_idx',
        //'delete_user_idx',
        //'create_datetime',
        //'update_datetime',
        //'delete_datetime',
    ];

    protected $casts = [
        'create_datetime' => 'datetime',
    ];

    public function setPersonalInfoAgreeAttribute($value)
    {
        $this->attributes['personal_info_agree'] = $value === 'Y' ? 1 : 0;
    }

    public function getPersonalInfoAgreeAttribute($value)
    {
        return (int) $value === 1 ? 'Y' : 'N';
    }

    public function setMarketingInfoAgreeAttribute($value)
    {
        $this->attributes['marketing_info_agree'] = $value === 'Y' ? 1 : 0;
    }

    public function getMarketingInfoAgreeAttribute($value)
    {
        return (int) $value === 1 ? 'Y' : 'N';
    }
}
