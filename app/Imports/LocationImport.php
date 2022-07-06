<?php

namespace App\Imports;

use App\Models\Definitions\Taxonomy;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use App\Models\Definitions\Term;
use App\Models\Definitions\TermLang;
use App\Models\Settings\Language;

class LocationImport implements ToCollection, WithCustomCsvSettings
{
	private $taxonomy;

	public function __construct()
	{
		$id = 13;
		$this->taxonomy = Taxonomy::find($id);
	}

	/* public function rules(): array
	{
		return [
			'0' => 'required|string',
		];
	} */

	public function collection(Collection $rows)
	{
		$nestedLocations = [];

		foreach ($rows as $row) {

			$location1 = $row[0];
			$location2 = $row[1];
			$location3 = $row[2];
			$location4 = $row[3];
			$location5 = $row[4];

			if ($location1) {
				if (!in_array($location1, array_keys($nestedLocations)))
					$nestedLocations[$location1] = [];
			}

			if ($location2) {
				if (!in_array($location2, array_keys($nestedLocations[$location1])))
					$nestedLocations[$location1][$location2] = [];
			}

			if ($location3) {
				if (!in_array($location3, array_keys($nestedLocations[$location1][$location2])))
					$nestedLocations[$location1][$location2][$location3] = [];
			}

			if ($location4) {
				if (!in_array($location4, array_keys($nestedLocations[$location1][$location2][$location3])))
					$nestedLocations[$location1][$location2][$location3][$location4] = [];
			}

			if ($location5) {
				if (!in_array($location5, array_keys($nestedLocations[$location1][$location2][$location3][$location4])))
					$nestedLocations[$location1][$location2][$location3][$location4][$location5] = [];
			}
		}

		foreach ($nestedLocations as $name1 => $items1) {

			$id1 = $this->createTerm($name1);

			if (!empty($items1)) {
				foreach ($items1 as $name2 => $items2) {

					$id2 = $this->createTerm($name2, $id1);

					if (!empty($items2)) {
						foreach ($items2 as $name3 => $items3) {

							$id3 = $this->createTerm($name3, $id2);

							if (!empty($items3)) {
								foreach ($items3 as $name4 => $items4) {

									$id4 = $this->createTerm($name4, $id3);

									if (!empty($items4)) {
										foreach ($items4 as $name5 => $items5) {
											$id5 = $this->createTerm($name5, $id4);
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}

	private function createTerm($name, $parent_id = 0)
	{
		$model = new Term;
		$model->taxonomy_id = $this->taxonomy->id;
		$model->parent_id = $parent_id;
		$model->save();

		$lang_id = 1; // English

		$modelLang = new TermLang;
		$modelLang->term_id = $model->id;
		$modelLang->lang_id = $lang_id;
		$modelLang->slug 	= $this->slugify($lang_id, $model->id, $name);
		$modelLang->name 	= $name;
		$modelLang->save();

		return $model->id;
	}

	public function getCsvSettings(): array
	{
		return [
			'input_encoding' => 'UTF-8'
		];
	}

	private static function slugify($langId, $id, $name)
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

		$exists = TermLang::where('slug', $text)->where('lang_id', $langId)->where('term_id', '<>', $id)->get();

		if (count($exists) > 0) {
			return self::slugify($langId, $id, $text . '-1');
		}

		return $text;
	}
}
