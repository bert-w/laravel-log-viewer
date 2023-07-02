# bert-w/laravel-log-viewer
[![Latest Stable Version](https://poser.pugx.org/bert-w/laravel-log-viewer/v/stable)](https://packagist.org/packages/bert-w/laravel-log-viewer)
[![Total Downloads](https://poser.pugx.org/bert-w/laravel-log-viewer/downloads)](https://packagist.org/packages/bert-w/laravel-log-viewer)
[![License](https://poser.pugx.org/bert-w/laravel-log-viewer/license)](https://packagist.org/packages/bert-w/laravel-log-viewer)

A fast log file viewer for Laravel that is easy to add and configure.

# Features
- Compatible with Laravel 8 + 9 + 10
- Read big log files with ease without reaching the memory limit
- Includable in your own blade templates, or using a predefined layout
- Bootstrap 3 + 4 + 5 templates
- Bootstrap 5 comes with a **Dark Mode** setting

|                                      Dark Mode                                      |                                      Light Mode                                      |
|:-----------------------------------------------------------------------------------:|:------------------------------------------------------------------------------------:|
| ![](https://github.com/bert-w/laravel-log-viewer/blob/master/art/dark.png?raw=true) | ![](https://github.com/bert-w/laravel-log-viewer/blob/master/art/light.png?raw=true) |

# Installation

1. Install package:
```sh
composer require bert-w/laravel-log-viewer
```
2. (This happens automatically, except if you bypass [package discovery](https://laravel.com/docs/master/packages#package-discovery)) Add the service provider to your `config/app.php`:
```php
'providers' => [
    // ...
    BertW\LaravelLogViewer\LogViewerServiceProvider::class,
]
```
The service provider sets up the views, routes, configuration and authentication. A `\BertW\LaravelLogViewer\LogViewer::class`
is also bound as a singleton to the service container. This allows you to inject the log viewer anywhere (for instance in your
custom controller) using `$logViewer = app(\BertW\LaravelLogViewer\LogViewer::class)`.
3. a) (optional) Publish configuration file: 
```sh
php artisan vendor:publish --provider=BertW\LaravelLogViewer\LogViewerServiceProvider
```
3. b) or copy it manually into `config/logviewer.php`:
```php
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
````
4. By default, your log viewer should be available at `/logviewer`. For customization, see the [Customization](#customization) section below.

## Authorization
Setting up authorization for the log viewer is easy: within any loaded service provider (like `AppServiceProvider`),
add the following lines in your `register()` method:
```php
\BertW\LaravelLogViewer\LogViewer::auth(function(\Illuminate\Http\Request $request) {
    return $request->user()->role === 'admin';
});
```
By default, if no custom authorization callback is given, the log viewer is only accessible in your `local` environment.

## Customization
Including the log viewer in one of your own templates can be done simply by modifying the view file `resources/views/vendor/logviewer/index.blade.php` (available after the publish-command), which has the following default:
```php
@extends('logviewer::bootstrap-5.layout')
@section('content')
    <div class="container">
        @include('logviewer::bootstrap-5.index')
    </div>
@endsection
```
The standard layout contains pre-included Bootstrap CSS/JS files linked from `https://cdn.jsdelivr.net`.