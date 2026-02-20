<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use SoftDeletes;

    protected $primaryKey = 'idx';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    const CREATED_AT = 'create_datetime';
    const UPDATED_AT = 'update_datetime';
    const DELETED_AT = 'delete_datetime';

    protected $table = 'users';

    // 주석 할당한건 대량할당 제외이지만 히스토리 유지를 위해 기록
    protected $fillable = [
        'email',
        'name',
        'password',
        'nick_name',
        'birth_date',
        'sex',
        'phone',
        'address',
        'personal_info_agree',
        'marketing_info_agree',
        'push_notification_agree',
        //'memo',
        //'level',
        //'ip',  
        'last_access_datetime',
        //'email_verify_token',
        //'email_verified_datetime',
        //'email_verify_exp_at',
        //'change_password_flag',
        'create_datetime',
        'create_user_idx',
        'update_datetime',
        'update_user_idx',
        'delete_datetime',
        'delete_user_idx',
    ];

    protected $casts = [
        'password' => 'hashed', // 라라벨 10 이상에서 지원하는 해시드 캐스트 
        'create_datetime' => 'datetime'
    ];

    protected $hidden = [
        'password',
    ];

    public function posts() : HasMany
    {
        return $this->hasMany(Post::class, 'user_idx', 'idx');
    }

    public function comments() : HasMany
    {
        return $this->hasMany(Comment::class, 'user_idx', 'idx');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->create_datetime = now();
        });

        static::updating(function ($model) {
            $model->update_datetime = now();
        });
    }

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

    public function setPushNotificationAgreeAttribute($value) 
    {
        $this->attributes['push_notification_agree'] = $value === 'Y' ? 1 : 0;
    }

    public function getPushNotificationAgreeAttribute($value) 
    {
        return (int) $value === 1 ? 'Y' : 'N';
    }

    public function getLevelLabelAttribute()
    {
        return config('member.levels.' . $this->level, 'NONE');
    }

    public function getPhoneFormattedAttribute(): string
    {
        $phone = preg_replace('/\D+/', '', (string) $this->phone);

        if ($phone === '') {
            return '-';
        }

        $length = strlen($phone);

        if ($length === 11) {
            return substr($phone, 0, 3) . '-' . substr($phone, 3, 4) . '-' . substr($phone, 7);
        }

        if ($length === 10) {
            if (str_starts_with($phone, '02')) {
                return substr($phone, 0, 2) . '-' . substr($phone, 2, 4) . '-' . substr($phone, 6);
            }

            return substr($phone, 0, 3) . '-' . substr($phone, 3, 3) . '-' . substr($phone, 6);
        }

        if ($length === 9 && str_starts_with($phone, '02')) {
            return substr($phone, 0, 2) . '-' . substr($phone, 2, 3) . '-' . substr($phone, 5);
        }

        if ($length === 8) {
            return substr($phone, 0, 4) . '-' . substr($phone, 4);
        }

        return $this->phone ?? '-';
    }
}
