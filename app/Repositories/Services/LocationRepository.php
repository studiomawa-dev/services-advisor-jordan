<?php

namespace App\Repositories\Services;

use App\Models\Services\Location;
use App\Repositories\BaseRepository;
use App\Models\Settings\Language;
use App\Models\Services\LocationLang;
use App\Repositories\Settings\LanguageRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class LocationRepository
 * @package App\Repositories\Services
 * @version May 20, 2019, 10:35 pm UTC
 */

class LocationRepository extends BaseRepository
{
	/**
	 * @var array
	 */
	protected $fieldSearchable = [
		'country_id',
		'city_id',
		'district_id',
		'sub_district_id',
		'neighborhood_id',
		'latitude',
		'longitude'
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
		return Location::class;
	}

	public function findOrCreate($city_id, $district_id, $sub_district_id, $neighborhood_id, $latitude, $longitude, $name, $address)
	{
		$location = $this->model
			->where('city_id', '=', $city_id)
			->where('district_id', '=', $district_id)
			->where('sub_district_id', '=', $sub_district_id)
			->where('neighborhood_id', '=', $neighborhood_id)
			->where('latitude', '=', $latitude)
			->where('longitude', '=', $longitude)
			->first();
		if ($location) return $location;

		$location = new \stdClass();
		$location->city_id = $city_id;
		$location->district_id = $district_id;
		$location->sub_district_id = $sub_district_id;
		$location->neighborhood_id = $neighborhood_id;
		$location->latitude = $latitude;
		$location->longitude = $longitude;
		$location->partner_ids = implode(',', \Illuminate\Support\Facades\Auth::user()->partnerIds());

		$location = $this->create($location);

		$langs = [];

		foreach (Language::all() as $lang) {

			$locationLang = [];
			$locationLang['name'] = $name;
			$locationLang['address'] = $address;
			$locationLang['direction'] = '';

			$langs[$lang->id] = $locationLang;
		}

		$item = $this->saveLangs(['langs' => $langs], $location);

		return $location;
	}

	public function saveLangs($input, $model)
	{
		$langs = $input['langs'];

		if ($langs != null && count($langs) > 0) {
			foreach ($langs as $key => $lang) {
				$langId = intval(str_replace('l', '', $key));
				$locationLang = [];
				$locationLang['location_id'] = $model->id;
				$locationLang['lang_id'] = $langId;
				$locationLang['name'] = ($lang['name'] == null) ? '' : $lang['name'];;
				$locationLang['slug'] = self::slugify($langId, $model->id, $lang['name']);
				$locationLang['address'] = ($lang['address'] == null) ? '' : $lang['address'];
				$locationLang['direction'] = ($lang['direction'] == null) ? '' : $lang['direction'];
				$item = LocationLang::updateOrCreate(['location_id' => $model->id, 'lang_id' => $langId], $locationLang);
			}
		}

		return $model;
	}

	public function importWithLangs($model)
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

				$locationLang = new LocationLang();
				$locationLang->location_id = $model->id;
				$locationLang->lang_id = $langId;
				$locationLang->name = ($lang['name'] == null) ? '' : $lang['name'];;
				$locationLang->slug = self::slugify($langId, $model->id, $lang['name']);
				$locationLang->address = ($lang['address'] == null) ? '' : $lang['address'];
				$locationLang->direction = ($lang['direction'] == null) ? '' : $lang['direction'];
				$locationLang->save();
			}
		}

		return $model;
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

	public function getCount()
	{
		$query = $this->model->newQuery();

		$result = $query
			->whereNull('deleted_at')
			->count();

		return $result;
	}

	private static function slugify($langId, $locationId, $name)
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

		$count = LocationLang::where('slug', 'LIKE', $text . '%')->where('lang_id', $langId)->where('location_id', '<>', $locationId)->count();

		if ($count > 0) {
			$text = $text . '-' . ($count + 1);
		}

		return $text;
	}
}
