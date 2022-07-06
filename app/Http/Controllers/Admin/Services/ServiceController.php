<?php

namespace App\Http\Controllers\Admin\Services;

use Flash;
use Response;
use Illuminate\Support\Facades\Auth;
use Request;
use App\Models\Settings\Language;
use App\Http\Controllers\AppBaseController;
use App\DataTables\Services\ServiceDataTable;
use App\Repositories\Settings\TagRepository;
use App\Repositories\Definitions\TermRepository;
use App\Repositories\Services\ServiceRepository;
use App\Http\Requests\Services\CreateServiceRequest;
use App\Http\Requests\Services\UpdateServiceRequest;
use App\Repositories\Settings\PartnerRepository;
use App\Repositories\Services\ServiceContactRepository;
use App\Repositories\Services\ServiceHourRepository;
use App\Repositories\Services\ServiceTermRepository;
use App\Repositories\Services\ServiceCategoryRepository;
use App\Models\Settings\Log;
use App\Jobs\NotifyUserOfExport;

class ServiceController extends AppBaseController
{
	/** @var  ServiceRepository */
	private $serviceRepository;

	/** @var TagRepository */
	private $tagRepository;

	/** @var  TermRepository */
	private $termRepository;

	/** @var  PartnerRepository */
	private $partnerRepository;

	/** @var  ServiceContactRepository */
	private $serviceContactRepository;

	/** @var  ServiceHourRepository */
	private $serviceHourRepository;

	/** @var  ServiceTermRepository */
	private $serviceTermRepository;

	/** @var  ServiceCategoryRepository */
	private $serviceCategoryRepository;

	public function __construct(
		ServiceRepository $serviceRepo,
		TagRepository $tagRepository,
		TermRepository $termRepo,
		PartnerRepository $partnerRepo,
		ServiceContactRepository $serviceContactRepo,
		ServiceHourRepository $serviceHourRepo,
		ServiceTermRepository $serviceTermRepo,
		ServiceCategoryRepository $serviceCategoryRepo
	) {
		$this->middleware('auth');
		$this->serviceRepository = $serviceRepo;
		$this->tagRepository = $tagRepository;
		$this->termRepository = $termRepo;
		$this->partnerRepository = $partnerRepo;
		$this->serviceContactRepository = $serviceContactRepo;
		$this->serviceHourRepository = $serviceHourRepo;
		$this->serviceTermRepository = $serviceTermRepo;
		$this->serviceCategoryRepository = $serviceCategoryRepo;
	}

	/**
	 * Display a listing of the Service.
	 *
	 * @param ServiceDataTable $serviceDataTable
	 * @return Response
	 */
	public function index(ServiceDataTable $serviceDataTable)
	{
		//$category_terms = $this->termRepository->getRootCategoryTermsForSelect(true);
		$tags = $this->tagRepository->getForSelect(true);

		$category_terms = $this->termRepository->getNestedCategories();
		$location_terms = $this->termRepository->getLocationTerms();
		$accessibility_terms = $this->termRepository->getTermsForSelect(1, null, true);
		$intake_criteria_terms = $this->termRepository->getTermsForSelect(8, null, true);
		$referral_method_terms = $this->termRepository->getTermsForSelect(14, null, true);

		$partners = $this->partnerRepository->getPartnersForSelect('en', true);

		$sid = Request::get('sid');
		$tag_id = Request::get('tag');
		$category = Request::get('category');
		$accessibility = Request::get('accessibility');
		$intake_criteria = Request::get('intake_criteria');
		$referral_method = Request::get('referral_method');
		$city = Request::get('city');
		$district = Request::get('district');
		$partner = Request::get('partner');
		$status = Request::get('status');

		$pageData = array(
			'service_id' => $sid,
			'tag_id' => $tag_id,
			'category_id' => $category,
			'partner_id' => $partner,
			'accessibility_id' => $accessibility,
			'intake_criteria_id' => $intake_criteria,
			'referral_method_id' => $referral_method,
			'city_id' => $city,
			'district_id' => $district,
			'status_id' => $status,
			'tags' => $tags,
			'category_terms' => $category_terms,
			'location_terms' => $location_terms,
			'accessibility_terms' => $accessibility_terms,
			'intake_criteria_terms' => $intake_criteria_terms,
			'referral_method_terms' => $referral_method_terms,
			'partners' => $partners,
		);

		return $serviceDataTable->render('services.services.index', $pageData);
	}

