<?php

namespace BertW\LaravelLogViewer;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;

/**
 * @property object $heading
 * @property string $raw_heading
 * @property \Illuminate\Support\Collection $lines
 */
class LogRecord extends Model
{

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->buildLines();
        $this->buildHeading();

        // Unset raw content for memory conservation.
        unset($this->attributes['raw']);
    }

    /**
     * Separate the heading from the lines and assign them as properties.
     *
     * @return void
     */
    protected function buildLines()
    {
        $lines = preg_split("/\r\n|\n|\r/", trim($this->raw));
        $heading = array_shift($lines);
        $this->lines = collect($lines);
        $this->raw_heading = $heading ?? '';
    }

    /**
     * Build the heading (first log line) for this record.
     *
     * @return void
     */
    protected function buildHeading()
    {
        $logViewer = app(LogViewer::class);

        $exp = $logViewer->pattern('heading', [
            'logLevels' => join('|', array_keys($logViewer->logLevels())) . '|',
        ]);

        preg_match($exp, $this->raw_heading, $matches);

        $matches = array_map(fn($i) => $i === '' ? null : $i, $matches);

        $this->heading = (object)[
            'created_at' => (function () use ($matches) {
                if ($date = $matches[1] ?? null) {
                    try {
                        return Carbon::parse($date);
                    } catch (InvalidFormatException $e) {
                        return $date;
                    }
                }
                return null;
            })(),
            'environment' => $matches[2] ?? null,
            'log_level' => $matches[3] ?? null,
            'title' => $matches[4] ?? $this->raw_heading ?: '',
        ];
    }

    /**
     * Hydrate a chunk of log lines to log records.
     *
     * @param string $chunk
     * @param bool $reverse
     * @return static[]
     */
    public static function hydrate($chunk, $reverse = true)
    {
        preg_match_all(app(LogViewer::class)->pattern('logs'), $chunk, $matches, PREG_OFFSET_CAPTURE);

        $logs = array_map(fn($i) => $i[0], $matches[0] ?? []);

        // Find byte offset of first match, so we can prepend the lines that were cut off from a previous log.
        $offset = $matches[0][0][1] ?? null;
        array_unshift($logs, mb_substr($chunk, 0, $offset));

        $arr = [];
        foreach ($logs as $record) {
            $arr[] = new LogRecord(['raw' => $record]);
        }

        return $reverse ? array_reverse($arr) : $arr;
    }

    public function bootstrapClass()
    {
        return app(LogViewer::class)->logLevels()[strtolower($this->heading->log_level ?? '')] ?? null;
    }
}
