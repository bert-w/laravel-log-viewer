<?php

namespace BertW\LaravelLogViewer;


use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

/**
 * Abstracted route binding so the routes do not need to include the RouteBinding middleware.
 */
class RouteBinding
{
    /**
     * Get the route binding from a log.
     *
     * @return string
     */
    public static function from(Log $log)
    {
        return Crypt::encrypt($log->real_path);
    }

    /**
     * Get the log from a route binding.
     *
     * @param string $value
     * @return \BertW\LaravelLogViewer\Log|false
     */
    public static function parse($value)
    {
        try {
            $file = Crypt::decrypt($value);

            return app(LogViewer::class)->logs()[$file] ?? false;
        } catch (DecryptException $e) {
            return false;
        }
    }
}