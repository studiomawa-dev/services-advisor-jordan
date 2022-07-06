<?php

namespace App\Repositories\Services;

use App\Models\Services\Service;
use App\Repositories\BaseRepository;
use App\Models\Services\ServiceLang;
use App\Repositories\Definitions\TermRepository;
use Illuminate\Support\Facades\DB;
use App\Libraries\Clustering\Clustering;
use App\Models\Services\ServiceTerm;
use App\Models\Settings\Language;
use PDO;

/**
 * Class ServiceRepository
 * @package App\Repositories\Services
 * @version May 18, 2019, 11:02 am UTC
 */

class ServiceRepository extends BaseRepository
{
	/**
	 * @var array
	 */
	protected $fieldSearchable = [
		'tag_id',
		'start_date',
		'end_date',
		'partner_id',
		'published'
	];

	/**
	 * Return searchable fields
	 *
	 * @return array
	 */
	public function getFieldsSearchable()
	{
		return $this->fieldSearchable;
	}

	/**
	 * Configure the Model
	 **/
	public function model()
	{
		return Service::class;
	}

	public function get_with_langs($id)
	{
		$query = $this->model->newQuery();
		return $query
			->where('id', $id)
			->with('langs')
			->first();
	}

	public function getItem($id, $langCode)
	{
		$lang = Language::getLangByCode($langCode);
		$langId = $lang == null ? Language::defaultLang()->id : $lang->id;

		if (strpos($id, '-') > -1) {
			$idParts = explode('-', $id);
			$id = $idParts[0];
		}

		/* $sql = "SELECT
					S.tag_id, S.id, SL.name, SL.additional, SL.comments, SL.phone, SL.link,
					(SELECT GROUP_CONCAT(CONCAT_WS(',', SH.day, SH.start_hour, SH.end_hour) SEPARATOR ';') FROM service_hour SH WHERE SH.service_id = S.id) hours,
					IFNULL(LL.address,'-') AS  `address`
				FROM `service` S
				INNER JOIN `service_lang` SL ON SL.service_id = S.id AND SL.lang_id = $langId
				INNER JOIN `location_lang` LL ON LL.location_id = S.location_id AND SL.lang_id = $langId
				WHERE S.id = $id AND S.deleted_at IS NULL AND S.start_date <= DATE(NOW()) AND S.end_date >= DATE(NOW())
				LIMIT 1";

		$result = DB::select($sql); */

		$driver = DB::connection()->getPDO()->getAttribute(PDO::ATTR_DRIVER_NAME);

		$currentDate = date('Y-m-d H:i:s');

		$result = DB::table('service')
			->select(
				'service.tag_id',
				'service.id',
				'service_lang.name',
				'service_lang.additional',
				'service_lang.comments',
				'service_lang.phone',
				'service_lang.link',
				($driver === 'mysql'
					? DB::raw("(SELECT GROUP_CONCAT(CONCAT_WS(',', SH.day, SH.start_hour, SH.end_hour) SEPARATOR ';') FROM service_hour SH WHERE SH.service_id = service.id) AS hours")
					: DB::raw("(SELECT STRING_AGG(CONCAT_WS(',', SH.day, SH.start_hour, SH.end_hour), ';') FROM service_hour AS SH WHERE SH.service_id = service.id) AS hours")
				),
				DB::raw("ISNULL(' - ', location_lang.address) AS address")
			)
			->join('service_lang', 'service_lang.service_id', '=', 'service.id')
			->join('location_lang', 'location_lang.location_id', '=', 'service.location_id')
			->where('service_lang.lang_id', '=', $langId)
			->where('location_lang.lang_id', '=', $langId)
			->where('service.id', '=', $id)
			->whereNull('service.deleted_at')
			->whereDate('service.start_date', '<=', $currentDate)
			->whereDate('service.end_date', '>=', $currentDate)
			->limit(1)
			->get();
		/* if (count($result) > 0) {
			return $result[0];
		} */
		return $result;
	}

