<?php

namespace App\Imports;

use App\Models\Definitions\Taxonomy;
use App\Models\Services\Location;
use App\Models\Services\Service;
use App\Models\Services\ServiceHour;
use App\Models\Settings\Language;
use App\Repositories\Definitions\TermRepository;
use App\Repositories\Services\LocationRepository;
use App\Repositories\Services\ServiceCategoryRepository;
use App\Repositories\Services\ServiceContactRepository;
use App\Repositories\Services\ServiceHourRepository;
use App\Repositories\Services\ServiceRepository;
use App\Repositories\Services\ServiceTermRepository;
use App\Repositories\Settings\LanguageRepository;
use App\Repositories\Settings\PartnerRepository;
use Maatwebsite\Excel\Concerns\ToModel;

class Service1Import implements ToModel
{
	/** @var  ServiceRepository */
	private $serviceRepository;

	/** @var  PartnerRepository */
	private $partnerRepository;

	/** @var  ServiceHourRepository */
	private $serviceHourRepository;

	/** @var  ServiceTermRepository */
	private $serviceTermRepository;

	private $locationTerms;
	private $langs;
	private $partners;

	private $services = [];

	function __construct(
		ServiceRepository $serviceRepo,
		LocationRepository $locationRepo,
		TermRepository $termRepo,
		PartnerRepository $partnerRepo,
		LanguageRepository $langRepo,
		ServiceContactRepository $serviceContactRepo,
		ServiceHourRepository $serviceHourRepo,
		ServiceTermRepository $serviceTermRepo,
		ServiceCategoryRepository $serviceCategoryRepo
	) {
		$this->serviceRepository = $serviceRepo;
		$this->locationRepository = $locationRepo;
		$this->termRepository = $termRepo;
		$this->partnerRepository = $partnerRepo;
		$this->langRepository = $langRepo;
		$this->serviceContactRepository = $serviceContactRepo;
		$this->serviceHourRepository = $serviceHourRepo;
		$this->serviceTermRepository = $serviceTermRepo;
		$this->serviceCategoryRepository = $serviceCategoryRepo;


		$this->locationTerms = $this->termRepository->getLocationTerms();
		for ($i = 0; $i < count($this->locationTerms); $i++) {
			$this->locationTerms[$i][3] = $this->slugify($this->locationTerms[$i][2]);
		}
		$this->langs = $this->langRepository->all();
		$this->categoryTerms = $this->termRepository->getNestedCategories();
		$this->partners = $this->partnerRepository->getPartnersForSelect('en', true);

		$this->services = [];
	}

	/**
	 * @param array $row
	 *
	 * @return \Illuminate\Database\Eloquent\Model|null
	 */
	public function model(array $row)
	{
		set_time_limit(0);
		ini_set('memory_limit', '2GB');

		$service = $this->serviceRepository->create($row);

		$this->services[] = $service;

		/* $service = null;
		$location = null;

		try {
			$partnerId = 6439; // MoNE
			$termIds = [
				1736, //Education
				1737, //Education opportunities
				1740, //Provide formal education
				2092, //Support to formal education (EBA center)
			];

			$location = $this->getLocation($row, $partnerId, 'MoNE');
			$location = $this->locationRepository->importWithLangs($location);

			$row['location_city_id'] = $location->city_id;
			$row['location_district_id'] = $location->district_id;
			$row['terms'] = $termIds;

			$service = $this->getService($row, $partnerId, $location->id);
			$service = $this->serviceRepository->importWithLangs($row, $service, $this->termRepository);

			//$this->serviceContactRepository->setServiceContacts($service->id, $input);
			$this->serviceTermRepository->setServiceTermIds($service->id, $termIds);
			$this->serviceCategoryRepository->setServiceCategoryTermIds($service->id, $termIds);
			$this->serviceHourRepository->insert($service->id, $this->getServiceHours($row));

			if($location == null) {
				echo '<pre>';
				var_dump([$row[1], $row[2]]);
				echo '</pre>';
			}
		} catch (\Throwable $exception) {
			dd([$row, $service, $location, $exception]);
		} */
	}

	private function getServiceHours($row)
	{
		$hours = [];

		for ($i = 0; $i < 5; $i++) {
			$hour = new ServiceHour();
			$hour->day = $i;
			$hour->start_hour = 900;
			$hour->end_hour = 1800;

			array_push($hours, $hour);
		}

		for ($i = 5; $i < 7; $i++) {
			$hour = new ServiceHour();
			$hour->day = $i;
			$hour->start_hour = 1000;
			$hour->end_hour = 1800;

			array_push($hours, $hour);
		}

		return $hours;
	}

	private function getService($row, $partnerId, $locationId)
	{
		$startDate =  \DateTime::createFromFormat('d.m.Y', $row[18])->format('Y-m-d');
		$endDate =  \DateTime::createFromFormat('d.m.Y', $row[19])->format('Y-m-d');
		$now = date('Y-m-d');
		$name = 'Education' . ' : ' . $row[1] . ' : ' . $row[2];
		$name = mb_convert_case($name, MB_CASE_TITLE, "UTF-8");

		$service = new Service();
		$service->start_date = $startDate;
		$service->end_date = $endDate;
		$service->partner_id = $partnerId;
		$service->location_id = $locationId;
		$service->published = 1;
		$service->publish_date = $now;
		$service->backendonly = 0;
		$service->langs = [
			[
				'lang_id' =>  1,
				'name' => $name,
				'additional' => $row[22],
				'comments' => '',
				'phone' => $row[23],
				'link' => ''
			]
		];

		return $service;
	}