	/**
	 * Show the form for creating a new Service.
	 *
	 * @return Response
	 */
	public function create()
	{
		$langs = Language::all();
		$defaultLangCode = Language::defaultLang()->code;
		$tagIds = Auth::user()->tagIds();

		$service = new \stdClass();
		$service->tag_id = 1;
		$service->publish_date = date('Y-m-d');
		$service->location = [];
		$service->termIds = [];
		$service->hours = [];
		$service->contacts = [];
		$service->published = false;
		$service->backendonly = false;
		$service->is_remote = false;

		$tags = $this->tagRepository->getForSelect();

		$accessibility_terms = $this->termRepository->getByTaxonomy(1);
		$availability_terms = $this->termRepository->getByTaxonomy(2);
		$available_nationality_terms = $this->termRepository->getByTaxonomy(3);
		$complaints_mechanism_terms = $this->termRepository->getByTaxonomy(4);
		$coverage_terms = $this->termRepository->getByTaxonomy(5);
		$feedback_delay_terms = $this->termRepository->getByTaxonomy(6);
		$feedback_mechanism_terms = $this->termRepository->getByTaxonomy(7);
		$intake_criteria_terms = $this->termRepository->getByTaxonomy(8);
		$referral_method_terms = $this->termRepository->getByTaxonomy(14);
		$referral_next_step_terms = $this->termRepository->getByTaxonomy(15);
		$registration_type_terms = $this->termRepository->getByTaxonomy(16);
		$response_delay_terms = $this->termRepository->getByTaxonomy(17);
		$category_terms = $this->termRepository->getCategoryTerms($defaultLangCode, $tagIds);
		$location_terms = $this->termRepository->getLocationTerms($defaultLangCode);
		$partners = $this->partnerRepository->getPartnersForSelect($defaultLangCode);

		if (!Auth::user()->isAdmin()) {
			$partnerIds = Auth::user()->partnerIds();
			foreach ($partners as $partnerId => $partnerName) {
				if ($partnerId != '' && !in_array($partnerId, $partnerIds)) {
					unset($partners[$partnerId]);
				}
			}
			$contacts = $this->serviceContactRepository->getContactsWithPartner($partnerIds);
		} else {
			$contacts = $this->serviceContactRepository->getContactsWithPartner();
		}


		return view('services.services.create')
			->with('tags', $tags)
			->with('service', $service)
			->with('category_terms', $category_terms)
			->with('location_terms', $location_terms)
			->with('accessibility_terms', $accessibility_terms)
			->with('availability_terms', $availability_terms)
			->with('available_nationality_terms', $available_nationality_terms)
			->with('complaints_mechanism_terms', $complaints_mechanism_terms)
			->with('coverage_terms', $coverage_terms)
			->with('feedback_delay_terms', $feedback_delay_terms)
			->with('feedback_mechanism_terms', $feedback_mechanism_terms)
			->with('intake_criteria_terms', $intake_criteria_terms)
			->with('referral_method_terms', $referral_method_terms)
			->with('referral_next_step_terms', $referral_next_step_terms)
			->with('registration_type_terms', $registration_type_terms)
			->with('response_delay_terms', $response_delay_terms)
			->with('partners', $partners)
			->with('contacts', $contacts)
			->with('langs', $langs);
	}

