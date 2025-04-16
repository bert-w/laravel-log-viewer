<?php

namespace BertW\LaravelLogViewer;

use Closure;
use Illuminate\Http\Request;

trait AuthorizesAccess
{
    /**
     * The callback that should be used to authenticate log viewer users.
     */
    public static ?Closure $authUsing = null;

    /**
     * Determine if the given request can access the log viewer.
     */
    public static function check(Request $request): bool
    {
        return (static::$authUsing ?? static function () {
            return app()->environment('local');
        })($request);
    }

    /**
     * Set the callback that should be used to authenticate log viewer users.
     */
    public static function auth(?Closure $callback): void
    {
        static::$authUsing = $callback;
    }
}