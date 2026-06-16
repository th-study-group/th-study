<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 노트 기본 정보
 */
class Note extends Base
{
    protected $table = 'notes';

    protected $fillable = [
        //'group_idx',
        //'categories_idx',
        //'topic_idx',
        'group_code',
        'categories_code',
        'subject',
        'content',
        'thumbnail_path',
        'use_flag',
        'access_page',
        //'create_user_idx',
        //'update_user_idx',
        //'delete_user_idx',
        //'create_datetime',
        //'update_datetime',
        //'delete_datetime',
    ];

    protected $casts = [
        'create_datetime' => 'datetime',
        'update_datetime' => 'datetime',
        'delete_datetime' => 'datetime',
    ];

    public function setUseFlagAttribute(string $value): void
    {
        $this->attributes['use_flag'] = $value === 'Y' ? 1 : 0;
    }

    public function getUseFlagAttribute(mixed $value): string
    {
        return (string) $value === '1' ? 'Y' : 'N';
    }

    public function getGroupTopicNameAttribute(): string
    {
        $groupName = $this->group?->name ?? $this->group_code ?? '-';
        $categoryName = $this->category?->name ?? $this->categories_code ?? '-';
        $topicName = $this->topic?->name ?? '-';

        return "{$groupName} > {$categoryName} > {$topicName}";
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(NoteGroup::class, 'group_idx', 'idx');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(NoteCategory::class, 'categories_idx', 'idx');
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(NoteTopic::class, 'topic_idx', 'idx');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(NoteHistory::class, 'note_idx', 'idx');
    }

    public function tagMaps(): HasMany
    {
        return $this->hasMany(NoteTagMap::class, 'note_idx', 'idx');
    }

    public function accessLogs() : HasMany
    {
        return $this->hasMany(AccessLog::class, 'access_page', 'access_page');
    }

    public function botAccessLogs() : HasMany
    {
        return $this->hasMany(BotAccessLog::class, 'access_page', 'access_page');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            NoteTag::class,
            'note_tag_map',
            'note_idx',
            'tag_idx',
            'idx',
            'idx'
        );
    }

    public function createUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'create_user_idx', 'idx');
    }

    public function updateUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'update_user_idx', 'idx');
    }

    public function deleteUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delete_user_idx', 'idx');
    }
}
