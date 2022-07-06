<?php

namespace App\Repositories\Definitions;

use App\Models\Definitions\Taxonomy;
use App\Models\Definitions\Term;
use App\Models\Definitions\TermLang;
use App\Repositories\BaseRepository;
use App\Models\Settings\Language;
use App\Models\Settings\Tag;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

/**
 * Class TermRepository
 * @package App\Repositories\Definitions
 * @version May 11, 2019, 10:41 am UTC
 */

class TermRepository extends BaseRepository
{
	/**
	 * @var array
	 */
	protected $fieldSearchable = [
		'taxonomy_id',
		'order',
		'deleted'
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
		return Term::class;
	}

	public function getAll($langCode)
	{
		$lang = Language::getLangByCode($langCode);
		$langId = $lang == null ? Language::defaultLang()->id : $lang->id;

		$tmpQuery1 = DB::table('term_lang')->select('name')->where('term_id', '=', 'term.id')->where('deleted', '=', 0)->limit(1);
		$tmpSQL1 = str_replace_array('?', $tmpQuery1->getBindings(), $tmpQuery1->toSql());

		$tmpQuery2 = DB::table('term_lang')->select('slug')->where('term_id', '=', 'term.id')->where('deleted', '=', 0)->limit(1);
		$tmpSQL2 = str_replace_array('?', $tmpQuery2->getBindings(), $tmpQuery2->toSql());

		$result = DB::table('term')
			->select(
				'term.id',
				'term.taxonomy_id AS tid',
				DB::raw('term.parent_id AS pid'),
				DB::raw('ISNULL(term_lang.name, (' . $tmpSQL1 . ')) AS name'),
				DB::raw('ISNULL(term_lang.slug, (' . $tmpSQL2 . ')) AS slug')
			)
			->join('term_lang', 'term_lang.term_id', '=', 'term.id')
			->where('term_lang.deleted', '=', 0)
			->where('term_lang.lang_id', '=', $langId)
			->where('term.deleted', '=', 0)
			->get();

		return $result;
	}

	public function getAllByTag($tagCode, $langCode)
	{
		$tagCodes = array($tagCode, 'all');
		$sql = "SELECT T.id FROM tag T WHERE code IN ('" . (implode("','", $tagCodes)) . "')";

		$tagList = DB::select($sql);
		$tagIds = [];
		foreach ($tagList as $tag) {
			$tagIds[] = $tag->id;
		}

		$lang = Language::getLangByCode($langCode);
		$langId = $lang == null ? Language::defaultLang()->id : $lang->id;
		$sql = "SELECT T.id, T.taxonomy_id AS tid, TAX.key AS tkey, T.parent_id AS pid, TL.slug,
					IFNULL(TL.name,(SELECT NAME FROM term_lang WHERE term_id=T.id AND deleted=0 LIMIT 1)) AS `name` 
				FROM term T
				JOIN taxonomy TAX ON (TAX.id = T.taxonomy_id)
				LEFT JOIN term_lang TL ON T.id = TL.term_id AND TL.deleted=0 AND TL.lang_id=$langId
				WHERE T.tag_id IN (" . (implode(',', $tagIds)) . ") AND T.deleted=0";

		$terms = DB::select($sql);

		$data = [];

		$taxonomySql = "SELECT * FROM taxonomy WHERE deleted=0";
		$taxonomies = DB::select($taxonomySql);

		foreach ($taxonomies as $taxonomy) {
			$data[$taxonomy->key] = [];
		}

		foreach ($terms as $term) {
			$data[$term->tkey][] = [
				'id' => $term->id,
				'parent' => $term->pid,
				'slug' => $term->slug,
				'name' => $term->name
			];
		}

		return $data;
	}

