<?php

namespace BertW\LaravelLogViewer;

use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use RuntimeException;
use SplFileObject;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @property string $path
 * @property string $real_path
 * @property string $basename
 * @property string $extension
 * @property bool $is_big
 * @property SplFileInfo $file
 */
class Log extends Model
{
    /**
     * Cache variable for the number of lines for this log file.
     */
    private ?int $linesCount = null;

    /**
     * File handle.
     */
    private SplFileObject $handle;

    /**
     * Get the route parameter for the URL which resolves to a unique log file.
     */
    public function routeParameter(): string
    {
        return RouteBinding::from($this);
    }

    /**
     * Get the file size as a string with a unit suffix.
     */
    public function size(int $precision = 2): string
    {
        $base = log($bytes = $this->bytes() ?: 1, 1024);
        if ($bytes <= 0) {
            return '0 B';
        }
        $suffixes = ['B', 'kB', 'MB', 'GB', 'TB', 'YT'];

        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }

    /**
     * Get the file size in bytes.
     */
    public function bytes(): int|false
    {
        return $this->handle()->getSize();
    }

    /**
     * Get a file handle.
     */
    public function handle(): SplFileObject
    {
        return $this->handle ??= $this->file->openFile('r');
    }

    /**
     * Get a pagination instance for the lines in this log file.
     *
     * @return LengthAwarePaginator<array-key, string>
     */
    public function paginate(?int $linesPerPage = null, ?int $page = null): LengthAwarePaginator
    {
        $linesPerPage ??= app(LogViewer::class)->config('lines_per_page');

        $page ??= AbstractPaginator::resolveCurrentPage();
        return new LengthAwarePaginator(
            $this->readLinesFromEnd($linesPerPage, $page),
            $this->linesCount(),
            $linesPerPage,
            $page,
            [
                'path' => Paginator::resolveCurrentPath()
            ]
        );
    }

    /**
     * Read lines starting from the end of the file.
     *
     * @return array<string>
     */
    protected function readLinesFromEnd(int $lines, int $page = 1): array
    {
        $lineLimit = app(LogViewer::class)->config('max_line_length');

        $file = $this->handle();
        $arr = [];
        $i = 0;
        try {
            $start = max($this->linesCount() - $page * $lines, 0);
            $file->seek($start);
            while ($i < $lines) {
                $line = $file->fgets();
                $arr[] = !is_null($lineLimit) ? mb_substr($line, 0, $lineLimit) : $line;
                $i++;
            }
        } catch (RuntimeException $e) {
            // File is empty or it cannot be read.
        }

        return $arr;
    }

    /**
     * Get the amount of lines for a specific file.
     */
    public function linesCount(): int
    {
        if (is_null($this->linesCount)) {
            ($handle = $this->handle())->seek($handle->getSize());

            $this->linesCount = $handle->key();
        }

        return $this->linesCount;
    }

    /**
     * Get the display name for this log.
     */
    public function displayName(): string
    {
        switch(app(LogViewer::class)->config('log_display_name')) {
            case 'full':
                return $this->real_path;
            case 'short':
            default:
                return $this->basename;
        }
    }
}
