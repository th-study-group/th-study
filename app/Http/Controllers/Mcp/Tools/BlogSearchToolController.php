<?php

namespace App\Http\Controllers\Mcp\Tools;

use App\Http\Controllers\Controller;
use App\Services\Mcp\Tools\BlogSearchToolService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * 블로그 테스트
 */
class BlogSearchToolController extends Controller
{
    public function handle(Request $request, BlogSearchToolService $service)
    {
        Log::channel('mcp')->info('MCP blog_search controller entered', [
            'title_exists' => $request->filled('title'),
            'status_exists' => $request->filled('status'),
            'created_at_exists' => $request->filled('created_at'),
            'created_at_from_exists' => $request->filled('created_at_from'),
            'created_at_to_exists' => $request->filled('created_at_to'),
            'user_id' => auth()->id(),
        ]);

        $result = $service->search([
            'title' => $request->input('title'),
            'status' => $request->input('status'),
            'created_at' => $request->input('created_at'),
            'created_at_from' => $request->input('created_at_from'),
            'created_at_to' => $request->input('created_at_to'),
            'limit' => $request->input('limit'),
        ]);

        Log::channel('mcp')->info('MCP blog_search controller completed', [
            'count' => $result['count'] ?? null,
            'split_required' => $result['split_required'] ?? false,
        ]);

        return response()->json($result);
    }
}