	public function getFull($id)
	{
		$query = $this->model->newQuery();

		$result = $query
			->where('id', $id)
			->with('langs')
			->with('terms')
			->with('terms.term.langs')
			->with('hours')
			->with('contacts')
			->with('location.langs')
			->with('partner.logo')
			->with('creator')
			->with('editor')
			->first();

		$termIds = [];

		if ($result != null) {
			foreach ($result->terms as $term) {
				array_push($termIds, $term->term_id);
			}

			$result->termIds = $termIds;
		}

		return $result;
	}

	public function getByCriteria($criteria)
	{
		$query = $this->model->newQuery();

		$service_id = isset($criteria['service_id']) ? $criteria['service_id'] : null;
		$category_id = isset($criteria['category_id']) ? $criteria['category_id'] : null;
		$partner_id = isset($criteria['partner_id']) ? $criteria['partner_id'] : null;
		$accessibility_id = isset($criteria['accessibility_id']) ? $criteria['accessibility_id'] : null;
		$intake_criteria_id = isset($criteria['intake_criteria_id']) ? $criteria['intake_criteria_id'] : null;
		$referral_method_id = isset($criteria['referral_method_id']) ? $criteria['referral_method_id'] : null;
		$city_id = isset($criteria['city_id']) ? $criteria['city_id'] : null;
		$district_id = isset($criteria['district_id']) ? $criteria['district_id'] : null;
		$status_id = isset($criteria['status_id']) ? $criteria['status_id'] : null;

		$query->where('deleted_at', null);

		if ($service_id != null && strlen($service_id) > 0) {
			if (is_numeric($service_id)) {
				$query->where('id', $service_id);
			}
		}

		if ($category_id != null && is_numeric($category_id)) {
			$query->whereHas('terms', function ($query) use ($category_id) {
				$query->where('term_id', $category_id);
			});
		}

		if ($partner_id != null && is_numeric($partner_id)) {
			$query->whereHas('partner', function ($query) use ($partner_id) {
				$query->where('partner_id', $partner_id);
			});
		}

		if ($accessibility_id != null && is_numeric($accessibility_id)) {
			$query->whereHas('terms', function ($query) use ($accessibility_id) {
				$query->where('term_id', $accessibility_id);
			});
		}

		if ($intake_criteria_id != null && is_numeric($intake_criteria_id)) {
			$query->whereHas('terms', function ($query) use ($intake_criteria_id) {
				$query->where('term_id', $intake_criteria_id);
			});
		}

		if ($referral_method_id != null && is_numeric($referral_method_id)) {
			$query->whereHas('terms', function ($query) use ($referral_method_id) {
				$query->where('term_id', $referral_method_id);
			});
		}

		if ($city_id != null && is_numeric($city_id)) {
			$query->whereHas('location', function ($query) use ($city_id) {
				$query->where('city_id', $city_id);
			});
		}

		if ($district_id != null && is_numeric($district_id)) {
			$query->whereHas('location', function ($query) use ($district_id) {
				$query->where('district_id', $district_id);
			});
		}

		if ($status_id != null && strlen($status_id) > 0) {
			if (is_numeric($status_id)) {
				if ($status_id == 1) { // Published
					$query->where('published', 1);
					$query->where('end_date', '>', date('Y-m-d h:i:s'));
					$query->where('publish_date', '<=', date('Y-m-d h:i:s'));
				} else if ($status_id == 2) { // Unpublished
					$query->where('published', 0);
				} else if ($status_id == 3) { // Scheduled
					$query->where('published', 1);
					$query->where('publish_date', '>', date('Y-m-d h:i:s'));
				} else if ($status_id == 4) { // Expired
					$query->where('published', 1);
					$query->where('end_date', '<', date('Y-m-d h:i:s'));
				}
			}
		}

		return $query->get();
	}

	public function getLastServices()
	{
		$query = $this->model->newQuery();

		$result = $query
			->with('langs')
			->with('terms.term.langs')
			->with('categories.term.langs')
			->with('partner.langs')
			->orderByDesc('id')
			->limit(10)
			->get();

		return $result;
	}

