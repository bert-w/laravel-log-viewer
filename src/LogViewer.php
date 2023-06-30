<?php

namespace BertW\LaravelLogViewer;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Psr\Log\LogLevel;

class LogViewer
{
    use AuthorizesAccess;

    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * @var string
     */
    protected $storagePath;

    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $fs;

    /**
     * @var array
     */
    protected $fallbackConfig;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->fs = $this->app['files'];
        $this->fallbackConfig = require_once(__DIR__ . '/../config/logviewer.php');
        $this->storagePath = $this->config('storage_path');
    }

    /**
     * Get a route for the logviewer application, automatically prefixed.
     * @return string
     */
    public function route()
    {
        $args = func_get_args();

        return route($this->config('route_name_prefix', 'logviewer.') . $args[0], array_shift($args));
    }

    /**
     * Get a config variable for the log viewer.
     * @param string $config
     * @param mixed $default
     * @return mixed
     */
    public function config($config, $default = null)
    {
        return $this->app['config']->get('logviewer.' . $config, Arr::get($this->fallbackConfig, $config) ?? $default);
    }

    /**
     * @return string
     */
    public function storagePath()
    {
        return $this->storagePath;
    }

    /**
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function fileSystem()
    {
        return $this->fs;
    }

    /**
     * @param string $pattern
     * @param array $options
     * @return string
     */
    public function pattern($pattern, $options = [])
    {
        $options = array_merge([
            'date' => '\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}(?:[\+-]\d{4})?',
            'logLevels' => '',
        ], $options);

        return [
            // Separate log file into log entries.
            'logs' => '/\[' . $options['date'] . '\].*(?:\R(?!\[' . $options['date'] . '\]).*)*/',

            // Capture 1: date, 2: context, 3: loglevel 4: message, 5: file.
            'heading' => '/^\[(' . $options['date'] . ')\](?:.*?(\w+)\.|.*?)(' . $options['logLevels'] . ')(?:\:|)(.*?)( in .*?:[0-9]+)?$/i',

            'files' => '/\{.*?\,.*?\}/i',
        ][$pattern];
    }

    /**
     * @return array
     */
    public function logLevels()
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

    /**
     * @return string
     */
    public function title()
    {
        return $this->config('title');
    }

    /**
     * Get the selected log (if any).
     *
     * @param \Illuminate\Http\Request|null $request
     * @return Log|null
     */
    public function selectedLog(Request $request = null)
    {
        $uri = ($request ?? request())->get('v');
        $v = $uri ? base64_decode($uri) : null;

        if (empty($v) && $this->config('preselect')) {
            return $this->preselected();
        }

        return $this->logs()[$v] ?? null;
    }

    /**
     * Find the preselected log if an ordering is defined in the configuration.
     *
     * @return \BertW\LaravelLogViewer\Log|null
     */
    public function preselected()
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
     * @param array|null $sortBy
     * @return \BertW\LaravelLogViewer\Log[]|\Illuminate\Support\Collection
     */
    public function logs(array $sortBy = null)
    {
        if (!$this->fs->exists($path = $this->storagePath)) {
            return collect();
        }

        [$attribute, $order] = $sortBy ?? $this->config('sort_by');

        $dirs = $this->fs->directories($path);

        $collect = collect();
        foreach (array_merge($dirs, [$path]) as $dir) {
            foreach ($this->fs->files($dir) as $file) {
                $collect[$file->getRealPath()] = new Log([
                    'path' => $file->getPath(),
                    'real_path' => $file->getRealPath(),
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
