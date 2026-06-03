<?php

namespace App\Http\Resources\Mcp\Tools;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * 노트 해시태그 MCP API 리소스
 */
class NoteTagResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'tag_name' => $this->name,
            'create_datetime' => $this->create_datetime?->format('Y-m-d H:i:s'),
            'create_user_idx' => $this->create_user_idx,
            'note' => $this->notes->map(function ($note) {
                
                $content = str_replace(
                    ['<br>', '<br/>', '<br />', '</p>'],
                    "\n",
                    $note->content
                );

                $content = strip_tags($content);
                $content = preg_replace('/\n+/', "\n", $content);
                $content = preg_replace('/[ \t]+/', ' ', $content);
                $content = trim($content);

                return [
                    'group_name' => $note->group?->name,
                    'categories_name' => $note->category?->name,
                    'topic_name' => $note->topic?->name,
                    'subject' => $note->subject,
                    'content' => $content,
                ];
            }),
        ]; 
    }
}
