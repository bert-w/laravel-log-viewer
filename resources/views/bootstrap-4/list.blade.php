<div class="list-group shadow mb-4">
    @foreach($logs as $path => $log)
        <div class="list-group-item list-group-item-action py-0">
            <div class="row">
                <div class="col">
                    <a href="{{ $logViewer->route('index', $log->routeParameter()) }}" class="py-2 text-decoration-none d-inline-block w-100">@if($selectedLog && $log->real_path === $selectedLog->real_path) &bull; @endif {{ $log->displayName() }}</a>
                </div>
                <div class="col-2 py-2">
                    {{ $log->modified_at }}
                </div>
                <div class="col-2 py-2 text-right {{ $log->is_big ? 'text-danger' : '' }}">
                    {{ $log->size() }}
                </div>
            </div>
        </div>
    @endforeach
</div>