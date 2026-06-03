<?php

namespace App\Http\Resources\Mcp\Tools;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * 노트 MCP API 리소스
 */
class NoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $content = str_replace(
            ['<br>', '<br/>', '<br />', '</p>'],
            "\n",
            $this->content
        );

        $content = strip_tags($content);
        $content = preg_replace('/\n+/', "\n", $content);
        $content = preg_replace('/[ \t]+/', ' ', $content);
        $content = trim($content);

        return [
            'group_name' => $this->group?->name,
            'categories_name' => $this->category?->name,
            'topic_name' => $this->topic?->name,
            'subject' => $this->subject,
            'content' => $content,
            'thumbnail_url' => $this->thumbnail_path
                ? rtrim(config('app.url'), '/') . '/storage/' . ltrim($this->thumbnail_path, '/')
                : null,
            'create_datetime' => $this->create_datetime?->format('Y-m-d H:i:s'),
            'create_user_idx' => $this->create_user_idx,
        ];
    }
}
