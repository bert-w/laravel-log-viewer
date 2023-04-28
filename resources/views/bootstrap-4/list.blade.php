<div class="list-group shadow">
    @foreach($logs as $path => $log)
        <div class="list-group-item list-group-item-action py-0">
            <div class="row">
                <div class="col">
                    <a href="?v={{ $log->encodedPath() }}" class="py-2 text-decoration-none d-inline-block w-100">@if($log === ($selectedLog ?? false)) &bull; @endif {{ $log->prettyPath() . ' ' . DIRECTORY_SEPARATOR }} {{ $log->basename }}</a>
                </div>
                <div class="col-2 py-2">
                    {{ $log->modified_at }}
                </div>
                <div class="col-2 py-2 text-end {{ $log->is_big ? 'text-danger' : '' }}">
                    {{ $log->size() }}
                </div>
            </div>
        </div>
    @endforeach
</div>