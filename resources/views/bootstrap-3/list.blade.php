<div class="list-group mb-4">
    @foreach($logs as $path => $log)
        <div class="list-group-item">
            <div class="row">
                <div class="col-md-8">
                    <a href="{{ $logViewer->route('index', $log->routeParameter()) }}" class="py-2 text-decoration-none d-inline-block w-100">@if($selectedLog && $log->real_path === $selectedLog->real_path) &bull; @endif {{ $log->displayName() }}</a>
                </div>
                <div class="col-md-2">
                    {{ $log->modified_at }}
                </div>
                <div class="col-md-2 {{ $log->is_big ? 'text-danger' : '' }}">
                    {{ $log->size() }}
                </div>
            </div>
        </div>
    @endforeach
</div>