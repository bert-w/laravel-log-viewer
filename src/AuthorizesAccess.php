<?php

namespace BertW\LaravelLogViewer;

use Closure;

trait AuthorizesAccess
{
    /**
     * The callback that should be used to authenticate log viewer users.
     *
     * @var \Closure
     */
    public static $authUsing;

    /**
     * Determine if the given request can access the log viewer.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public static function check($request)
    {
        return (static::$authUsing ?: function () {
            return app()->environment('local');
        })($request);
    }

    /**
     * Set the callback that should be used to authenticate log viewer users.
     *
     * @param \Closure $callback
     * @return void
     */
    public static function auth(Closure $callback)
    {
        static::$authUsing = $callback;
    }
}