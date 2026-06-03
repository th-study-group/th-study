<?php

namespace App\Http\Resources\Mcp\Tools;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * 사용자 MCP API 리소스
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'idx' => $this->idx,
            'name' => $this->name,
            'nick_name' => $this->nick_name,
            'sex' => $this->sex_label,
            'email' => $this->email,
            'address' => $this->address,
            'phone' => $this->phone,
            'ip' => $this->ip,
            'last_access_datetime' => $this->last_access_datetime?->format('Y-m-d H:i:s'),
            'personal_info_agree' => $this->personal_info_agree_label,
            'marketing_info_agree' => $this->marketing_info_agree_label,
            'push_notification_agree' => $this->push_notification_agree_label,
            'level_label' => $this->level_label,
            'memo' => $this->memo,
            'create_datetime' => $this->create_datetime?->format('Y-m-d H:i:s'),
            'create_user_idx' => $this->create_user_idx,
        ];
    }
}
