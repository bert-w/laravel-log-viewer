<div class="card shadow">
    <div class="card-header d-flex justify-content-between">
        <div><a href="" class="text-decoration-none">{{ $selectedLog->path . DIRECTORY_SEPARATOR . $selectedLog->basename }}</a>
            <br/>
            <small class="text-muted">Last modified: {{ $selectedLog->modified_at }}</small>
        </div>
        <div class="d-flex align-items-center">
            <div class="me-4">{{ $selectedLog->linesCount() }} lines</div>
            <div class="me-4 {{ $selectedLog->is_big ? 'text-danger' : '' }}">{{ $selectedLog->size() }}</div>
            <div>
                <a href="{{ $logViewer->route('raw', $selectedLog->encodedPath()) }}" target="_blank" class="btn btn-sm btn-primary">Raw</a>
                <a href="{{ $logViewer->route('download', $selectedLog->encodedPath()) }}" class="btn btn-sm btn-primary">Download</a>
                <form id="logviewer-delete" style="display: none;" method="POST" action="{{ $logViewer->route('destroy', $selectedLog->encodedPath()) }}">
                    <input type="hidden" name="_method" value="DELETE" />
                </form>
                <button onclick="event.preventDefault(); if(confirm('Really delete this file?')) { document.querySelector('#logviewer-delete').submit(); }" class="btn btn-sm btn-danger">Delete</button>
            </div>
        </div>
    </div>
    <div class="card-body">
        @section('paginator')
            {{ ($paginator = $selectedLog->paginate()->appends(request()->query()))->render(view()->exists('pagination::bootstrap-5') ? 'pagination::bootstrap-5' : null) }}
        @endsection
        @yield('paginator')
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Level</th>
                    <th>Date</th>
                    <th>Env</th>
                    <th>Message</th>
                </tr>
                </thead>
                <tbody>
                @foreach(\BertW\LaravelLogViewer\LogRecord::hydrate($paginator->join('')) as $id => $record)
                    <tr>
                        <td class="text-{{ $bsClass = $record->bootstrapClass() ?? '' }}">{{ $record->heading->log_level ?? 'EMPTY' }}</td>
                        <td>{{ $record->heading->created_at }}</td>
                        <td>{{ $record->heading->environment }}</td>
                        <td><span>{{ $record->heading->title }}</span></td>
                    </tr>
                    @if($record->lines->count())
                        <tr data-bs-toggle="collapse" data-bs-target="#stacktrace-{{ $id }}">
                            <td colspan="4" class="p-1 view-stacktrace text-center">- View stacktrace -</td>
                        </tr>
                        <tr id="stacktrace-{{ $id }}" class="collapse">
                            <td colspan="4">
                                <div class="stacktrace text-muted">{{ $record->lines->join("\n") }}</div>
                            </td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
        @yield('paginator')
    </div>
</div>
