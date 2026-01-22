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
            $routeName = $route->getName();
            $slug = $route->parameter('slug');

            if ($routeName && $slug !== null) {
                $notes = config('note', []);
                $allowed = array_keys($notes[$routeName] ?? []);

                if (empty($allowed) || !in_array($slug, $allowed, true)) {
                    abort(404);
                }
            }
        }

        return $next($request);
    }
}