	public function getCount()
	{
		$query = $this->model->newQuery();

		$result = $query
			->where('published', 1)
			->count();

		return $result;
	}

	public function getCategoryCounts()
	{
		$default_lang_id = Language::defaultLang()->id;

		$result = DB::table('service')
			->select(DB::raw('COUNT(service_term.term_id) as "count"'), 'service_term.term_id', 'term_lang.name')
			->join('service_term', 'service_term.service_id', '=', 'service.id')
			->join('term', 'term.id', '=', 'service_term.term_id')
			->join('term_lang', 'term_lang.term_id', '=', 'term.id')
			->where('term.taxonomy_id', '=', 12)
			->where('term.parent_id', '=', 0)
			->where('lang_id', '=', $default_lang_id)
			->groupBy('service_term.term_id', 'term_lang.name')
			->orderBy(DB::raw('COUNT(service_term.term_id)'), 'desc')
			->limit(5)
			->get();

		return $result;
	}

	public function getCoordinates($rootCategories)
	{
		$driver = DB::connection()->getPDO()->getAttribute(PDO::ATTR_DRIVER_NAME);

		$currentDate = date('Y-m-d H:i:s');

		$result = DB::table('service')
			->select(
				'service.id',
				'service.partner_id AS pid',
				'service.is_remote AS r',
				'location.latitude AS lat',
				'location.longitude AS lng',
				'location.city_id AS cid',
				'location.district_id AS did',
				'service_category.category_term_id AS c',
				'service_category.category_no AS cn',
				DB::raw('(SELECT ' . ($driver === 'mysql' ? 'GROUP_CONCAT' : 'STRING_AGG') . "(service_term.term_id, ',')" . 'FROM service_term WHERE service_term.service_id = service.id) AS t')
			)
			->join('location', 'location.id', '=', 'service.location_id')
			->join('service_category', 'service_category.service_id', '=', 'service.id', 'inner')
			->where('service_category.deleted', '=', 0)
			->where('service.published', '=', 1)
			->where('service.backendonly', '<>', 1)
			->whereNull('service.deleted_at')
			->whereDate('service.start_date', '<=', $currentDate)
			->whereDate('service.end_date', '>=', $currentDate)
			->whereDate('service.publish_date', '<=', $currentDate)
			->get();

		return $result;
	}

	public function getCoordinatesByTag($tagCode)
	{
		$sql = "SELECT
					S.id, S.partner_id,
					L.latitude latitude, L.longitude longitude, L.city_id, L.district_id,
					(SELECT GROUP_CONCAT(STN.term_id) FROM `service_term` STN WHERE STN.service_id = S.id) terms
				FROM `service` S
				JOIN tag ON tag.id = S.tag_id
				INNER JOIN `location` L ON L.id = S.location_id
				INNER JOIN `service_category` SC ON SC.service_id = S.id AND SC.deleted = 0
				WHERE tag.code IN ('all', '$tagCode')
				AND S.published = 1 
				AND S.backendonly <> 1 
				AND S.deleted_at IS NULL 
				AND S.start_date <= DATE(NOW()) 
				AND S.end_date >= DATE(NOW()) 
				AND S.publish_date <= DATE(NOW())";

		$result = DB::select($sql);

		return $result;
	}


