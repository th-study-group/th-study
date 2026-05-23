<?php

namespace App\Http\Controllers\Mcp;

use App\Http\Controllers\Controller;
use App\Services\Mcp\ToolRunner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * MCP API 컨트롤러 (챗지피티)
 */
class McpApiController extends Controller
{
    /**
     * MCP API 요청 처리
     *
     * @param Request $request
     * @param ToolRunner $runner
     * @return void
     */
    public function handle(Request $request, ToolRunner $runner)
    {
        $id = $request->input('id');
        $method = $request->input('method');

        if (!$method && $request->isMethod('GET')) {
            $method = 'tools/list';
        }

        Log::channel('mcp')->info('MCP API request entered', [
            'method' => $method,
            'jsonrpc_id' => $id,
            'user_id' => auth()->id(),
        ]);

        switch ($method) {
            case 'initialize':
                return response()->json([
                    'jsonrpc' => '2.0',
                    'id' => $id,
                    'result' => [
                        'protocolVersion' => '2024-11-05',
                        'serverInfo' => [
                            'name' => 'TH-Study MCP',
                            'version' => '1.0.0',
                        ],
                        'capabilities' => [
                            'tools' => new \stdClass(),
                        ],
                    ],
                ]);

            case 'tools/list':
                $toolPath = config('mcp.tool_path');

                if (!$toolPath || !file_exists($toolPath)) {
                    Log::channel('mcp')->warning('MCP tools/list file missing', [
                        'tool_path' => $toolPath,
                    ]);

                    return response()->json([
                        'jsonrpc' => '2.0',
                        'id' => $id,
                        'error' => [
                            'code' => -32000,
                            'message' => 'Tool file not found',
                        ],
                    ]);
                }

                $tools = json_decode(
                    file_get_contents($toolPath),
                    true
                );

                if (!is_array($tools)) {
                    Log::channel('mcp')->warning('MCP tools/list invalid json', [
                        'tool_path' => $toolPath,
                    ]);

                    return response()->json([
                        'jsonrpc' => '2.0',
                        'id' => $id,
                        'error' => [
                            'code' => -32000,
                            'message' => 'Invalid tool file',
                        ],
                    ]);
                }

                Log::channel('mcp')->info('MCP tools/list completed', [
                    'tool_count' => count($tools),
                ]);

                return response()->json([
                    'jsonrpc' => '2.0',
                    'id' => $id,
                    'result' => [
                        'tools' => $tools,
                    ],
                ]);

            case 'tools/call':
                $params = (array) $request->input('params', []);

                $tool = (string) ($params['name'] ?? '');
                $args = (array) ($params['arguments'] ?? []);

                Log::channel('mcp')->info('MCP tools/call entered', [
                    'tool' => $tool,
                    'args_keys' => array_keys($args),
                ]);

                $result = $runner->run($tool, $args);

                Log::channel('mcp')->info('MCP tools/call completed', [
                    'tool' => $tool,
                    'status' => $result['status'] ?? 200,
                    'count' => $result['count'] ?? null,
                ]);

                return response()->json([
                    'jsonrpc' => '2.0',
                    'id' => $id,
                    'result' => [
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => json_encode(
                                    $result,
                                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                                ),
                            ],
                        ],
                    ],
                ]);

            default:
                Log::channel('mcp')->warning('MCP method not found', [
                    'method' => $method,
                ]);

                return response()->json([
                    'jsonrpc' => '2.0',
                    'id' => $id,
                    'error' => [
                        'code' => -32601,
                        'message' => 'Method not found',
                    ],
                ]);
        }
    }
}
