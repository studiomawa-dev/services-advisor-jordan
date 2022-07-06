<?php

namespace App\Repositories\Settings;

use App\Models\Settings\Partner;
use App\Repositories\BaseRepository;
use App\Models\Settings\PartnerLang;
use App\Models\Settings\Language;
use Illuminate\Support\Facades\DB;

/**
 * Class PartnerRepository
 * @package App\Repositories\Settings
 * @version May 26, 2019, 1:05 pm UTC
 */

class PartnerRepository extends BaseRepository
{
	/**
	 * @var array
	 */
	protected $fieldSearchable = [
		'type_id',
		'logo_id'
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
		return Partner::class;
	}

	public function getCount()
	{
		$query = $this->model->newQuery();

		$result = $query
			->whereNull('deleted_at')
			->count();

		return $result;
	}

	public function getAll($langCode = null)
	{
		$lang = null;
		if ($langCode)
			$lang = Language::getLangByCode($langCode);

		$langId = $lang == null ? Language::defaultLang()->id : $lang->id;

		$result = DB::table('partner')
			->select(
				'partner.id',
				'partner_lang.name',
				DB::raw('partner_lang.full_name AS fname'),
				DB::raw('partner_lang.description AS "desc"'),
				DB::raw('term_lang.id AS tid'),
				DB::raw('term_lang.name AS tname'),
				DB::raw('media.filename AS logo')
			)
			->join('partner_lang', 'partner_lang.partner_id', '=', 'partner.id')
			->join('term_lang', 'term_lang.term_id', '=', 'partner.type_id')
			->join('media', 'media.id', '=', 'partner.logo_id')
			->where('partner_lang.lang_id', '=', $langId)
			->where('term_lang.lang_id', '=', $langId)
			->whereNull('partner.deleted_at')
			->get();

		return $result;
	}

	public function getAllByTag($tagCode = null, $langCode = null)
	{
		$tagCodes = array($tagCode, 'all');
		$sql = "SELECT T.id FROM tag T WHERE code IN ('" . (implode("','", $tagCodes)) . "')";

		$tagList = DB::select($sql);
		$tagIds = [];
		foreach ($tagList as $tag) {
			$tagIds[] = $tag->id;
		}

		$lang = null;
		if ($langCode)
			$lang = Language::getLangByCode($langCode);

		$langId = $lang == null ? Language::defaultLang()->id : $lang->id;
		$sql = "SELECT
					P.id, PL.name, PL.full_name full_name, PL.slug, PL.url, PL.description `desc`,
					TL.id term_id, TL.name term_name,
					CONCAT('media/',M.filename) logo
				FROM `partner` P

				LEFT JOIN  `partner_lang` PL ON PL.partner_id = P.id AND PL.lang_id = $langId
				LEFT JOIN `term_lang` TL ON TL.term_id = P.type_id AND TL.lang_id = $langId
				LEFT JOIN `media` M ON M.id = P.logo_id
				WHERE P.tag_id IN (" . (implode(',', $tagIds)) . ") AND P.deleted_at IS NULL ";

		$result = DB::select($sql);

		return $result;
	}

	public function getPartnersForSelect($langCode = null, $placeholder = false)
	{
		$lang = null;
		if ($langCode)
			$lang = Language::getLangByCode($langCode);

		$langId = $lang == null ? Language::defaultLang()->id : $lang->id;

		$all_partners = $this->model()::whereNull('deleted_at')
			->with('langs')
			->get();

		$partners = [];
		$partner_list = [];

		if ($placeholder) {
			$partners[null] = __('app.Select Organization');
		}
		foreach ($all_partners as $partner) {

			if ($partner->langs != null && count($partner->langs) > 0) {
				if ($langId != null) {
					foreach ($partner->langs as $partnerLang) {
						if ($partnerLang->lang_id == $langId) {
							$partner_list[] = [
								"id" => $partner->id . '',
								"name" => $partnerLang->name
							];
						}
					}

					if (!isset($partners[$partner->id . '']) || $partners[$partner->id . ''] == null) {
						$partner_list[] = [
							"id" => $partner->id . '',
							"name" => $partner->langs[0]->name
						];
					}
				} else {
					$partner_list[] = [
						"id" => $partner->id . '',
						"name" => $partner->langs[0]->name
					];
				}
			}
		}

		if (count($partner_list) > 0) {
			usort($partner_list, function ($a, $b) {
				return strcmp($a['name'], $b['name']);
			});

			foreach ($partner_list as $partner) {
				$partners[$partner['id']] = $partner['name'];
			}
		}

		return $partners;
	}

	public function getWithLogo($id)
	{
		return $this->model()::with('logo')->with('langs')->find($id);
	}

	public function getFull($id)
	{
		return $this->model()::with('logo')->with('langs')->with('type.langs')->find($id);
	}

	public function saveLangs($input, $model)
	{
		$langs = $input['langs'];

		if ($langs != null && is_array($langs) && count($langs) > 0) {
			foreach ($langs as $key => $lang) {
				$langId = intval(str_replace('l', '', $key));
				$partnerLang = [];
				$partnerLang['name'] = ($lang['name'] == null) ? '' : $lang['name'];
				$partnerLang['full_name'] = $lang['full_name'];
				$partnerLang['url'] = $lang['url'];
				$partnerLang['description'] = $lang['description'];
				$partnerLang['slug'] = self::slugify($langId, $model->id, $lang['name']);

				$item = PartnerLang::updateOrCreate(['partner_id' => $model->id, 'lang_id' => $langId], $partnerLang);
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

	private static function slugify($langId, $partnerId, $name)
	{
		$text = preg_replace('~[^\pL\d]+~u', '-', $name);
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

		$text = preg_replace('~[^-\w]+~', '', $text);

		$text = trim($text, '-');

		$text = preg_replace('~-+~', '-', $text);

		$text = strtolower($text);

		if (empty($text)) {
			return time() . uniqid();
		}

		$exists = PartnerLang::where('slug', $text)->where('lang_id', $langId)->where('partner_id', '<>', $partnerId)->get();

		if (count($exists) > 0) {
			return self::slugify($langId, $partnerId, $text . '-1');
		}

		return $text;
	}
}