	/**
	 * Store a newly created Service in storage.
	 *
	 * @param CreateServiceRequest $request
	 *
	 * @return Response
	 */
	public function store(CreateServiceRequest $request)
	{
		$input = $request->all();

		$input['start_date'] = date('Y-m-d', strtotime($input['start_date']));
		$input['end_date'] = date('Y-m-d', strtotime($input['end_date']));
		$input['published'] = (isset($input['published']) && $input['published'] == 'on') ? true : false;
		$input['backendonly'] = (isset($input['backendonly']) && $input['backendonly'] == 'on') ? true : false;
		$input['is_remote'] = (isset($input['is_remote']) && $input['is_remote'] == 'on') ? true : false;

		if (isset($input['publish_date']) && $input['publish_date'] != null && strlen($input['publish_date']) > 0) {
			$input['publish_date'] = date('Y-m-d', strtotime($input['publish_date']));
		} else {
			$input['publish_date'] = date('Y-m-d', strtotime($input['start_date']));
		}

		$service = $this->serviceRepository->create($input);

		$this->serviceRepository->saveLangs($input, $service, $this->termRepository);

		$allTermIds = $input['terms'];
		$termIds = [];
		$termTagTypes = [];
		foreach ($allTermIds as $allTermId) {
			if (intval($allTermId) > 0) {
				$termIds[] = intval($allTermId);

				$term = $this->termRepository->find($allTermId);
				if ($term->taxonomy_id == 12) {
					if (!in_array($term->tag_id, $termTagTypes)) {
						$termTagTypes[] = $term->tag_id;
					}
				}
			}
		}

		if (!empty($termTagTypes)) {
			if (in_array(3, $termTagTypes) || (in_array(1, $termTagTypes) && in_array(2, $termTagTypes))) {
				$service->tag_id = 3;
			} elseif (in_array(1, $termTagTypes)) {
				$service->tag_id = 1;
			} elseif (in_array(2, $termTagTypes)) {
				$service->tag_id = 2;
			}
		} else {
			$service->tag_id = 1;
		}

		$service->save();

		$this->serviceContactRepository->setServiceContacts($service->id, $input);
		$this->serviceTermRepository->setServiceTermIds($service->id, $termIds);
		$this->serviceCategoryRepository->setServiceCategoryTermIds($service->id, $termIds);
		$this->serviceHourRepository->updateOrInsert($service->id, $input);

		Flash::success(__('app.Service saved successfully'));
		Log::warning('Service', 'Create', 'Service created successfully.');

		return redirect(route('services.services.index'));
	}

