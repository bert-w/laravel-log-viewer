<?php

return [
    /*
    | The base URI for the log viewer.
    */
    'url' => '/logviewer',

    /*
    | The route name prefix to use for the logviewer route names.
    */
    'route_name_prefix' => 'logviewer.',

    /*
    | Display name for log files when they are listed in the interface, which is one of:
    | 'short' (filename only) or 'full' (absolute path).
    */
    'log_display_name' => 'short',

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
    | length of a single log record (Default: 3200).
    */
    'lines_per_page' => 3200,

    /*
    | The max length in bytes that a single line may have. Content that exceeds this limit will
    | be truncated from view. Note: disabling this feature with `null` may cause memory issues
    | with big log files that exceed this max line length (Default: 16000).
    */
    'max_line_length' => 16000,

    /*
    | The threshold in bytes for a log file to be marked as "big". This allows
    | the frontend to visually indicate that the log file is big (Default: 64MB).
    */
    'big_file_threshold' => 2**26,

    /*
    | The sorting order of the log files how they appear in the interface.
    */
    'sort_by' => ['modified_at', 'desc'],

    /*
    | Preselect the first log file based on the given ordering.
    | If `null`, no log file will be opened by default.
    */
    'preselect' => ['modified_at', 'desc'],
];
