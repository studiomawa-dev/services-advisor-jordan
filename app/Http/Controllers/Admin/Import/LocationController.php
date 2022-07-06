<?php

namespace App\Http\Controllers\Admin\Import;

use Flash;
use Response;
use Illuminate\Support\Facades\Auth;
use Request;
use App\Http\Controllers\AppBaseController;
use App\Imports\LocationImport;
use App\Imports\LocationTermImport;
use Maatwebsite\Excel\Facades\Excel;

class LocationController extends AppBaseController
{
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function index()
	{
		$fileName = 'import_locations.xlsx';
		Excel::import(new LocationImport, 'public/imports/' . $fileName, 'local');
	}

	/**
	 * Display a listing of the Service.
	 *
	 * @param ServiceDataTable $serviceDataTable
	 * @return Response
	 */
	public function terms()
	{
		$fileName = 'import_taxonomy_locations.xlsx';
		Excel::import(new LocationTermImport, 'public/imports/' . $fileName, 'local');

		//return redirect(route('admin/definitions/terms', ['taxonomy' => 12]));
	}
}
