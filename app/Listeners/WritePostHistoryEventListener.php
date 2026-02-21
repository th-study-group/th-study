<?php

namespace App\Listeners;

use App\Events\PostHistoryEvent;
use App\Models\PostHistory;
use Illuminate\Support\Facades\Log;

/**
 * 게시글 이력관리 
 */
class WritePostHistoryEventListener
{
    public function handle(PostHistoryEvent $event): void
    {
        $refererUrl = request()->headers->get('referer');
        $refererUrl = is_string($refererUrl) && $refererUrl !== ''
            ? mb_substr($refererUrl, 0, 2048)
            : null;

        $history = new PostHistory();
        $history->forceFill([
            'post_idx' => $event->postIdx,
            'ip' => $event->ip,
            'user_agent' => $event->userAgent,
            'referer_url' => $refererUrl,
            'job_type' => $event->jobType,
            'table_name' => $event->tableName,
            'status' => $event->status,
            'post_type' => $event->postType,
            'create_user_idx' => $event->createUserIdx,
        ]);
        $history->save();

        Log::info('Post history logged', [
            'action' => 'history',
            'model' => 'PostHistory',
            'post_idx' => $event->postIdx,
            'job_type' => $event->jobType,
            'table_name' => $event->tableName,
            'status' => $event->status,
            'post_type' => $event->postType,
            'create_user_idx' => $event->createUserIdx,
            'ip' => $event->ip,
        ]);
    }
}