	public function getParentCategories($tagCode, $langCode)
	{
		$taxonomyID = 12;
		$tagCodes = array($tagCode, 'all');
		$sql = "SELECT T.id FROM tag T WHERE code IN ('" . (implode("','", $tagCodes)) . "')";

		$tagList = DB::select($sql);
		$tagIds = [];
		foreach ($tagList as $tag) {
			$tagIds[] = $tag->id;
		}

		$lang = Language::getLangByCode($langCode);
		$langId = $lang == null ? Language::defaultLang()->id : $lang->id;
		$sql = "SELECT T.id, T.taxonomy_id AS tid, TAX.key AS tkey, T.parent_id AS pid, TL.slug, T.color,
					IFNULL(TL.name,(SELECT NAME FROM term_lang WHERE term_id=T.id AND deleted=0 LIMIT 1)) AS `name`
				FROM term T
				JOIN taxonomy TAX ON (TAX.id = T.taxonomy_id)
				LEFT JOIN term_lang TL ON T.id = TL.term_id AND TL.deleted=0 AND TL.lang_id=$langId
				WHERE TAX.id = $taxonomyID
				AND T.parent_id = 0 
				AND T.deleted=0 " .
			(!empty($tagIds) ? "AND T.tag_id IN (" . (implode(',', $tagIds)) . ")" : '');

		$terms = DB::select($sql);

		$data = [];

		foreach ($terms as $term) {
			$data[] = [
				'id' => $term->id,
				'slug' => $term->slug,
				'name' => $term->name,
				'color' => $term->color
			];
		}

		return $data;
	}

	public function getListTree($tagCode, $langCode)
	{
		/* $items = $this->getAllByTag($tagCode, $langCode);

		foreach($items){

			
		} */
	}

	public function getByTaxonomy($taxonomy_id, $langId = null, $tag_ids = null)
	{
		$langId = $langId == null ? Language::defaultLang()->id : $langId;

		$query = Term::where(['taxonomy_id' => $taxonomy_id, 'term.deleted' => 0])
			->select(['term.*', 'tag.name as tag_name'])
			->leftJoin('term_lang as langs', function ($join) use ($langId) {
				$join->on('langs.term_id', '=', 'term.id');
				$join->where(['langs.lang_id' => $langId]);
			})
			->leftJoin('tag as tag', function ($join) use ($tag_ids) {
				$join->on('tag.id', '=', 'term.tag_id');
			})
			->orderBy('langs.name', 'asc');

		if ($tag_ids) {
			if ($taxonomy_id == 12) {
				$tag_ids[] = 3;
				$query->whereIn('term.tag_id', $tag_ids);
			} else {
				$tag_ids[] = 3;
				$query->whereIn('term.tag_id', $tag_ids);
			}
		}

		$terms = $query->get();

		foreach ($terms as $term) {

			if (count($term->langs) > 1) {
				for ($i = count($term->langs) - 1; $i >= 0; $i--) {
					if ($term->langs[$i]->lang_id == $langId) {
						$term->langs[0] = $term->langs[$i];
					}
				}
			}

			if (!isset($term->langs) || count($term->langs) == 0) {
				$termLang = new \stdClass();
				$termLang->term_id = $term->id;
				$termLang->lang_id = $langId;
				$termLang->name = "Undefined";
				$termLang->slug = "undefined";
				$term->langs[0] = $termLang;
			}
		}



		return $terms;
	}


	public function findWithLangs($id)
	{
		return Term::where('id', $id)
			->with('langs')
			->orderBy('order')
			->firstOrFail();
	}

	public function createWithLangs($input)
	{
		$model = $this->create($input);

		Term::setParentTagType($model->id);

		$langs = $input['langs'];

		$termLangs = [];
		if ($langs != null && count($langs) >  0) {

			foreach ($langs as $key => $lang) {
				if ($lang['name'] != null) {
					$termLang = [];
					$termLang['term_id'] = $model->id;
					$termLang['lang_id'] = intval(str_replace('l', '', $key));
					$termLang['name'] = $lang['name'];
					$termLangs[] = $termLang;
				}
			}
		}

		if (empty($termLangs))
			return false;

		foreach ($termLangs as $termLang) {
			TermLang::create($termLang);
		}

		return $model;
	}

