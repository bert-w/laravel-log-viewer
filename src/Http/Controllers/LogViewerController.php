<?php

namespace BertW\LaravelLogViewer\Http\Controllers;

use BertW\LaravelLogViewer\LogViewer;
use BertW\LaravelLogViewer\RouteBinding;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LogViewerController
{
    public function index(Request $request, LogViewer $logViewer): View
    {
        return app(ViewFactory::class)->make('logviewer::index', [
            'logViewerFile' => ($param = $request->route('logViewerFile')) ? RouteBinding::parse($param) : null,
        ]);
    }

    public function raw(Request $request): BinaryFileResponse
    {
        if (!$log = RouteBinding::parse($request->route('logViewerFile'))) {
            throw new NotFoundHttpException();
        }

        return app(ResponseFactory::class)->file($log->real_path, ['Content-Type' => 'text/plain']);
    }

    public function download(Request $request): mixed
    {
        if (!$log = RouteBinding::parse($request->route('logViewerFile'))) {
            throw new NotFoundHttpException();
        }

        return app(ResponseFactory::class)->download($log->real_path);
    }

    public function destroy(Request $request, LogViewer $logViewer): mixed
    {
        if (!$log = RouteBinding::parse($request->route('logViewerFile'))) {
            throw new NotFoundHttpException();
        }

        $logViewer->fileSystem()->delete($log->real_path);

        return app('redirect')->back();
    }
}
