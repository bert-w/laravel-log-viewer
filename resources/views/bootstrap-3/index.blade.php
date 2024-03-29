<?php
    $logViewer = app(\BertW\LaravelLogViewer\LogViewer::class);
    $selectedLog = $logViewerFile ?? $logViewer->preselected();
?>
<div id="log-viewer">
    @if($title = $logViewer->title())<h1 class="fs-4"><a href="{{ $logViewer->route('index') }}">{{ $title }}</a></h1>@endif
    @include('logviewer::bootstrap-3.list', ['logs' => $logViewer->logs(), '$selectedLog' => $selectedLog])

    @if($selectedLog)
        @include('logviewer::bootstrap-3.show', ['selectedLog' => $selectedLog])
    @endif
</div>