	public function updateWithLangs($input, $id)
	{
		$model = $this->update($input, $id);

		Term::setParentTagType($id);

		$langs = $input['langs'];

		if ($langs != null && count($langs) > 0) {
			foreach ($langs as $key => $lang) {
				$langId = intval(str_replace('l', '', $key));
				$termLang = [];
				$termLang['term_id'] = $model->id;
				$termLang['lang_id'] = $langId;
				$termLang['name'] = ($lang['name'] == null) ? '' : $lang['name'];
				$item = TermLang::updateOrCreate(['term_id' => $model->id, 'lang_id' => $langId], $termLang);
			}
		}

		return $model;
	}

	public function getLocationTerms($langCode = null)
	{
		$langId = $langCode == null ? Language::defaultLang()->id : Language::getLangByCode($langCode)->id;
		$all_location_terms = $this->getByTaxonomy(13, $langId);
		$location_terms = [];

		foreach ($all_location_terms as $all_term) {
			$term = new \stdClass();
			$term->id = $all_term['id'];
			$term->parent_id = $all_term['parent_id'];

			foreach ($all_term->langs as $lang) {
				if ($langId == $lang->lang_id)
					$term->name = $lang['name'];
			}

			if (!isset($term->name)) {
				$term->name = $all_term['id'] . '';
			}

			array_push($location_terms, [$term->id, $term->parent_id, $term->name]);
		}

		return $location_terms;
	}

	public function getTermsForSelect($taxonomy_id, $langCode = null, $placeholder = false)
	{
		$langId = $langCode == null ? Language::defaultLang()->id : Language::getLangByCode($langCode)->id;

		$all_terms = $this->getByTaxonomy($taxonomy_id, $langId);
		$terms = [];

		if ($placeholder) {
			$terms[null] = __('app.Select Term');
		}

		foreach ($all_terms as $all_term) {
			$terms[$all_term->id] = $all_term['langs'][0]->name;
		}

		return $terms;
	}

	public function getRootLocationTermsForSelect($placeholder = false)
	{
		$allLocations = $this->getLocationTerms();
		$locations = [];
		if ($placeholder) {
			$locations[null] = __('app.Select City');
		}
		foreach ($allLocations as $location) {
			if ($location[1] == null || $location[1] == 0) {
				$locations[$location[0]] = $location[2];
			}
		}
		return $locations;
	}

	public function getTerms($taxonomyId, $langCode = null, $tagIds = null)
	{
		$langId = $langCode == null ? Language::defaultLang()->id : Language::getLangByCode($langCode)->id;

		$all_category_terms = $this->getByTaxonomy($taxonomyId, $langId, $tagIds);
		$category_terms = [];
		foreach ($all_category_terms as $all_term) {
			$term = new \stdClass();
			$term->taxonomy_id = $all_term['taxonomy_id'];
			$term->id = $all_term['id'];
			$term->parent_id = $all_term['parent_id'];
			$term->name = $all_term['id'] . '';
			$term->slug = $all_term['id'] . '';

			foreach ($all_term['langs'] as $lang) {
				if ($lang->lang_id == $langId) {
					$term->name = $lang->name;
					$term->slug = $lang->slug;
				}
			}

			array_push($category_terms, $term);
		}

		return $category_terms;
	}

	public function getCategoryTerms($langCode = null, $tagIds = null)
	{
		$langId = $langCode == null ? Language::defaultLang()->id : Language::getLangByCode($langCode)->id;

		$all_category_terms = $this->getByTaxonomy(12, $langId, $tagIds);
		$category_terms = [];
		foreach ($all_category_terms as $all_term) {
			$term = new \stdClass();
			$term->id = $all_term['id'];
			$term->parent_id = $all_term['parent_id'];
			$term->name = $all_term['id'] . '';
			$term->slug = $all_term['id'] . '';

			foreach ($all_term['langs'] as $lang) {
				if ($lang->lang_id == $langId) {
					$term->name = $lang->name;
					$term->slug = $lang->slug;
				}
			}

			array_push($category_terms, $term);
		}

		return $category_terms;
	}



	public function getRootCategoryTerms() // All langs
	{
		$terms = Term::where(['taxonomy_id' => 12, 'deleted' => 0])
			->with('langs')
			->orderBy('order')
			->get();


		$categories = [];
		foreach ($terms as $category) {
			if ($category->parent_id == null || $category->parent_id == 0) {
				$categories[] = $category;
			}
		}

		return $categories;
	}

