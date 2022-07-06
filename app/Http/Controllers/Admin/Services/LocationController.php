<?php

namespace App\Http\Controllers\Admin\Services;

use Flash;
use Request;
use Response;

use Illuminate\Support\Facades\Auth;
use App\DataTables\Services\LocationDataTable;
use App\Http\Requests\Services;
use App\Http\Requests\Services\CreateLocationRequest;
use App\Http\Requests\Services\UpdateLocationRequest;
use App\Repositories\Services\LocationRepository;
use App\Repositories\Services\ServiceRepository;
use App\Repositories\Settings\LanguageRepository;
use App\Http\Controllers\AppBaseController;
use App\Repositories\Definitions\TermRepository;
use App\Models\Settings\Log;

class LocationController extends AppBaseController
{
	/** @var  LocationRepository */
	private $locationRepository;

	/** @var  LanguageRepository */
	private $langRepository;

	/** @var  TermRepository */
	private $termRepository;

	/** @var  ServiceRepository */
	private $serviceRepository;

	public function __construct(
		LocationRepository $locationRepo,
		ServiceRepository $serviceRepo,
		LanguageRepository $langRepo,
		TermRepository $termRepo
	) {
		$this->middleware('auth');
		$this->locationRepository = $locationRepo;
		$this->serviceRepository = $serviceRepo;
		$this->langRepository = $langRepo;
		$this->termRepository = $termRepo;
	}

	/**
	 * Display a listing of the Location.
	 *
	 * @param LocationDataTable $locationDataTable
	 * @return Response
	 */
	public function index(LocationDataTable $locationDataTable)
	{
		$location_terms = $this->termRepository->getLocationTerms();

		$src = Request::get('src');
		$city = Request::get('city');
		$district = Request::get('district');
		$sub_district = Request::get('sub_district');
		$neighborhood = Request::get('neighborhood');

		$pageData = array(
			'src' => $src,
			'city_id' => $city,
			'district_id' => $district,
			'sub_district_id' => $sub_district,
			'neighborhood' => $neighborhood,
			'location_terms' => $location_terms,
		);

		return $locationDataTable->render('services.locations.index', $pageData);
	}

	public function item($id)
	{
		return $this->locationRepository->find($id);
	}

	public function list(LocationDataTable $locationDataTable)
	{
		$city = Request::get('city');
		$district = Request::get('district');
		$sub_district = Request::get('sub_district');
		$neighborhood = Request::get('neighborhood');
		return $locationDataTable->render('services.locations.frame', array('city' => $city, 'district' => $district, 'sub_district' => $sub_district, 'neighborhood' => $neighborhood));
	}

	/**
	 * Show the form for creating a new Location.
	 *
	 * @return Response
	 */
	public function create()
	{
		$city = Request::get('city');
		$district = Request::get('district');
		$sub_district = Request::get('sub_district');
		$neighborhood = Request::get('neighborhood');

		$location = new \stdClass();
		$location->id = 0;
		$location->city_id = 0;
		$location->district_id = 0;
		$location->sub_district_id = 0;
		$location->neighborhood_id = 0;
		$location->latitude = 0;
		$location->longitude = 0;
		$langs = $this->langRepository->all();
		$location_terms = $this->termRepository->getLocationTerms();

		if (!isset($location->langs) || $location->langs == null) {
			$location->langs = [];
		}
		$locationLangs = $location->langs;

		foreach ($langs as $lang) {
			$currenLocationLang = null;
			foreach ($locationLangs as $locationLang) {
				if ($locationLang->lang_id == $lang->id) {
					$currenLocationLang = $locationLang;
				}
			}

			if ($currenLocationLang == null) {
				$currenLocationLang = new \stdClass();
				$currenLocationLang->name = '';
				$currenLocationLang->address = '';
				$currenLocationLang->direction = '';
				$currenLocationLang->lang_id = $lang->id;
			}
			$location->langs['l' . $lang->id] = $currenLocationLang;
		}

		return view('services.locations.create')
			->with('langs', $langs)
			->with('location', $location)
			->with('city', $city)
			->with('district', $district)
			->with('sub_district', $sub_district)
			->with('neighborhood', $neighborhood)
			->with('location_terms', $location_terms);
	}

