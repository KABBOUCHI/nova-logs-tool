<?php

namespace KABBOUCHI\LogsTool\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
use KABBOUCHI\LogsTool\LogsTool;
use KABBOUCHI\Ward\Ward;

class LogsController extends Controller
{
    public function index()
    {
        Ward::setFile($file = request()->input('file', 'laravel.log'));

        $logs = Ward::all();

        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        $collection = collect($logs);

        $search = request()->input('search');

        if ($search) {
            $collection = $collection->filter(function ($log) use ($search) {
                return false !== stristr($log['text'], $search);
            });
        }

        $perPage = config('nova-logs-tool.perPage');

        $currentPageSearchResults = $collection->slice(($currentPage - 1) * $perPage, $perPage)->values()->toArray();

        return new LengthAwarePaginator($currentPageSearchResults, count($collection), $perPage);
    }

    public function dailyLogFiles()
    {
        return collect(Ward::getFiles(true))->filter(function ($file) {
            return preg_match(config('nova-logs-tool.regexForFiles'), $file);
        })->values()->all();
    }

    /**
     * @param $log
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \Exception
     */
    public function show($log, Request $request)
    {
        if (! LogsTool::authorizedToDownload($request)) {
            abort(403);
        }

        return response()->download(Ward::pathToLogFile($log));
    }

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function destroy(Request $request)
    {
        if (! LogsTool::authorizedToDelete($request)) {
            abort(403);
        }

        app('files')->delete(Ward::pathToLogFile(request('file')));
        cache()->clear();
    }

    public function permissions(Request $request)
    {
        return [
            'canDownload' => LogsTool::authorizedToDownload($request),
            'canDelete'   => LogsTool::authorizedToDelete($request),
        ];
    }
}
