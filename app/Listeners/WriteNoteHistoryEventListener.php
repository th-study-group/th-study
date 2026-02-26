<?php

namespace App\Listeners;

use App\Events\NoteHistoryEvent;
use App\Models\NoteHistory;
use Illuminate\Support\Facades\Log;

/**
 * 노트 이력관리
 */
class WriteNoteHistoryEventListener
{
    public function handle(NoteHistoryEvent $event): void
    {
        $refererUrl = request()->headers->get('referer');
        $refererUrl = is_string($refererUrl) && $refererUrl !== ''
            ? mb_substr($refererUrl, 0, 2048)
            : null;

        $history = new NoteHistory();
        $history->forceFill([
            'note_idx' => $event->noteIdx,
            'job_type' => $event->jobType,
            'ip' => $event->ip,
            'user_agent' => $event->userAgent,
            'referer_url' => $refererUrl,
            'create_user_idx' => $event->createUserIdx,
        ]);
        $history->save();

        Log::info('Note history logged', [
            'action' => 'history',
            'model' => 'NoteHistory',
            'note_idx' => $event->noteIdx,
            'job_type' => $event->jobType,
            'create_user_idx' => $event->createUserIdx,
            'ip' => $event->ip,
        ]);
    }
}
