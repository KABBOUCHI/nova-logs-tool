<?php

namespace KABBOUCHI\LogsTool\Http\Controllers;

use KABBOUCHI\Ward\Ward;
use Illuminate\Routing\Controller;
use Illuminate\Pagination\LengthAwarePaginator;

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

        $perPage = 6;

        $currentPageSearchResults = $collection->slice(($currentPage - 1) * $perPage, $perPage)->values()->toArray();

        return new LengthAwarePaginator($currentPageSearchResults, count($collection), $perPage);
    }

    public function dailyLogFiles()
    {
        return collect(Ward::getFiles(true))->filter(function ($file) {
            return strpos($file, 'laravel') === 0;
        });
    }

    /**
     * @param $log
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \Exception
     */
    public function show($log)
    {
        return response()->download(Ward::pathToLogFile($log));
    }

    /**
     * @throws \Exception
     */
    public function destroy()
    {
        app('files')->delete(Ward::pathToLogFile(request('file')));
        cache()->clear();
    }
}
