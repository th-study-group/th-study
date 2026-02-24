<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateNoteSlug
{
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();

        if ($route) {
            $slug = $route->parameter('slug');
            $group = $route->parameter('group');

            if ($group && $slug !== null) {
                $notes = config('note', []);
                $allowed = array_keys($notes[$group] ?? []);

                if (empty($allowed) || !in_array($slug, $allowed, true)) {
                    abort(404);
                }
            }
        }

        return $next($request);
    }
}