	private function getLocation($row, $partnerId, $partnerName)
	{
		$province = $this->slugify($row[1]);
		$district = $this->slugify($row[2]);


		$parts = explode(' ', $row[3]);
		$parts = array_splice($parts, 0, 2);
		$name = implode(' ', $parts);
		$name = $partnerName . ' - ' . $name;
		$name = mb_convert_case($name, MB_CASE_TITLE, "UTF-8");

		$provinceTerm = null;
		$districtTerm = null;

		foreach ($this->locationTerms as $locationTerm) {
			if ($locationTerm[1] === 0 && $locationTerm[3] === $province) {
				$provinceTerm = $locationTerm;
				break;
			}
		}

		if ($provinceTerm == null) return null;

		foreach ($this->locationTerms as $locationTerm) {
			if ($provinceTerm[0] == $locationTerm[1] && $locationTerm[3] == $district) {
				$districtTerm = $locationTerm;
				break;
			}
		}

		if ($districtTerm == null) return null;

		$location = new Location();
		$location->country_id = 1;
		$location->city_id = $provinceTerm[0];
		$location->district_id = $districtTerm[0];
		$location->partner_ids = '' . $partnerId;
		$location->latitude = number_format(floatval($row[16]), 8);
		$location->longitude = number_format(floatval($row[17]), 8);
		$location->created_by = 1;
		$location->updated_by = 1;
		$location->deleted_by = null;
		$location->langs = [
			[
				'lang_id' =>  1,
				'name' => $name,
				'address' => $row[3],
				'direction' => ''
			]
		];

		return $location;
	}

	private function slugify($name)
	{
		$text = str_replace(
			["ş", "Ş", "ı", "ü", "Ü", "ö", "Ö", "ç", "Ç", "ş", "Ş", "ı", "ğ", "Ğ", "İ", "ö", "Ö", "Ç", "ç", "ü", "Ü"],
			["s", "S", "i", "u", "U", "o", "O", "c", "C", "s", "S", "i", "g", "G", "I", "o", "O", "C", "c", "u", "U"],
			$name
		);

		$text = preg_replace('~[^\pL\d]+~u', '-', $text);
		$text = iconv('utf-8', 'utf-8//TRANSLIT', $text);

		$text = preg_replace('~[^-\w]+~', '', $text);

		$text = trim($text, '-');

		$text = preg_replace('~-+~', '-', $text);

		$text = strtolower($text);

		if (empty($text)) {
			return time() . uniqid();
		}

		return $text;
	}

	public function headings(): array
	{

		$headers = [
			'start_date' => 'Start Date',
			'end_date' => 'End Date',
			'partner_id' =>  'Organization',
			'categories' => 'Categories',
			'availability' => 'Availability',
			'accessibility' => 'Accessibility',
			'city' => 'City',
			'district' => 'District',
			'location_name' => 'Location Name',
			'address' => 'Location Address',
			'latitude' => 'Latitude',
			'longitude' => 'Longitude',
			'legal_documents_required' => 'Legal Documents Required',
			'nationality' => 'Nationality',
			'gender_age' => 'Gender & Age',
			'intake_criteria' => 'Intake Criteria',
			'coverage' => 'Coverage',
			'referral_method' => 'Referral Method',
			'immediate_next_step_after_referral' => 'Immediate Next Step After Referral',
			'response_delay_after_referral' => 'Response Delay After Referral',
			'feedback_mechanism' => 'Feedback Mechanism',
			'feedback_delay' => 'Feedback Delay',
			'complaints_mechanism' => 'Complaints Mechanism',
			'additional_details' => 'Additional Details',
			'comments' => 'Comments',
			'hotline_public_phone' => 'Hotline / Public Phone',
			'more_info_link' => 'More Info Link',

			/* 
		
		18 => string '' (length=18)
		19 => string '' (length=14)
		20 => string '' (length=20)
		21 => string '' (length=18)
		22 => string '' (length=8)
		23 => string '' (length=22)
		24 => string 'More Info Link' (length=14) */
		];
		return $headers;
		/* $default_lang_id = Language::defaultLang()->id;

		$this->taxonomies = Taxonomy::join('taxonomy_lang', function ($join) use ($default_lang_id) {
			$join->on('taxonomy_lang.taxonomy_id', '=', 'taxonomy.id')
				->where('taxonomy_lang.lang_id', '=', $default_lang_id);
		})->get();

		$headers = [
			'Region',
			'Province',
			'Latitude',
			'Longitude',
			'Start Date',
			'End Date',
			'Organization',
			'Categories',
		];

		foreach ($this->taxonomies as $taxonomy) {
			$headers[] = $taxonomy->name;
		}
		return $headers; */
	}

	public static function getCellTerms($name)
	{
		if ($name === 'Region') {
			$termRepo = new TermRepository(app());

			$locationTerms = $termRepo->getLocationTerms();
		}

		return $name;
	}
}