	public function getList($langCode, $page = 1)
	{
		$page = (int) $page;
		$lang = Language::getLangByCode($langCode);

		if (!$lang) {
			return  [];
		}

		if ($page <= 0) {
			$page = 1;
		}

		$langID = $lang->id;
		$url = \Illuminate\Support\Facades\URL::to('/');
		$currentUrl = \Illuminate\Support\Facades\URL::current();

		$baseUrl  = "$url/{$lang->code}/service";
		$mediaUrl = "$url/media";

		$countSQL = "SELECT count(*) as total_count
			  FROM `service` S
			  INNER JOIN `service_lang` SL ON SL.service_id = S.id AND SL.lang_id = $langID
			  JOIN `location` L ON L.id = S.location_id
			  INNER JOIN `location_lang` LL ON LL.location_id = L.id AND LL.lang_id = $langID
			  INNER JOIN `service_category` SC ON SC.service_id = S.id AND SC.deleted = 0
			  INNER JOIN `partner` P ON P.id = S.partner_id
			  INNER JOIN `partner_lang` PL ON PL.partner_id = P.id AND PL.lang_id = $langID
			  INNER JOIN `media` PM ON PM.id = P.logo_id
			  WHERE S.published = 1
				AND S.backendonly <> 1
				AND S.deleted_at IS NULL
				AND S.start_date <= DATE(NOW())
				AND S.end_date >= DATE(NOW())
				AND S.publish_date <= DATE(NOW())
				AND L.city_id = 877";

		$result = DB::selectOne($countSQL);

		$totalCount = $result->total_count;
		$itemsPerPage = 1000;

		$offset = ($page - 1) * $itemsPerPage;
		$totalPage = ceil($totalCount / $itemsPerPage);

		$nextPage = $currentUrl . '?lang=' . $lang->code . '&page=' . ($page + 1);
		if ($page + 1 > $totalPage)
			$nextPage = '';

		$sql = "SELECT
					S.tag_id as tag_id,
					T.code as tag_code,
					T.name as tag_name,
       				SL.name AS name,
					L.latitude as latitude, L.longitude as longitude,
       				LL.name AS location,
       				SL.additional AS additional, SL.phone as phone,
       				PL.name as partner,
       				PL.full_name AS partner_full_name,
       				PL.url AS partner_url,
       				(CASE WHEN PM.filename IS NOT NULL THEN CONCAT('$mediaUrl','/',PM.filename) ELSE null END )AS partner_logo,
       				CONCAT('$baseUrl','/',S.id) AS service_url,
              	    SC.category_term_id as cat_id
				FROM `service` S
				INNER JOIN `service_lang` SL ON SL.service_id = S.id AND SL.lang_id = $langID
				JOIN `location` L ON L.id = S.location_id
				JOIN `tag` T ON T.id = S.tag_id
				INNER JOIN `location_lang` LL ON LL.location_id = L.id AND LL.lang_id = $langID
				INNER JOIN `service_category` SC ON SC.service_id = S.id AND SC.deleted = 0
				INNER JOIN `partner` P ON P.id = S.partner_id
				INNER JOIN `partner_lang` PL ON PL.partner_id = P.id AND PL.lang_id = $langID
				INNER JOIN `media` PM ON PM.id = P.logo_id
				WHERE S.published = 1
				  AND S.backendonly <> 1
				  AND S.deleted_at IS NULL
				  AND S.start_date <= DATE(NOW())
				  AND S.end_date >= DATE(NOW())
				  AND S.publish_date <= DATE(NOW())
				  AND L.city_id = 877
				LIMIT $offset,$itemsPerPage";

		$result = DB::select($sql);

		return ['result' => $result, 'page' => $page, 'next_page' => $nextPage, 'count' => count($result), 'total_count' => $totalCount];
	}

