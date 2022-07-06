<?php

namespace App\Http\Controllers\Admin\Import;

use Flash;
use Response;
use Illuminate\Support\Facades\Auth;
use Request;
use App\Http\Controllers\AppBaseController;
use App\Imports\CategoryImport;
use Maatwebsite\Excel\Facades\Excel;

class CategoryController extends AppBaseController
{
	public function __construct()
	{
		$this->middleware('auth');
	}

	/**
	 * Display a listing of the Service.
	 *
	 * @param ServiceDataTable $serviceDataTable
	 * @return Response
	 */
	public function index()
	{
		$fileName = 'import_taxonomy_nested_categories.xlsx';
		Excel::import(new CategoryImport, 'public/imports/' . $fileName, 'local');

		//return redirect(route('definitions/terms', ['taxonomy' => 12]));
	}
}
