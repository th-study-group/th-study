<?php

namespace App\Repositories\Api;

use App\Models\AccessLog;
use App\Models\BotAccessLog;
use App\Models\ConversionLog;
use App\Models\DailyPageStat;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * 유입 전환 로그 API 레포지토리
 */
class TrafficLogRepository
{
    /**
     * 사람 유입 목록 반환
     *
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function paginateAccessLogs(array $data) : LengthAwarePaginator
    {
        $perPage = $data['per_page'] ?? 20;

        $accessLogs = AccessLog::query()
            ->with([
                'note:idx,access_page,group_idx,categories_idx,topic_idx,subject,content,thumbnail_path,use_flag',
                'note.group:idx,code,name',
                'note.category:idx,group_idx,code,name,use_flag',
                'note.topic:idx,categories_idx,name,memo,use_flag'
            ])
            ->select(
                'idx',
                'access_date',
                'access_datetime',
                'access_page',
                'referer_host',
                'device_type',
                'device_model',
                'device_brand',
                'os',
                'browser',
                'ip',
                'referer_url',
                'user_agent',
                'user_idx'
            )
            ->when(!empty($data['user_idx']), function ($query) use ($data) {
                $query->where('user_idx', $data['user_idx']);
            })
            ->when(!empty($data['device_types']), function ($query) use ($data) {
                $query->whereIn('device_type', $data['device_types']);
            })
            ->when(!empty($data['ip']), function ($query) use ($data) {
                $query->where('ip', $data['ip']);
            })
            ->when(!empty($data['referer_host']), function ($query) use ($data) {
                $query->where('referer_host', 'like', "%{$data['referer_host']}%");
            })
            ->when(!empty($data['access_date']), function($query) use ($data) {
                $query->where('access_date', $data['access_date']);
            })
            ->when(!empty($data['start_date']), function($query) use ($data) {
                $query->whereDate('access_date', '>=', $data['start_date']);
            })
            ->when(!empty($data['end_date']), function($query) use ($data) {
                $query->whereDate('access_date', '<=', $data['end_date']);
            })
            ->when(isset($data['has_note']), function ($query) use ($data) {
                if ($data['has_note'] === true) {
                    $query->whereHas('note', function($q) use ($data) {
                        $q->whereNotNull('idx')
                            ->where('use_flag', 1);
                    });
                } else {
                    $query->whereDoesntHave('note');
                }
            })
            ->when(!empty($data['group_code']), function($query) use ($data) {
                $query->whereHas('note.group', function($q) use ($data) {
                    $q->where('code', $data['group_code']);
                });
            })
            ->when(!empty($data['categories_code']), function($query) use ($data) {
                $query->whereHas('note.category', function($q) use ($data) {
                    $q->where('code', $data['categories_code'])
                      ->where('use_flag', 1);
                });
            })
            ->when(!empty($data['topic_code']), function($query) use ($data) {
                $query->whereHas('note.topic', function($q) use ($data) {
                   $q->where('name', $data['topic_code'])
                     ->where('use_flag', 1); 
                });
            })
            ->orderby('access_datetime', 'desc')
            ->paginate($perPage);

        return $accessLogs;
    }

    /**
     * 봇 유입 목록 반환
     *
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function paginateBotAccessLogs(array $data) : LengthAwarePaginator
    {
        $perPage = $data['per_page'] ?? 20;

        $botAccessLogs = BotAccessLog::query()
            ->with([
                'note:idx,access_page,group_idx,categories_idx,topic_idx,subject,content,thumbnail_path,use_flag',
                'note.group:idx,code,name',
                'note.category:idx,group_idx,code,name,use_flag',
                'note.topic:idx,categories_idx,name,memo,use_flag'
            ])
            ->select(
                'idx',
                'access_date',
                'access_datetime',
                'access_page',
                'referer_host',
                'bot_name',
                'referer_url',
                'user_agent'
            )
            ->when(!empty($data['bot_name']), function ($query) use ($data) {
                $query->where('bot_name', $data['bot_name']);
            })
            ->when(!empty($data['referer_host']), function ($query) use ($data) {
                $query->where('referer_host', 'like', "%{$data['referer_host']}%");
            })
            ->when(!empty($data['access_date']), function($query) use ($data) {
                $query->where('access_date', $data['access_date']);
            })
            ->when(!empty($data['start_date']), function($query) use ($data) {
                $query->whereDate('access_date', '>=', $data['start_date']);
            })
            ->when(!empty($data['end_date']), function($query) use ($data) {
                $query->whereDate('access_date', '<=', $data['end_date']);
            })
            ->when(isset($data['has_note']), function ($query) use ($data) {
                if ($data['has_note'] === true) {
                    $query->whereHas('note', function($q) use ($data) {
                        $q->whereNotNull('idx')
                            ->where('use_flag', 1);
                    });
                } else {
                    $query->whereDoesntHave('note');
                }
            })
            ->when(!empty($data['group_code']), function($query) use ($data) {
                $query->whereHas('note.group', function($q) use ($data) {
                    $q->where('code', $data['group_code']);
                });
            })
            ->when(!empty($data['categories_code']), function($query) use ($data) {
                $query->whereHas('note.category', function($q) use ($data) {
                    $q->where('code', $data['categories_code'])
                      ->where('use_flag', 1);
                });
            })
            ->when(!empty($data['topic_code']), function($query) use ($data) {
                $query->whereHas('note.topic', function($q) use ($data) {
                   $q->where('name', $data['topic_code'])
                     ->where('use_flag', 1); 
                });
            })
            ->orderBy('access_datetime', 'desc')
            ->paginate($perPage);

        return $botAccessLogs;
    }

    /**
     * 유입 후 전환 로그 목록 반환
     *
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function paginateConversionLogs(array $data) : LengthAwarePaginator
    {
        $perPage = $data['per_page'] ?? 20;

        $conversionLogs = ConversionLog::query()
            ->with([
                'note:idx,access_page,group_idx,categories_idx,topic_idx,subject,content,thumbnail_path,use_flag',
                'note.group:idx,code,name',
                'note.category:idx,group_idx,code,name,use_flag',
                'note.topic:idx,categories_idx,name,memo,use_flag'
            ])
            ->select(
                'idx',
                'conversion_date',
                'conversion_datetime',
                'conversion_type',
                'access_page',
                'device_type',
                'device_brand',
                'device_model',
                'target_page',
                'referer_host',
                'os',
                'browser',
                'ip',
                'referer_url',
                'user_agent',
                'user_idx'
            )
            ->when(!empty($data['user_idx']), function ($query) use ($data) {
                $query->where('user_idx', $data['user_idx']);
            })
            ->when(!empty($data['device_types']), function ($query) use ($data) {
                $query->whereIn('device_type', $data['device_types']);
            })
            ->when(!empty($data['ip']), function ($query) use ($data) {
                $query->where('ip', $data['ip']);
            })
            ->when(!empty($data['referer_host']), function ($query) use ($data) {
                $query->where('referer_host', 'like', "%{$data['referer_host']}%");
            })
            ->when(!empty($data['conversion_type']), function($query) use ($data) {
                $query->where('conversion_type', $data['conversion_type']);
            })
            ->when(!empty($data['conversion_date']), function($query) use ($data) {
                $query->where('conversion_date', $data['conversion_date']);
            })
            ->when(!empty($data['start_date']), function($query) use ($data) {
                $query->whereDate('conversion_date', '>=', $data['start_date']);
            })
            ->when(!empty($data['end_date']), function($query) use ($data) {
                $query->whereDate('conversion_date', '<=', $data['end_date']);
            })
            ->when(isset($data['has_note']), function ($query) use ($data) {
                if ($data['has_note'] === true) {
                    $query->whereHas('note', function($q) use ($data) {
                        $q->whereNotNull('idx')
                            ->where('use_flag', 1);
                    });
                } else {
                    $query->whereDoesntHave('note');
                }
            })
            ->when(!empty($data['group_code']), function($query) use ($data) {
                $query->whereHas('note.group', function($q) use ($data) {
                    $q->where('code', $data['group_code']);
                });
            })
            ->when(!empty($data['categories_code']), function($query) use ($data) {
                $query->whereHas('note.category', function($q) use ($data) {
                    $q->where('code', $data['categories_code'])
                      ->where('use_flag', 1);
                });
            })
            ->when(!empty($data['topic_code']), function($query) use ($data) {
                $query->whereHas('note.topic', function($q) use ($data) {
                   $q->where('name', $data['topic_code'])
                     ->where('use_flag', 1); 
                });
            })
            ->orderby('conversion_datetime', 'desc')    
            ->paginate($perPage);

        return $conversionLogs;
    }

    /**
     * 일별 유입/전환 통계 로그 목록 반환
     *
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function paginateDailyPageStatLogs(array $data) : LengthAwarePaginator 
    {
        $perPage = $data['per_page'] ?? 20;

        $dailyPageStats = DailyPageStat::query()
            ->with([
                'note:idx,access_page,group_idx,categories_idx,topic_idx,subject,content,thumbnail_path,use_flag',
                'note.group:idx,code,name',
                'note.category:idx,group_idx,code,name,use_flag',
                'note.topic:idx,categories_idx,name,memo,use_flag'
            ])
            ->select(
                'stat_date',
                'access_page',
                'device_type',
                'total_access_count',
                'real_access_count',
                'conversion_count'
            )
            ->when(!empty($data['device_types']), function ($query) use ($data) {
                $query->whereIn('device_type', $data['device_types']);
            })
            ->when(!empty($data['stat_date']), function($query) use ($data) {
                $query->where('stat_date', $data['stat_date']);
            })
            ->when(!empty($data['start_date']), function($query) use ($data) {
                $query->whereDate('stat_date', '>=', $data['start_date']);
            })
            ->when(!empty($data['end_date']), function($query) use ($data) {
                $query->whereDate('stat_date', '<=', $data['end_date']);
            })
            ->when(isset($data['has_note']), function ($query) use ($data) {
                if ($data['has_note'] === true) {
                    $query->whereHas('note', function($q) use ($data) {
                        $q->whereNotNull('idx')
                            ->where('use_flag', 1);
                    });
                } else {
                    $query->whereDoesntHave('note');
                }
            })
            ->when(!empty($data['group_code']), function($query) use ($data) {
                $query->whereHas('note.group', function($q) use ($data) {
                    $q->where('code', $data['group_code']);
                });
            })
            ->when(!empty($data['categories_code']), function($query) use ($data) {
                $query->whereHas('note.category', function($q) use ($data) {
                    $q->where('code', $data['categories_code'])
                    ->where('use_flag', 1);
                });
            })
            ->when(!empty($data['topic_code']), function($query) use ($data) {
                $query->whereHas('note.topic', function($q) use ($data) {
                $q->where('name', $data['topic_code'])
                    ->where('use_flag', 1); 
                });
            })
            ->orderby('create_datetime', 'desc')
            ->paginate($perPage);
            
        return $dailyPageStats;
    }
}