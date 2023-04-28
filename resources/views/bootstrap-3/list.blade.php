<div class="list-group">
    @foreach($logs as $path => $log)
        <div class="list-group-item">
            <div class="row">
                <div class="col-md-8">
                    <a href="?v={{ $log->encodedPath() }}" class="py-2 text-decoration-none d-inline-block w-100">@if($log === ($selectedLog ?? false)) &bull; @endif {{ $log->prettyPath() . ' ' . DIRECTORY_SEPARATOR }} {{ $log->basename }}</a>
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