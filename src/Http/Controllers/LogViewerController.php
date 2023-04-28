<?php

namespace BertW\LaravelLogViewer\Http\Controllers;

use BertW\LaravelLogViewer\LogViewer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LogViewerController extends Controller
{
    public function index(Request $request, LogViewer $logViewer)
    {
        return view('logviewer::bootstrap-5.layout.index');
    }

    public function raw(Request $request, LogViewer $logViewer, $file)
    {
        $this->validateFile($file = base64_decode($file));

        return response()->file($file, ['Content-Type' => 'text/plain']);
    }

    /**
     * Validate a real path by checking if it is in the storage path as defined by the log viewer.
     *
     * @param string $file
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function validateFile($file)
    {
        if (!str_starts_with($file, app(LogViewer::class)->storagePath())) {
            abort(403, 'This file is not a valid file or it is not in the log directory.');
        }
    }

    public function download(Request $request, LogViewer $logViewer, $file)
    {
        $this->validateFile($file = base64_decode($file));

        return response()->download($file);
    }

    public function destroy(Request $request, LogViewer $logViewer, $file)
    {
        $this->validateFile($file = base64_decode($file));
        $logViewer->fileSystem()->delete($file);

        return redirect()->back();
    }
}
