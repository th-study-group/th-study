<?php

namespace App\Http\Resources\Mcp\Tools;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * 일별 유입/전환 통계 MCP API 리소스
 */
class DailyPageStatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'stat_date' => $this->stat_date,
            'subject' => $this->note?->subject ?? '-',
            'group_name' => $this->note?->group?->name ?? '-',
            'categories_name' => $this->note?->category?->name ?? '-',
            'topic_name' => $this->note?->topic?->name ?? '-',
            'device_type' => $this->device_type,
            'total_access_count' => $this->total_access_count,
            'total_real_count' => $this->real_access_count,
            'total_conversion_count' => $this->conversion_count,
        ];
    }
}
