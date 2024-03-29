<div class="panel panel-default">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-6"><a href="" class="text-decoration-none">{{ $selectedLog->path . DIRECTORY_SEPARATOR . $selectedLog->basename }}</a>
                <br/>
                <small class="text-muted">Last modified: {{ $selectedLog->modified_at }}</small>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-4">{{ $selectedLog->linesCount() }} lines</div>
                    <div class="col-md-4 {{ $selectedLog->is_big ? 'text-danger' : '' }}">{{ $selectedLog->size() }}</div>
                    <div class="col-md-4">
                        <a href="{{ $logViewer->route('raw', $selectedLog->routeParameter()) }}" target="_blank" class="btn btn-sm btn-primary">Raw</a>
                        <a href="{{ $logViewer->route('download', $selectedLog->routeParameter()) }}" class="btn btn-sm btn-primary">Download</a>
                        <form id="logviewer-delete" style="display: none;" method="POST" action="{{ $logViewer->route('destroy', $selectedLog->routeParameter()) }}">
                            <input type="hidden" name="_method" value="DELETE" />
                        </form>
                        <button onclick="event.preventDefault(); if(confirm('Really delete this file?')) { document.querySelector('#logviewer-delete').submit(); }" class="btn btn-sm btn-danger">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-body">
        @section('paginator')
            {{ ($paginator = $selectedLog->paginate()->appends(request()->query())) }}
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
                        <tr data-toggle="collapse" data-target="#stacktrace-{{ $id }}">
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
