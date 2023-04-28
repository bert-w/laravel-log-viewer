<?php

namespace BertW\LaravelLogViewer\Http\Middleware;

use BertW\LaravelLogViewer\LogViewer;

class Authenticate
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return \Illuminate\Http\Response|null
     */
    public function handle($request, $next)
    {
        return LogViewer::check($request) ? $next($request) : abort(403);
    }
}
