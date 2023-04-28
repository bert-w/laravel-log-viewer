<?php
    $logViewer = app(\BertW\LaravelLogViewer\LogViewer::class);
    $selectedLog = $logViewer->selectedLog();
?>
<div id="log-viewer" class="mt-4">
    @if($title = $logViewer->title())<h1 class="fs-4"><a href="{{ request()->url() }}">{{ $title }}</a></h1>@endif
    @include('logviewer::bootstrap-4.list', ['logs' => $logViewer->logs(), '$selectedLog' => $selectedLog])

    @if($selectedLog)
        @include('logviewer::bootstrap-4.show', ['selectedLog' => $selectedLog])
    @endif
</div>