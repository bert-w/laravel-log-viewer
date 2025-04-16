<?php

namespace BertW\LaravelLogViewer\Http\Middleware;

use BertW\LaravelLogViewer\LogViewer;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Authenticate
{
    /**
     * Handle the incoming request.
     */
    public function handle(Request $request, \Closure $next): mixed
    {
        return LogViewer::check($request) ? $next($request) : throw new HttpException(403);
    }
}
