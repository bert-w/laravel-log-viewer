<?php

namespace BertW\LaravelLogViewer;

use Illuminate\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Psr\Log\LogLevel;

class LogViewer
{
    use AuthorizesAccess;

    protected Application $app;

    protected string $storagePath;

    protected Filesystem $fs;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->fs = $this->app['files'];
        $this->storagePath = $this->config('storage_path');
    }

    /**
     * Get a route for the logviewer application, automatically prefixed.
     */
    public function route(): string
    {
        $args = func_get_args();

        return app('url')->route($this->config('route_name_prefix', 'logviewer.') . array_shift($args), ...$args);
    }

    /**
     * Get a config variable for the log viewer.
     */
    public function config(string $config, mixed $default = null): mixed
    {
        return $this->app['config']->get('logviewer.' . $config, $default);
    }

    public function storagePath(): string
    {
        return $this->storagePath;
    }

    public function fileSystem(): Filesystem
    {
        return $this->fs;
    }

    /**
     * @param string $pattern
     * @param array<'date'|'logLevels', string> $options
     * @return string
     */
    public function pattern(string $pattern, array $options = []): string
    {
        $options = [
            'date' => '\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}(?:[\+-]\d{4})?',
            'logLevels' => '',
            ...$options,
        ];

        return [
            // Separate log file into log entries.
            'logs' => '/\[' . $options['date'] . '\].*(?:\R(?!\[' . $options['date'] . '\]).*)*/',

            // Capture 1: date, 2: context, 3: loglevel 4: message, 5: file.
            'heading' => '/^\[(' . $options['date'] . ')\](?:.*?(\w+)\.|.*?)(' . $options['logLevels'] . ')(?:\:|)(.*?)( in .*?:[0-9]+)?$/i',

            'files' => '/\{.*?\,.*?\}/i',
        ][$pattern];
    }

    /**
     * @return array<string, string>
     */
    public function logLevels(): array
    {
        return [
            LogLevel::EMERGENCY => 'danger',
            LogLevel::ALERT => 'danger',
            LogLevel::CRITICAL => 'danger',
            LogLevel::ERROR => 'danger',
            LogLevel::WARNING => 'warning',
            LogLevel::NOTICE => 'info',
            LogLevel::INFO => 'info',
            LogLevel::DEBUG => 'info',
        ];
    }

    public function title(): string
    {
        return $this->config('title');
    }

    /**
     * Try to retrieve a model from the url route binding.
     */
    public function retrieveRouteBinding(string $value): ?Log
    {
        return RouteBinding::parse($value);
    }

    /**
     * Find the preselected log if an ordering is defined in the configuration.
     */
    public function preselected(): ?Log
    {
        [$attribute, $order] = $this->config('preselect');

        if (!$attribute) {
            return null;
        }

        return $this->logs([$attribute, $order])->first();
    }

    /**
     * Get all the logs.
     *
     * @param ?array{0: string, 1: 'asc'|'desc'} $sortBy
     * @return \Illuminate\Support\Collection<string, \BertW\LaravelLogViewer\Log>
     */
    public function logs(?array $sortBy = null): Collection
    {
        if (!$this->fs->exists($path = $this->storagePath)) {
            return collect();
        }

        [$attribute, $order] = $sortBy ?? $this->config('sort_by');

        $dirs = $this->fs->directories($path);

        /** @var \Illuminate\Support\Collection<string, \BertW\LaravelLogViewer\Log> */
        $collect = collect();
        foreach (array_merge($dirs, [$path]) as $dir) {
            foreach ($this->fs->files($dir) as $file) {
                $collect[$realPath = $file->getRealPath()] = new Log([
                    'path' => $file->getPath(),
                    'real_path' => $realPath,
                    'basename' => $file->getBasename(),
                    'accessed_at' => Carbon::createFromTimestamp($file->getATime() ?: 0),
                    'created_at' => Carbon::createFromTimestamp($file->getCTime() ?: 0),
                    'modified_at' => Carbon::createFromTimestamp($file->getMTime() ?: 0),
                    'extension' => $file->getExtension(),
                    'file' => $file,
                    'is_big' => ($big = $this->config('big_file_threshold')) && $file->getSize() > $big,
                ]);
            }
        }
        return $collect->sortBy(fn($i) => $i->$attribute, SORT_REGULAR, strtolower($order) === 'desc');
    }
}
