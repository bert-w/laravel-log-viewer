<?php

namespace BertW\LaravelLogViewer;

use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use RuntimeException;
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
     *
     * @var int
     */
    private $linesCount;

    /**
     * File handle.
     *
     * @var \SplFileObject
     */
    private $handle;

    /**
     * Get the encoded path.
     *
     * @return string
     */
    public function encodedPath()
    {
        return base64_encode($this->real_path);
    }

    /**
     * Get the file size as a string with a unit suffix.
     *
     * @param int $precision
     * @return string
     */
    public function size($precision = 2)
    {
        $base = log($bytes = $this->bytes(), 1024);
        if ($bytes <= 0) {
            return '0 B';
        }
        $suffixes = ['B', 'kB', 'MB', 'GB', 'TB', 'YT'];

        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }

    /**
     * Get the file size in bytes.
     *
     * @return false|int
     */
    public function bytes()
    {
        return $this->handle()->getSize();
    }

    /**
     * Get a file handle.
     *
     * @return \SplFileObject
     */
    public function handle()
    {
        if (is_null($this->handle)) {
            $this->handle = $this->file->openFile('r');
        }

        return $this->handle;
    }

    /**
     * Get a pagination instance for the lines in this log file.
     *
     * @param int $linesPerPage
     * @param int|null $page
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginate($linesPerPage = null, $page = null)
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
     * @param int $lines
     * @param int $page
     * @return array
     */
    protected function readLinesFromEnd($lines, $page = 1)
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
     *
     * @return int
     */
    public function linesCount()
    {
        if (is_null($this->linesCount)) {
            ($handle = $this->handle())->seek($handle->getSize());

            $this->linesCount = $handle->key();
        }

        return $this->linesCount;
    }

    /**
     * Get a pretty path representation from this log's path.
     *
     * @return string
     */
    public function prettyPath()
    {
        return str_replace(DIRECTORY_SEPARATOR, ' ' . DIRECTORY_SEPARATOR . ' ', $this->path);
    }
}