	public function getClusters($bounds, $zoom, $rootCategories)
	{
		$query = $this->model->newQuery();

		$returnData = [];
		$result = $query
			->with('partner')
			->with('location')
			->with('terms')
			->whereHas('location', function ($query) use ($bounds) {
				$query->where('longitude', '>', (float) $bounds[0]);
				$query->where('longitude', '<', (float) $bounds[1]);
				$query->where('latitude', '>', (float) $bounds[2]);
				$query->where('latitude', '<', (float) $bounds[3]);
			})
			->get();

		if ($result != null && count($result) > 100 && $zoom < 10) {
			$services = $result->toArray();
			foreach ($services as &$service) {
				if ($service['location'] != null && $service['location']['latitude'] != null && $service['location']['longitude'] != null && $service['location']['latitude'] > 0 && $service['location']['longitude'] > 0) {
					$service['lat'] = $service['location']['latitude'];
					$service['lng'] = $service['location']['longitude'];
					$service['sid'] = $service['id'];
				}
			}

			$radius = 0.1;
			if ($zoom > 15) {
				$radius = 0.01;
			} else if ($zoom > 12) {
				$radius = 0.1;
			} else if ($zoom > 10) {
				$radius = 0.3;
			} else if ($zoom > 8) {
				$radius = 0.6;
			} else if ($zoom > 7) {
				$radius = 1;
			} else if ($zoom > 6) {
				$radius = 2;
			} else if ($zoom > 5) {
				$radius = 4;
			} else if ($zoom > 4) {
				$radius = 6;
			} else if ($zoom > 2) {
				$radius = 60;
			} else {
				$radius = 120;
			}

			$clusters = Clustering::getClusters($services, $radius);
			$returnData = array(
				'type' => 'cluster',
				'data' => $clusters
			);
		} else if ($result != null && count($result)) {
			$services = [];
			foreach ($result as $item) {
				if ($item->location != null) {
					$category = 0;
					$nationalities = [];
					foreach ($item->terms as $term) {
						foreach ($rootCategories as $key => $value) {
							if ($key == $term->term_id) {
								$category = $key;
								break;
							}
						}

						if ($term->taxonomy_id == 3) { //nationality
							array_push($nationalities, $term->term_id);
						}
					}
					array_push($services, [$item->id, $item->location->latitude, $item->location->longitude, $item->location->city_id, $item->location->district_id, $category, $item->partner->id, $nationalities]);
				}
			}

			$returnData = array(
				'type' => 'location',
				'data' => $services
			);
		}

		return $returnData;
	}

	public function getTerms()
	{
		$query = $this->model->newQuery();

		$services = [];
		$result = $query
			->with('terms')
			->get();

		if ($result != null && count($result)) {
			foreach ($result as $item) {
				if ($item->location != null) {
					$termIds = [];

					foreach ($item->terms as $term) {
						array_push($termIds, $term->term_id);
					}

					array_push($services, [$item->id, $termIds]);
				}
			}
		}

		return $services;
	}

	public function importWithLangs($input, $model, TermRepository $termRepository)
	{
		$langs = $model->langs;
		unset($model->langs);

		$model->save();

		if (!($model->id > 0)) {
			return $model;
		}

		if ($langs != null && count($langs) > 0) {
			foreach ($langs as $lang) {
				$langId = $lang['lang_id'];

				$serviceLang = new ServiceLang();
				$serviceLang->service_id = $model->id;
				$serviceLang->lang_id = $langId;
				$serviceLang->name = $lang['name'];
				$serviceLang->slug = self::slugify($langId, $model->id, $serviceLang['name']);
				$serviceLang->additional = ($lang['additional'] == null) ? '' : $lang['additional'];
				$serviceLang->comments = ($lang['comments'] == null) ? '' : $lang['comments'];
				$serviceLang->phone = ($lang['phone'] == null) ? '' : $lang['phone'];
				$serviceLang->link = ($lang['link'] == null) ? '' : $lang['link'];
				$serviceLang->save();
			}
		}

		return $model;
	}

	public function saveLangs($input, $model, TermRepository $termRepository)
	{
		$langs = $input['langs'];

		if ($langs != null && count($langs) > 0) {
			foreach ($langs as $key => $lang) {
				$langId = intval(str_replace('l', '', $key));
				$serviceLang = [];
				$serviceLang['service_id'] = $model->id;
				$serviceLang['lang_id'] = $langId;
				$serviceLang['name'] = self::getServiceName($input, $langId, $termRepository);
				$serviceLang['slug'] = self::slugify($langId, $model->id, $serviceLang['name']);
				$serviceLang['additional'] = ($lang['additional'] == null) ? '' : $lang['additional'];
				$serviceLang['comments'] = ($lang['comments'] == null) ? '' : $lang['comments'];
				$serviceLang['phone'] = ($lang['phone'] == null) ? '' : $lang['phone'];
				$serviceLang['link'] = ($lang['link'] == null) ? '' : $lang['link'];

				$item = ServiceLang::updateOrCreate(['service_id' => $model->id, 'lang_id' => $langId], $serviceLang);
			}
		}

		return $model;
	}