	/**
	 * Display the specified Service.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function show($id)
	{
		$langs = Language::all();
		$defaultLangCode = Language::defaultLang()->code;
		$tagIds = Auth::user()->tagIds();

		$service = $this->serviceRepository->getFull($id);

		$accessibility_terms = $this->termRepository->getByTaxonomy(1);
		$availability_terms = $this->termRepository->getByTaxonomy(2);
		$available_nationality_terms = $this->termRepository->getByTaxonomy(3);
		$complaints_mechanism_terms = $this->termRepository->getByTaxonomy(4);
		$coverage_terms = $this->termRepository->getByTaxonomy(5);
		$feedback_delay_terms = $this->termRepository->getByTaxonomy(6);
		$feedback_mechanism_terms = $this->termRepository->getByTaxonomy(7);
		$intake_criteria_terms = $this->termRepository->getByTaxonomy(8);
		$referral_method_terms = $this->termRepository->getByTaxonomy(14);
		$referral_next_step_terms = $this->termRepository->getByTaxonomy(15);
		$registration_type_terms = $this->termRepository->getByTaxonomy(16);
		$response_delay_terms = $this->termRepository->getByTaxonomy(17);
		$category_terms = $this->termRepository->getCategoryTerms($defaultLangCode, $tagIds);
		$location_terms = $this->termRepository->getLocationTerms();
		$partners = $this->partnerRepository->getPartnersForSelect('en');
		$contacts = $this->serviceContactRepository->getContactsWithPartner();

		if (empty($service)) {
			Flash::error(__('app.Service not found'));

			return redirect(route('services.services.index'));
		}

		if (!isset($service->langs) || $service->langs == null) {
			$service->langs = [];
		}
		$serviceLangs = $service->langs;

		foreach ($langs as $lang) {
			$currentServiceLang = null;
			foreach ($serviceLangs as $serviceLang) {
				if ($serviceLang->lang_id == $lang->id) {
					$currentServiceLang = $serviceLang;
				}
			}

			if ($currentServiceLang == null) {
				$currentServiceLang = new \stdClass();
				$currentServiceLang->name = '';
				$currentServiceLang->lang_id = $lang->id;
			}
			$service->langs['l' . $lang->id] = $currentServiceLang;
		}

		return view('services.services.show')
			->with('service', $service)
			->with('category_terms', $category_terms)
			->with('location_terms', $location_terms)
			->with('accessibility_terms', $accessibility_terms)
			->with('availability_terms', $availability_terms)
			->with('available_nationality_terms', $available_nationality_terms)
			->with('complaints_mechanism_terms', $complaints_mechanism_terms)
			->with('coverage_terms', $coverage_terms)
			->with('feedback_delay_terms', $feedback_delay_terms)
			->with('feedback_mechanism_terms', $feedback_mechanism_terms)
			->with('intake_criteria_terms', $intake_criteria_terms)
			->with('referral_method_terms', $referral_method_terms)
			->with('referral_next_step_terms', $referral_next_step_terms)
			->with('registration_type_terms', $registration_type_terms)
			->with('response_delay_terms', $response_delay_terms)
			->with('partners', $partners)
			->with('contacts', $contacts)
			->with('langs', $langs);
	}

	/**
	 * Show the form for editing the specified Service.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function edit($id)
	{
		$langs = Language::all();
		$defaultLangCode = Language::defaultLang()->code;
		$tagIds = Auth::user()->tagIds();

		$service = $this->serviceRepository->getFull($id);

		$tags = $this->tagRepository->getForSelect();

		$accessibility_terms = $this->termRepository->getByTaxonomy(1);
		$availability_terms = $this->termRepository->getByTaxonomy(2);
		$available_nationality_terms = $this->termRepository->getByTaxonomy(3);
		$complaints_mechanism_terms = $this->termRepository->getByTaxonomy(4);
		$coverage_terms = $this->termRepository->getByTaxonomy(5);
		$feedback_delay_terms = $this->termRepository->getByTaxonomy(6);
		$feedback_mechanism_terms = $this->termRepository->getByTaxonomy(7);
		$intake_criteria_terms = $this->termRepository->getByTaxonomy(8);
		$referral_method_terms = $this->termRepository->getByTaxonomy(14);
		$referral_next_step_terms = $this->termRepository->getByTaxonomy(15);
		$registration_type_terms = $this->termRepository->getByTaxonomy(16);
		$response_delay_terms = $this->termRepository->getByTaxonomy(17);
		$category_terms = $this->termRepository->getCategoryTerms($defaultLangCode, $tagIds);
		$location_terms = $this->termRepository->getLocationTerms();
		$partners = $this->partnerRepository->getPartnersForSelect('en');

		if (!Auth::user()->isAdmin()) {
			$partnerIds = Auth::user()->partnerIds();
			foreach ($partners as $partnerId => $partnerName) {
				if ($partnerId != '' && !in_array($partnerId, $partnerIds)) {
					unset($partners[$partnerId]);
				}
			}
			$contacts = $this->serviceContactRepository->getContactsWithPartner($partnerIds);
		} else {
			$contacts = $this->serviceContactRepository->getContactsWithPartner();
		}

		if (empty($service)) {
			Flash::error(__('app.Service not found'));

			return redirect(route('services.services.index'));
		}

		if (!isset($service->langs) || $service->langs == null) {
			$service->langs = [];
		}
		$serviceLangs = $service->langs;

		foreach ($langs as $lang) {
			$currentServiceLang = null;
			foreach ($serviceLangs as $serviceLang) {
				if ($serviceLang->lang_id == $lang->id) {
					$currentServiceLang = $serviceLang;
				}
			}

			if ($currentServiceLang == null) {
				$currentServiceLang = new \stdClass();
				$currentServiceLang->name = '';
				$currentServiceLang->lang_id = $lang->id;
			}
			$service->langs['l' . $lang->id] = $currentServiceLang;
		}

		return view('services.services.edit')
			->with('service', $service)
			->with('tags', $tags)
			->with('category_terms', $category_terms)
			->with('location_terms', $location_terms)
			->with('accessibility_terms', $accessibility_terms)
			->with('availability_terms', $availability_terms)
			->with('available_nationality_terms', $available_nationality_terms)
			->with('complaints_mechanism_terms', $complaints_mechanism_terms)
			->with('coverage_terms', $coverage_terms)
			->with('feedback_delay_terms', $feedback_delay_terms)
			->with('feedback_mechanism_terms', $feedback_mechanism_terms)
			->with('intake_criteria_terms', $intake_criteria_terms)
			->with('referral_method_terms', $referral_method_terms)
			->with('referral_next_step_terms', $referral_next_step_terms)
			->with('registration_type_terms', $registration_type_terms)
			->with('response_delay_terms', $response_delay_terms)
			->with('partners', $partners)
			->with('contacts', $contacts)
			->with('langs', $langs);
	}

	/**
	 * Update the specified Service in storage.
	 *
	 * @param  int              $id
	 * @param UpdateServiceRequest $request
	 *
	 * @return Response
	 */
	public function update($id, UpdateServiceRequest $request)
	{
		$input = $request->all();

		$input['start_date'] = date('Y-m-d', strtotime($input['start_date']));
		$input['end_date'] = date('Y-m-d', strtotime($input['end_date']));
		$input['published'] = (isset($input['published']) && $input['published'] == 'on') ? true : false;
		$input['backendonly'] = (isset($input['backendonly']) && $input['backendonly'] == 'on') ? true : false;
		$input['is_remote'] = (isset($input['is_remote']) && $input['is_remote'] == 'on') ? true : false;

		if (isset($input['publish_date']) && $input['publish_date'] != null && strlen($input['publish_date']) > 0) {
			$input['publish_date'] = date('Y-m-d', strtotime($input['publish_date']));
		} else {
			$input['publish_date'] = date('Y-m-d', strtotime($input['start_date']));
		}

		$service = $this->serviceRepository->find($id);

		if (empty($service)) {
			Flash::error(__('app.Service not found'));

			return redirect(route('services.services.index'));
		}

		$service->update($input);
		$service->touch();

		$service = $this->serviceRepository->saveLangs($input, $service, $this->termRepository);

		$allTermIds = $input['terms'];
		$termIds = [];
		$termTagTypes = [];
		foreach ($allTermIds as $allTermId) {
			if (intval($allTermId) > 0) {
				$termIds[] = intval($allTermId);

				$term = $this->termRepository->find($allTermId);
				if ($term->taxonomy_id == 12) {
					if (!in_array($term->tag_id, $termTagTypes)) {
						$termTagTypes[] = $term->tag_id;
					}
				}
			}
		}

		if (!empty($termTagTypes)) {
			if (in_array(3, $termTagTypes) || (in_array(1, $termTagTypes) && in_array(2, $termTagTypes))) {
				$service->tag_id = 3;
			} elseif (in_array(1, $termTagTypes)) {
				$service->tag_id = 1;
			} elseif (in_array(2, $termTagTypes)) {
				$service->tag_id = 2;
			}
		} else {
			$service->tag_id = 1;
		}

		$service->save();


		$this->serviceContactRepository->setServiceContacts($id, $input);
		$this->serviceTermRepository->setServiceTermIds($id, $termIds);
		$this->serviceCategoryRepository->setServiceCategoryTermIds($service->id, $termIds);
		$this->serviceHourRepository->updateOrInsert($id, $input);


		Flash::success(__('app.Service updated successfully'));
		Log::warning('Service', 'Update', 'Service updated successfully.');

		return redirect(route('services.services.index'));
	}

