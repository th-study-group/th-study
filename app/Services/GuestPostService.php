<?php

namespace App\Services;

use App\Http\Requests\Posts\GuestPostRequest;
use App\Models\GuestPost;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 게시글 서비스 
 */
class GuestPostService
{
    public function create(GuestPostRequest $request): GuestPost
    {
        $payload = $request->validated();
        $ip = $request->ip();
        $userAgent = (string) $request->userAgent();

        $contactValue = $payload['contact_method'] === 'phone'
            ? $payload['phone']
            : $payload['email'];

        $writer = $payload['name'];
        $title = "{$writer} 님이 메인페이지에서 작성한 문의입니다.";

        Log::info('Guest post create start', [
            'action' => 'create',
            'model' => 'GuestPost',
            'contact_method' => $payload['contact_method'] ?? null,
            'ip' => $ip,
        ]);

        try {
            $guestPost = DB::transaction(function () use ($payload, $ip, $userAgent, $contactValue, $writer, $title) {
                $guestPost = GuestPost::create([
                    'title' => $title,
                    'content' => $payload['inquiry_memo'],
                    'personal_info_agree' => $payload['personal_info_agree'],
                    'marketing_info_agree' => $payload['marketing_info_agree'] ?? 'N',
                    'contact_method' => $payload['contact_method'],
                    'contact_value' => $contactValue,
                    'writer' => $writer,
                    'user_agent' => $userAgent,
                    'ip' => $ip,
                ]);

                $guestPost->forceFill([
                    'post_type' => 'inquiries',
                ])->saveQuietly();

                return $guestPost;
            });

            Log::info('Guest post create success', [
                'action' => 'create',
                'model' => 'GuestPost',
                'guest_post_idx' => $guestPost->idx,
                'ip' => $ip,
            ]);

            return $guestPost;
        } catch (\Throwable $e) {
            Log::error('Guest post create failed', [
                'action' => 'create',
                'model' => 'GuestPost',
                'ip' => $ip,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
