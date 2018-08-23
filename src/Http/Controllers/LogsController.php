<?php

namespace KABBOUCHI\LogsTool\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
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

		if ($search)
			$collection = $collection->filter(function ($log) use ($search) {
				return false !== stristr($log['text'], $search);
			});

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
}
