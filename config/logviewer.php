<?php

return [
    /*
    | The base URI for the log viewer.
    */
    'url' => '/logviewer',

    /*
    | The title for the logviewer page. If `null`, no title is shown.
    */
    'title' => config('app.name') . ' Log Viewer',

    /*
    | The storage path that contains the logs to be displayed in the log viewer.
    */
    'storage_path' => storage_path('logs'),

    /*
    | The amount of lines from a log file that are read per page. The amount of logs that
    | are shown per page depends on this value, and it may differ depending on the
    | length of a single log record.
    */
    'lines_per_page' => 3200,

    /*
    | The max length in bytes that a single line may have. Content that exceeds this limit will
    | be truncated from view. Note: disabling this feature with `null` may cause memory issues
    | with big log files that have few newlines.
    */
    'max_line_length' => 16000,

    /*
    | The threshold in bytes for a log file to be marked as "big". This allows
    | the frontend to visually indicate that the log file is big.
    */
    'big_file_threshold' => 2**26,

    /*
    | Preselect the first log file based on the given ordering.
    | If `null`, no log file will be opened by default.
    */
    'preselect' => ['modified_at', 'desc'],
];
