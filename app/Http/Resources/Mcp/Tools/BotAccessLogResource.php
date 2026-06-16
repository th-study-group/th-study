<?php

namespace App\Http\Resources\Mcp\Tools;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * 봇 유입 로그 MCP API 리소스
 */
class BotAccessLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [ 
            'access_datetime' => $this->access_datetime?->format('Y-m-d H:i:s'),
            'group_name' => $this->note?->group?->name ?? '-',
            'categories_name' => $this->note?->category?->name ?? '-',
            'topic_name' => $this->note?->topic?->name ?? '-',
            'access_page' => $this->access_page ?? '-',
            'referer_host' => $this->referer_host ?? '-',
            'bot_name' => $this->bot_name ?? '-',
            'referer_url' => $this->referer_url ?? '-',
            'user_agent' => $this->user_agent ?? '-'
        ];
    }
}
