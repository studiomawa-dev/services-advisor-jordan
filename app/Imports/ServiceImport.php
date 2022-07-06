<?php

namespace App\Imports;

use App\Models\Definitions\Taxonomy;
use App\Models\Definitions\Term;
use App\Models\Definitions\TermLang;
use App\Models\Services\Location;
use App\Models\Services\LocationLang;
use App\Models\Services\Service;
use App\Models\Services\ServiceCategory;
use App\Models\Services\ServiceHour;
use App\Models\Services\ServiceLang;
use App\Models\Services\ServiceLocation;
use App\Models\Services\ServiceTerm;
use App\Models\Settings\Language;
use App\Models\Settings\Partner;
use App\Models\Settings\PartnerLang;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ServiceImport implements ToModel, WithHeadingRow
{
	private $languages;
	private $categoryTaxonomyID = 12;
	private $locationTaxonomyID = 13;

	public function __construct()
	{
		set_time_limit(0);
		ini_set('memory_limit', -1);

		$this->languages = Language::all();
	}

	/**
	 * @param array $row
	 *
	 * @return \Illuminate\Database\Eloquent\Model|null
	 */
	public function model(array $row)
	{
		$nid = $row['nid'];
		$name = $row['title'];
		$organization = $row['organization'];
		$start_date = $row['start_date'];
		$end_date = $row['end_date'];

		$categories = array(
			$row['service_category_1'],
			$row['service_category_2'],
			$row['service_category_3'],
			$row['service_category_4'],
			$row['service_category_5'],
			$row['service_category_6'],
		);

		$locations = array(
			$row['service_location_1'],
			$row['service_location_2'],
			$row['service_location_3'],
			$row['service_location_4'],
			$row['service_location_5'],
		);

		$gps_coordinates = $row['gps_coordinates'];
		$coordinates = [];
		preg_match('/\((.+)\)/', $gps_coordinates, $gps_coordinate);
		if (isset($gps_coordinate[1])) {
			$coordinates = explode(' ', $gps_coordinate[1]);
		}

		$hours = $row['hours'];
		$accessibility = $row['accessibility'];
		$coverage = $row['coverage'];
		$public_address_location_details = $row['public_address_location_details'];
		$legal_documents_required = $row['legal_documents_required'];
		$nationality = $row['nationality'];
		$in_take_criteria = $row['in_take_criteria'];
		$referral_method = $row['referral_method'];
		$referral_contact_name = $row['referral_contact_name'];
		$referral_contact_phone = $row['referral_contact_phone'];
		$referral_contact_email = $row['referral_contact_email'];
		$immediate_next_step_after_referral = $row['immediate_next_step_after_referral'];
		$response_delay_after_referral = $row['response_delay_after_referral'];
		$feedback_mechanism = $row['feedback_mechanism'];
		$feedback_delay = $row['feedback_delay'];
		$complaints_mechanism = $row['complaints_mechanism'];
		$partner_type = $row['partner_type'];


		$terms = $this->getTerms(
			[
				'service_coverage' => $coverage,
				'service_registration_type' => $legal_documents_required,
				'service_accessibility' => $accessibility,
				'service_available_nationality' => $nationality,
				'service_intake_criteria' => $in_take_criteria,
				'service_referral_method' => $referral_method,
				'service_referral_next_step' => $immediate_next_step_after_referral,
				'service_response_delay' => $response_delay_after_referral,
				'service_feedback_mechanism' => $feedback_mechanism,
				'service_feedback_delay' => $feedback_delay,
				'service_complaints_mechanism' => $complaints_mechanism
			],

		);

		if (isset($categories[0]) && $categories[0]) {

			if (isset($locations[3]) && $locations[3] && isset($coordinates[0]) && $coordinates[1]) {

				$serviceName = $categories[0] . ':' . $locations[0];
				$locationName = $categories[0] . ':' . $locations[0] . ':' . $locations[1];

				//$serviceName  = implode(':', $categories) . ':' . implode(':', $locations);

				$partner  = $this->getPartner($organization);
				$location = $this->getLocation($locations, $coordinates, $locationName, $public_address_location_details);
				$service  = $this->createService($nid, $serviceName, $start_date, $end_date, $partner, $location, $nid);

				$this->setCategories($service, $categories);
				$this->setLocations($service, $location);
				$this->setTerms($service, $terms);
				$this->setHours($service, $hours);

				echo 'NID: ' . $nid . ' - ID' . $service->id . '<br/>';
			}
		} else {
			echo 'NID: ' . $nid . ' - NO CAT <br/>';
		}
	}

	private function getPartner($name)
	{
		$langModel = PartnerLang::where('name', $name)->first();

		if ($langModel === null) {
			$model = new Partner;
			$model->save();

			foreach ($this->languages as $language) {
				$langModel = new PartnerLang;
				$langModel->partner_id = $model->id;
				$langModel->lang_id = $language->id;
				$langModel->slug = $this->slugify($name);
				$langModel->name = $name;
				$langModel->save();
			}
		} else {
			$model = $langModel->partner;
		}

		return $model;
	}

	private function createService($id, $name, $start_date, $end_date, $partnerModel, $locationModel, $comment = null)
	{
		$startDate = date('Y-m-d', strtotime($start_date));
		$endDate = date('Y-m-d', strtotime($end_date));

		//		$service = Service::where(['start_date' => $startDate, 'end_date' => $endDate, 'partner_id' => $partnerModel->id, 'location_id' => $locationModel->id])->first();
		$service = Service::where(['import_id' => $id])->first();

		if ($service) return $service;

		$now = date('Y-m-d');

		$service = new Service;
		$service->start_date = $startDate;
		$service->end_date = $endDate;
		$service->partner_id = $partnerModel->id;
		$service->location_id = $locationModel->id;
		$service->published = strtotime($endDate) >= strtotime($now) ? 1 : 0;
		$service->publish_date = $now;
		$service->backendonly = 0;
		$service->import_id = $id;
		$service->save();

		foreach ($this->languages as $language) {
			$serviceLang = new ServiceLang;
			$serviceLang->service_id = $service->id;
			$serviceLang->lang_id  =  $language->id;
			$serviceLang->name = $name . ':' . $service->id;
			$serviceLang->additional = null;
			$serviceLang->comments = $comment;
			$serviceLang->phone = null;
			$serviceLang->link = null;
			$serviceLang->save();
		}

		return $service;
	}

	private function setCategories($service, $categories)
	{
		$categories = array_filter($categories);

		$parentId = 0;

		foreach ($categories as $categoryName) {

			$catno = ServiceCategory::where(['service_id' => $service->id])->count();

			$categoryTerm = $this->getCategoryTerm($categoryName, $parentId);

			if ($categoryTerm) {
				$parentId = $categoryTerm->id;

				if ($categoryTerm->parent_id == 0) {
					if (!ServiceCategory::where(['service_id' => $service->id, 'category_term_id' => $categoryTerm->id])->exists()) {
						$model = new ServiceCategory;
						$model->service_id = $service->id;
						$model->category_term_id = $categoryTerm->id;
						$model->category_no = $catno + 1;
						$model->save();
					}
				}

				$params = ['service_id' => $service->id, 'term_id' => $categoryTerm->id, 'taxonomy_id' => $this->categoryTaxonomyID];
				if (!ServiceTerm::where($params)->exists()) {
					ServiceTerm::insert($params);
				}
			}
		}
	}

	private function setLocations($service, $location)
	{
		if (!ServiceLocation::where(['service_id' => $service->id, 'location_id' => $location->id])->exists()) {
			$model = new ServiceLocation;
			$model->service_id = $service->id;
			$model->location_id = $location->id;
			$model->save();
		}
	}

	private function getLocation($locations, $coordinates, $name, $address = null)
	{
		$latitude = $coordinates[1];
		$longitude = $coordinates[0];

		$city 		  = null;
		$district 	  = null;
		$subDistrict  = null;
		$neighborhood = null;

		if (isset($locations[0])) {
			$city = $this->getTerm($this->locationTaxonomyID, $locations[0]);
		}

		if (isset($locations[1])) {
			$district = $this->getTerm($this->locationTaxonomyID, $locations[1]);
		}

		if (isset($locations[2])) {
			$subDistrict = $this->getTerm($this->locationTaxonomyID, $locations[2]);
		}

		if (isset($locations[3])) {
			$neighborhood = $this->getTerm($this->locationTaxonomyID, $locations[3]);
		}

		$city_id = $city ? $city->id : null;
		$district_id = $district ? $district->id : null;
		$subdistrict_id = $subDistrict ? $subDistrict->id : null;
		$neighborhood_id = $neighborhood ? $neighborhood->id : null;

		$latitude_coordinate = number_format(floatval($latitude), 8);
		$longitude_coordinate = number_format(floatval($longitude), 8);

		$location = Location::where(
			[
				'city_id' => $city_id, 'district_id' => $district_id, 'sub_district_id' => $subdistrict_id, 'neighborhood_id' => $neighborhood_id,
				'latitude' => $latitude_coordinate, 'longitude' => $longitude_coordinate
			]
		)->first();

		if ($location) {
			return $location;
		}

		$location = new Location();
		$location->country_id 		= null;
		$location->city_id 			= $city_id;
		$location->district_id  	= $district_id;
		$location->sub_district_id  = $subdistrict_id;
		$location->neighborhood_id  = $neighborhood_id;
		$location->partner_ids  	= null;
		$location->latitude 		= $latitude_coordinate;
		$location->longitude 		= $longitude_coordinate;
		$location->created_by 		= 1;
		$location->updated_by 		= 1;
		$location->deleted_by 		= null;
		$location->save();

		foreach ($this->languages as $language) {
			$locationLang = new LocationLang;
			$locationLang->location_id = $location->id;
			$locationLang->lang_id = $language->id;
			$locationLang->name = $name . ':' . $location->id;
			$locationLang->address = $address;
			$locationLang->direction = null;
			$locationLang->save();
		}

		return $location;
	}

	public function getTerm($taxonomyId, $name)
	{
		$term = Term::select(['term.id', 'term.parent_id', 'term_lang.name'])
			->join('term_lang', 'term_lang.term_id', '=', 'term.id')
			->where(['taxonomy_id' => $taxonomyId, 'name' => $name])
			->first();
		return $term;
	}

	public function getCategoryTerm($name, $parendId = 0)
	{
		$termQuery = Term::select(['term.id', 'term.parent_id', 'term_lang.name', 'term.parent_id'])
			->join('term_lang', 'term_lang.term_id', '=', 'term.id')
			->where(['taxonomy_id' => $this->categoryTaxonomyID, 'parent_id' => $parendId, 'name' => $name]);

		/* if (!empty($parentNames)) {

			foreach ($parentNames as $parentName) {
				$parentTerm = Term::select(['term.id', 'term.parent_id', 'term_lang.name'])
					->join('term_lang', 'term_lang.term_id', '=', 'term.id')
					->innerJoin('term as parent_term', 'parent_term.id', '=', 'term.parent_id')
					->innerJoin('term_lang as parent_term_lang', 'parent_term_lang.term_id', '=', 'parent_term.id')
					->where(['taxonomy_id' => $this->categoryTaxonomyID, 'parent_term_lang.name' => $parentName])
					->first();
			}

			$termQuery->where(['term.parent_id' => $parentTerm->id]);
		} */

		$term = $termQuery->first();
		return $term;
	}

	private function getTerms($data)
	{
		$terms = [];

		foreach ($data as $taxKey => $item) {
			if ($item) {
				$items = explode(',', $item);
				$taxonomy = Taxonomy::where(['key' => $taxKey])->first();

				foreach ($items as $itemName) {

					$itemName = trim($itemName);

					$term = $this->getTerm($taxonomy->id, trim($itemName));
					if ($term) {
						$terms[$taxonomy->id][] = $term->id;
					}
				}
			}
		}

		return $terms;
	}

	private function setTerms($service, $terms)
	{
		foreach ($terms as $taxonomy_id => $termItems) {
			foreach ($termItems as $term_id) {

				$params = ['service_id' => $service->id, 'term_id' => $term_id, 'taxonomy_id' => $taxonomy_id];

				if (!ServiceTerm::where($params)->exists()) {
					ServiceTerm::insert($params);
				}
			}
		}
	}

	private function setContacts($service, $contacts)
	{
	}

	private function setHours($service, $hoursData)
	{
		$hourItems = explode(',', $hoursData);

		$dayIndexies = [
			'sunday',
			'monday',
			'tuesday',
			'wednesday',
			'thursday',
			'friday',
			'saturday'
		];

		$items = [];

		if ($hourItems) {
			foreach ($hourItems as $hourItem) {
				$hourItem = str_replace(' ', '', $hourItem);

				$day   = substr($hourItem, 0, strpos($hourItem, ':'));
				$hour  = substr($hourItem, strpos($hourItem, ':') + 1, strlen($hourItem));

				$hours = explode('-', $hour);

				if (strpos($day, '-') !== false) {
					$days = explode('-', $day);
					$startIndex = array_search(strtolower(trim($days[0])), $dayIndexies);
					$endIndex   = array_search(strtolower(trim($days[1])), $dayIndexies);

					if ($startIndex !== false && $endIndex !== false) {
						for ($ind = $startIndex; $ind <= $endIndex; $ind++) {
							if (isset($hours[0]) && isset($hours[1]) && $hours[0] != 'Closed')
								$items[$ind] = ['start' => str_replace(':', '', $hours[0]), 'end' => str_replace(':', '', $hours[1])];
						}
					}
				} else {

					if (isset($hours[0]) && isset($hours[1]) && $hours[0] != 'Closed') {
						$ind = array_search(strtolower(trim($day)), $dayIndexies);
						$items[$ind] = ['start' => str_replace(':', '', $hours[0]), 'end' => str_replace(':', '', $hours[1])];
					}
				}
			}
		}

		if (!empty($items)) {

			foreach ($items as $dayIndex => $value) {

				$params = ['service_id' => $service->id, 'day' => $dayIndex, 'start_hour' => $value['start'], 'end_hour' => $value['end']];

				if (!ServiceHour::where($params)->exists()) {
					ServiceHour::insert($params);
				}
			}
		}
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
}
