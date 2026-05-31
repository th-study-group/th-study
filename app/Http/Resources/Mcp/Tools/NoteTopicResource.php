<?php

namespace App\Http\Resources\Mcp\Tools;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * 노트 주제 MCP API 리소스
 */
class NoteTopicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'group_name' => $this->category?->group?->name,
            'categories_name' => $this->category?->name,
            'topic_name' => $this->name,
            'topic_memo' => $this->memo,
            'create_datetime' => $this->create_datetime?->format('Y-m-d H:i:s'),
            'create_user_idx' => $this->create_user_idx,
        ];
    }
}