	/**
	 * Remove the specified Service from storage.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function destroy($id)
	{
		$service = $this->serviceRepository->find($id);

		if (empty($service)) {
			Flash::error(__('app.Service not found'));

			return redirect(route('services.services.index'));
		}

		$this->serviceRepository->delete($id);

		Flash::success(__('app.Service deleted successfully'));
		Log::warning('Service', 'Delete', 'Service deleted successfully.');

		return redirect(route('services.services.index'));
	}

	public function deleteMultiple()
	{
		//$category_terms = $this->termRepository->getRootCategoryTermsForSelect(true);
		$category_terms = $this->termRepository->getNestedCategories();
		$location_terms = $this->termRepository->getLocationTerms();
		$accessibility_terms = $this->termRepository->getTermsForSelect(1, null, true);
		$intake_criteria_terms = $this->termRepository->getTermsForSelect(8, null, true);
		$referral_method_terms = $this->termRepository->getTermsForSelect(14, null, true);

		$partners = $this->partnerRepository->getPartnersForSelect('en', true);

		$search = Request::get('search');
		$sid = Request::get('sid');
		$category = Request::get('category');
		$accessibility = Request::get('accessibility');
		$intake_criteria = Request::get('intake_criteria');
		$referral_method = Request::get('referral_method');
		$city = Request::get('city');
		$district = Request::get('district');
		$partner = Request::get('partner');
		$status = Request::get('status');

		$searchQuery = http_build_query(
			array(
				'sid' => $sid,
				'category' => $category,
				'accessibility' => $accessibility,
				'intake_criteria' => $intake_criteria,
				'referral_method' => $referral_method,
				'city' => $city,
				'district' => $district,
				'partner' => $partner,
				'status' => $status,
			)
		);

		$pageData = array(
			'search' => $search,
			'service_id' => $sid,
			'category_id' => $category,
			'partner_id' => $partner,
			'accessibility_id' => $accessibility,
			'intake_criteria_id' => $intake_criteria,
			'referral_method_id' => $referral_method,
			'city_id' => $city,
			'district_id' => $district,
			'status_id' => $status,
			'category_terms' => $category_terms,
			'location_terms' => $location_terms,
			'accessibility_terms' => $accessibility_terms,
			'intake_criteria_terms' => $intake_criteria_terms,
			'referral_method_terms' => $referral_method_terms,
			'partners' => $partners,
			'count' => 0,
			'searchQuery' => $searchQuery
		);

		if (isset($_POST) && Request::get('process')) {
			$list = $this->serviceRepository->getByCriteria($pageData);
			$count = count($list);

			if ($count > 0) {
				foreach ($list as $_service) {
					$_service->delete();
				}
			}

			return Response::json(array('status' => 1, 'count' => $count));
		}

		if ($search) {

			$query = $this->serviceRepository->getByCriteria($pageData);
			$count = count($query);

			$pageData['count'] = $count;
		}

		return view('services.services.delete-multiple', $pageData);
	}

	public function export(\Illuminate\Http\Request $request)
	{
		//$export = new \App\Exports\ServicesExport();
		$filePath = 'public/exports/';
		$fileName = 'services_' . date('Y_m_d_H_i_s') . '.xlsx';
		$fileStoreLocation = $filePath . $fileName;

		//(new \App\Exports\ServicesExport)->queue($fileStoreLocation);

		//return back()->withSuccess('Export started!');

		//$status = \Excel::store($export, $filePath . $fileName);
		//$status = \Excel::queue($export, $fileStoreLocation, $disk = null, $writerType = null, $diskOptions = []);
		//dd($status);
		//($export)->store($filePath . $fileName);


		//$fileName = 'services_import_template.xlsx';
		//return \Illuminate\Support\Facades\Storage::disk('local')

		//return \Excel::queue($export, $filePath, $disk = null, $writerType = null, $diskOptions = []);

		//return \Excel::download(new \App\Exports\ServicesExport, 'services.xlsx');
		(new  \App\Exports\ServicesExport())->queue($fileStoreLocation)->chain([
			//new NotifyUserOfExport($request->user(), $name),
		]);

		//(new  \App\Exports\ServicesExport())->queue('public/exports/' . $name);

		Flash::success('Services export will take some time. You will receive an email when it is ready to download.');
		return back();
	}
}