	/**
	 * Store a newly created Location in storage.
	 *
	 * @param CreateLocationRequest $request
	 *
	 * @return Response
	 */
	public function store(CreateLocationRequest $request)
	{
		$input = $request->all();
		$input['partner_ids'] = implode(',', Auth::user()->partnerIds());

		$location = $this->locationRepository->create($input);

		$this->locationRepository->saveLangs($input, $location);

		Flash::success('Location saved successfully.');
		Log::warning('Location', 'Create', 'Location created successfully.');

		return redirect(route('services.locations.index'));
	}

	/**
	 * Display the specified Location.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function show($id)
	{
		$location = $this->locationRepository->find($id);
		$langs = $this->langRepository->all();
		$location_terms = $this->termRepository->getLocationTerms();

		if (!isset($location->langs) || $location->langs == null) {
			$location->langs = [];
		}
		$locationLangs = $location->langs;

		foreach ($langs as $lang) {
			$currenLocationLang = null;
			foreach ($locationLangs as $locationLang) {
				if ($locationLang->lang_id == $lang->id) {
					$currenLocationLang = $locationLang;
				}
			}

			if ($currenLocationLang == null) {
				$currenLocationLang = new \stdClass();
				$currenLocationLang->name = '';
				$currenLocationLang->address = '';
				$currenLocationLang->direction = '';
				$currenLocationLang->lang_id = $lang->id;
			}
			$location->langs['l' . $lang->id] = $currenLocationLang;
		}

		if (empty($location)) {
			Flash::error('Location not found');

			return redirect(route('services.locations.index'));
		}

		return view('services.locations.show')
			->with('langs', $langs)
			->with('location', $location)
			->with('location_terms', $location_terms);
	}

	/**
	 * Show the form for editing the specified Location.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function edit($id)
	{
		$location = $this->locationRepository->find($id);
		$langs = $this->langRepository->all();
		$location_terms = $this->termRepository->getLocationTerms();

		if (!isset($location->langs) || $location->langs == null) {
			$location->langs = [];
		}
		$locationLangs = $location->langs;

		foreach ($langs as $lang) {
			$currenLocationLang = null;
			foreach ($locationLangs as $locationLang) {
				if ($locationLang->lang_id == $lang->id) {
					$currenLocationLang = $locationLang;
				}
			}

			if ($currenLocationLang == null) {
				$currenLocationLang = new \stdClass();
				$currenLocationLang->name = '';
				$currenLocationLang->address = '';
				$currenLocationLang->direction = '';
				$currenLocationLang->lang_id = $lang->id;
			}
			$location->langs['l' . $lang->id] = $currenLocationLang;
		}

		if (empty($location)) {
			Flash::error('Location not found');

			return redirect(route('services.locations.index'));
		}


		return view('services.locations.edit')
			->with('langs', $langs)
			->with('location', $location)
			->with('location_terms', $location_terms);
	}

	/**
	 * Update the specified Location in storage.
	 *
	 * @param  int              $id
	 * @param UpdateLocationRequest $request
	 *
	 * @return Response
	 */
	public function update($id, UpdateLocationRequest $request)
	{
		$location = $this->locationRepository->find($id);
		if (empty($location)) {
			Flash::error('Location not found');

			return redirect(route('services.locations.index'));
		}

		$input = $request->all();

		$location->update($input);
		$location->touch();

		$this->locationRepository->saveLangs($input, $location);

		Flash::success('Location updated successfully.');
		Log::warning('Location', 'Update', 'Location updated successfully.');

		return redirect(route('services.locations.index'));
	}

	/**
	 * Remove the specified Location from storage.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function destroy($id)
	{
		$location = $this->locationRepository->find($id);
		$location->touch();

		if (empty($location)) {
			Flash::error('Location not found');

			return redirect(route('services.locations.index'));
		}

		$services = $this->serviceRepository->getServicesByLocation($id);

		if (count($services) > 0) {
			$serviceLinks = [];
			foreach ($services as $service) {
				$serviceLinks[] = '<a href="/admin/services/services/' . $service->id . '">' . $service->id . '</a>';
			}

			Flash::error('This location cannot be deleted because this is your choosed location for ' . count($services) . ' service' . (count($services) > 1 ? 's' : '') . ' (' . implode('', $serviceLinks) . ').');
			Log::error('Location', 'Delete', 'Location in use.');

			return redirect(route('services.locations.index'));
		}

		$this->locationRepository->delete($id);

		Flash::success('Location deleted successfully.');
		Log::warning('Location', 'Delete', 'Location deleted successfully.');

		return redirect(route('services.locations.index'));
	}
}
