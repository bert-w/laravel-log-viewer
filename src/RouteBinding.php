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
     */
    public static function from(Log $log): string
    {
        return Crypt::encrypt($log->real_path);
    }

    /**
     * Get the log from a route binding.
     */
    public static function parse(string $value): ?Log
    {
        try {
            $file = Crypt::decrypt($value);

            return app(LogViewer::class)->logs()[$file] ?? null;
        } catch (DecryptException $e) {
            return null;
        }
    }
}