	public function getNationalityTerms($langCode = null)
	{
		$langId = $langCode == null ? Language::defaultLang()->id : Language::getLangByCode($langCode)->id;

		$all_category_terms = $this->getByTaxonomy(3, $langId);
		$category_terms = [];

		foreach ($all_category_terms as $all_term) {
			$term = new \stdClass();
			$term->id = $all_term['id'];
			$term->parent_id = $all_term['parent_id'];
			$term->name = $all_term['id'] . '';
			$term->slug = $all_term['id'] . '';

			foreach ($all_term['langs'] as $lang) {
				if ($lang['lang_id'] == $langId) {
					$term->name = $lang['name'];
					$term->slug = $lang['slug'];
				}
			}

			array_push($category_terms, $term);
		}

		return $category_terms;
	}



	public function getRootCategoryTermsForSelect($placeholder = false)
	{
		$allCategories = $this->getCategoryTerms();
		$categories = [];
		if ($placeholder) {
			$categories[null] = __('app.Select Category');
		}
		foreach ($allCategories as $category) {
			if ($category->parent_id == null || $category->parent_id == 0) {
				$categories[$category->id] = $category->name;
			}
		}
		return $categories;
	}

	public function getNestedCategories($langCode = null)
	{
		function getNestedCategoriesOptions($parent, $menu)
		{
			$html = "";
			if (isset($menu['parents'][$parent])) {
				foreach ($menu['parents'][$parent] as $itemId) {
					if (!isset($menu['parents'][$itemId])) {
						$html .= '<option value="' . $itemId . '">' . $menu['items'][$itemId]['langs'][0]['name'] . '</option>';
					}
					if (isset($menu['parents'][$itemId])) {
						$html .= '<option value="' . $itemId . '">' . $menu['items'][$itemId]['langs'][0]['name'] . '<optgroup>';
						$html .= getNestedCategoriesOptions($itemId, $menu);
					}
				}
				$html .= '</optgroup>';
			}
			return $html;
		}

		$langId = $langCode == null ? Language::defaultLang()->id : Language::getLangByCode($langCode)->id;

		$allCategories = $this->getByTaxonomy(12, $langId)->toArray();

		$categories = array(
			'items' => array(),
			'parents' => array()
		);

		foreach ($allCategories as $category) {
			$categories['items'][$category['id']] = $category;
			$categories['parents'][$category['parent_id']][] = $category['id'];
		}

		return substr(getNestedCategoriesOptions(0, $categories), 0, -10);
	}

	public function getNestedTerms($taxonomyId)
	{
		$temp_terms = $this->getByTaxonomy($taxonomyId)->toArray();

		$terms = array();

		function buildTree(array $elements, $parentId = 0, $level = 0)
		{
			$branch = array();

			foreach ($elements as $element) {
				if ($element['parent_id'] == $parentId) {
					if (!isset($element['level'])) $element['level'] = $level;

					$children = buildTree($elements, $element['id'], $element['level'] + 1);
					if ($children) {
						$element['children'] = $children;
					}
					$branch[] = $element;
				}
			}

			return $branch;
		}

		$terms = buildTree($temp_terms);

		return $terms;
	}

	private function checkSlugs($terms, $langId)
	{
		$this->slugify(0, 0, "ĞÜŞİÖÇIğüşiöç");
		foreach ($terms as $term) {

			if (count($term->langs) > 0) {
				$termName = "term";

				foreach ($term->langs as $item) {
					if ($item->lang_id == 1) {
						$termName = $item->name;
					}
				}

				foreach ($term->langs as $item) {
					if ($item->slug == null || strlen($item) == 0) {
						$item->slug = self::slugify($item->lang_id, $item->id, $item->lang_id > 3 ? $termName : $item->name);
						$item->save();
					}
				}
			}
		}
	}

	private static function slugify($langId, $termId, $name)
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

		$exists = TermLang::where('slug', $text)->where('lang_id', $langId)->where('term_id', '<>', $termId)->get();

		if (count($exists) > 0) {
			return self::slugify($langId, $termId, $text . '-1');
		}

		return $text;
	}
}
