<?php

namespace App\Http\Controllers\Admin\Import;

use Flash;
use Response;
use Illuminate\Support\Facades\Auth;
use Request;
use App\Http\Controllers\AppBaseController;
use App\Imports\PartnerImport;
use App\Repositories\Settings\PartnerRepository;
use Maatwebsite\Excel\Facades\Excel;

class PartnersController extends AppBaseController
{
	/** @var  PartnerRepository */
	private $partnerRepository;

	public function __construct(
		PartnerRepository $partnerRepo
	) {
		$this->middleware('auth');
		$this->partnerRepository = $partnerRepo;
	}

	/**
	 * Display a listing of the Service.
	 *
	 * @param ServiceDataTable $serviceDataTable
	 * @return Response
	 */
	public function index()
	{
		$fileName = 'import_partners.xlsx';
		Excel::import(new PartnerImport, 'public/imports/' . $fileName, 'local');

		return redirect(route('settings.partners.index'));
	}
}
