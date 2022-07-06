<?php

namespace App\Imports;

use App\Models\Settings\Language;
use App\Models\Settings\Partner;
use App\Models\Settings\PartnerLang;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;


class PartnerImport implements ToCollection, WithHeadingRow, WithCustomCsvSettings
{
	public function rules(): array
	{
		return [
			'0' => 'required|string',
		];
	}

	public function collection(Collection $rows)
	{
		$languages = Language::all();

		$importedCount = 0;
		$items = array();

		foreach ($rows as $row) {

			$name = $row['name'];

			if ($name) {

				$exists = PartnerLang::where('name', $name)->get();

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

			foreach ($items as $item) {

				$name = $item['name'];

				$partner = new Partner;
				$partner->save();

				foreach ($languages as $language) {

					$partnerLang = new PartnerLang;
					$partnerLang->partner_id = $partner->id;
					$partnerLang->lang_id = $language->id;
					$partnerLang->slug = $this->slugify($language->id, $partner->id, $name);
					$partnerLang->name = $name;
					$partnerLang->save();
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
