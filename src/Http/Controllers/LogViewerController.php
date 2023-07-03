<?php

namespace BertW\LaravelLogViewer\Http\Controllers;

use BertW\LaravelLogViewer\LogViewer;
use BertW\LaravelLogViewer\RouteBinding;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LogViewerController extends Controller
{
    public function index(Request $request, LogViewer $logViewer)
    {
        return view('logviewer::index', [
            'logViewerFile' => ($param = $request->route('logViewerFile')) ? RouteBinding::parse($param) : null,
        ]);
    }

    public function raw(Request $request)
    {
        if (!$log = RouteBinding::parse($request->route('logViewerFile'))) {
            abort(404);
        }

        return response()->file($log->real_path, ['Content-Type' => 'text/plain']);
    }

    public function download(Request $request)
    {
        if (!$log = RouteBinding::parse($request->route('logViewerFile'))) {
            abort(404);
        }

        return response()->download($log->real_path);
    }

    public function destroy(Request $request, LogViewer $logViewer)
    {
        if (!$log = RouteBinding::parse($request->route('logViewerFile'))) {
            abort(404);
        }

        $logViewer->fileSystem()->delete($log->real_path);

        return redirect()->back();
    }
}
