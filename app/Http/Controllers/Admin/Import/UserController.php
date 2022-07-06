<?php

namespace App\Http\Controllers\Admin\Import;

use Flash;
use Response;
use Illuminate\Support\Facades\Auth;
use Request;
use App\Http\Controllers\AppBaseController;
use App\Imports\PartnerImport;
use App\Imports\UserImport;
use App\Repositories\Settings\PartnerRepository;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends AppBaseController
{
	/** @var  PartnerRepository */
	private $partnerRepository;

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
		$fileName = 'import_users.xlsx';
		Excel::import(new UserImport, 'public/imports/' . $fileName, 'local');

		//return redirect(route('settings.users.index'));
	}
}
