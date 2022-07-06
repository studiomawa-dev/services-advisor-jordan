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

class TermsImport implements ToCollection, WithHeadingRow, WithCustomCsvSettings
{
	private $taxonomy;

	public function __construct($id)
	{
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
		$languages = Language::all();

		$importedCount = 0;
		$items = array();

		foreach ($rows as $row) {

			$name = $row['name'];

			if ($name) {

				$exists = TermLang::join('term', 'term.id', '=', 'term_lang.term_id')->where('name', $name)->where('term.taxonomy_id', $this->taxonomy->id)->exists();

				if (!$exists) {
					$items[] = array(
						'name' => $name
					);
				}
			}
		}

		$items = collect($items)->sortBy('name')->reverse()->toArray();

		if (!empty($items)) {

			$items = collect($items)->sortBy('name')->toArray();

			$orderIndex = 0;
			foreach ($items as $item) {

				$name = $item['name'];

				$model = new Term;
				$model->taxonomy_id = $this->taxonomy->id;
				$model->parent_id = 0;
				$model->order = $orderIndex++;
				$model->save();

				foreach ($languages as $language) {

					$modelLang = new TermLang;
					$modelLang->term_id = $model->id;
					$modelLang->lang_id = $language->id;
					$modelLang->slug 	= $this->slugify($language->id, $model->id, $name);
					$modelLang->name 	= $name;
					$modelLang->save();
				}

				$importedCount++;
			}
		}

		echo $importedCount;
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
