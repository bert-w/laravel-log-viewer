<?php
    $logViewer = app(\BertW\LaravelLogViewer\LogViewer::class);
    $selectedLog = $logViewer->selectedLog();
?>
<div id="log-viewer" class="mt-4">
    <div class="row align-items-center mb-2">
        <div class="col">@if($title = $logViewer->title())<h1 class="fs-4"><a href="{{ request()->url() }}">{{ $title }}</a></h1>@endif</div>
        <div class="col-auto">@include('logviewer::bootstrap-5.select_theme')</div>
    </div>

    @include('logviewer::bootstrap-5.list', ['logs' => $logViewer->logs(), '$selectedLog' => $selectedLog])

    @if($selectedLog)
        @include('logviewer::bootstrap-5.show', ['selectedLog' => $selectedLog])
    @endif
</div>