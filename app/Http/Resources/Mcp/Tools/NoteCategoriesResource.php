<?php

namespace App\Http\Resources\Mcp\Tools;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * 노트 카테고리 MCP API 리소스
 */
class NoteCategoriesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'group_code' => $this->group->code,
            'group_name' => $this->group->name,
            'categories_code' => $this->code,
            'categories_name' => $this->name,
            'categories_memo' => $this->memo,
            'create_datetime' => $this->create_datetime?->format('Y-m-d H:i:s'),
            'create_user_idx' => $this->create_user_idx,
        ];
    }
}