	public function setTagIdByCategories()
	{
	}

	public function getRecents($count)
	{
		$query = $this->model->newQuery();
		$created = $query
			->whereNotNull('created_at')
			->whereNotNull('created_by')
			->with('langs')
			->with('creator')
			->orderBy('created_at', 'desc')
			->limit($count)
			->get();

		$query = $this->model->newQuery();
		$updated = $query
			->whereNotNull('updated_at')
			->whereNotNull('updated_by')
			->with('langs')
			->with('editor')
			->orderBy('updated_at', 'desc')
			->limit($count)
			->get();

		return $created->merge($updated)->sortByDesc('updated_at')->sortByDesc('created_at');
	}

	public function getLastUpdateTime()
	{
		$query = $this->model->newQuery();
		return $query->max('updated_at');
	}

	public function getServicesByLocation($locationId)
	{
		$query = $this->model->newQuery();
		return $query->where('location_id', $locationId)->whereNull('deleted_at')->get();
	}
	private static function getServiceName($input, $langId, TermRepository $termRepository)
	{
		$categoryTerms = $termRepository->getRootCategoryTerms();

		$category = self::getRootCategoryName($input, 1, $categoryTerms);
		$location = self::getLocationName($input, 1, $termRepository);

		if ($category != null && $location != null) {
			return $category . ' : ' . $location;
		}

		return '';
	}

	private static function getRootCategoryName($input, $langId, $categoryTerms)
	{
		$inputTerms = $input['terms'];
		if (isset($inputTerms) && is_array($inputTerms) && count($inputTerms) > 0) {
			foreach ($inputTerms as $inputTerm) {
				if (is_numeric($inputTerm)) {
					foreach ($categoryTerms as $categoryTerm) {
						if ($categoryTerm->id == intval($inputTerm)) {
							$categoryTermLangs = $categoryTerm->langs;
							if ($categoryTermLangs != null && count($categoryTermLangs) > 0) {
								foreach ($categoryTermLangs as $categoryTermLang) {
									if ($categoryTermLang->lang_id == $langId) {
										return $categoryTermLang->name;
									}
								}
							}
						}
					}
				}
			}
		}

		return null;
	}

	private static function getLocationName($input, $langId, TermRepository $termRepository)
	{
		$cityName = null;
		$districtName = null;

		$cityId = $input['location_city_id'];
		$districtId = $input['location_district_id'];

		if (is_numeric($cityId) && is_numeric($districtId)) {
			$city = $termRepository->findWithLangs($cityId);
			$district = $termRepository->findWithLangs($districtId);

			if ($city->langs != null && count($city->langs) > 0) {
				foreach ($city->langs as $cityLang) {
					if ($cityLang->lang_id == $langId) {
						$cityName = $cityLang->name;
						break;
					}
				}
				if ($cityName == null) {
					$cityName = $city->langs[0]->name;
				}
			}

			if ($district->langs != null && count($district->langs) > 0) {
				foreach ($district->langs as $districtLang) {
					if ($districtLang->lang_id == $langId) {
						$districtName = $districtLang->name;
						break;
					}
				}
				if ($districtName == null) {
					$districtName = $district->langs[0]->name;
				}
			}
		}

		if ($cityName != null && $districtName != null) {
			return $cityName . ' : ' . $districtName;
		}

		return null;
	}

	private static function slugify($langId, $serviceId, $name)
	{
		$text = preg_replace('~[^\pL\d]+~u', '-', $name);
		$text = iconv('utf-8', 'utf-8//TRANSLIT', $text);

		$text = preg_replace('~[^-\w]+~', '', $text);

		$text = trim($text, '-');

		$text = preg_replace('~-+~', '-', $text);

		$text = strtolower($text);

		if (empty($text)) {
			return time() . uniqid();
		}

		$count = ServiceLang::where('slug', 'LIKE', $text . '%')->where('lang_id', $langId)->where('service_id', '<>', $serviceId)->count();

		if ($count > 0) {
			$text = $text . '-' . ($count + 1);
		}

		return $text;
	}
}
