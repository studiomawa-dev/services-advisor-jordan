<?php

namespace App\Exports;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Definitions\Taxonomy;
use App\Models\Services\Service;
use App\Models\Settings\Language;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomQuerySize;
use Throwable;

class ServicesExport implements FromCollection, WithHeadings, ShouldQueue, WithCustomQuerySize
{
	use Exportable;

	public $taxonomies;

	/**
	 * @return \Illuminate\Support\Querry
	 */
	public function collection()
	{
		return $this->getQuery()->get();
	}

	public function headings(): array
	{
		$default_lang_id = Language::defaultLang()->id;

		$this->taxonomies = Taxonomy::join('taxonomy_lang', function ($join) use ($default_lang_id) {
			$join->on('taxonomy_lang.taxonomy_id', '=', 'taxonomy.id')
				->where('taxonomy_lang.lang_id', '=', $default_lang_id);
		})->get();

		$headers = [
			'ID',
			'Start Date',
			'End Date',
			'Region',
			'Province',
			'Latitude',
			'Longitude',
			'Organization',
			'Categories',
		];

		foreach ($this->taxonomies as $taxonomy) {
			$headers[] = $taxonomy->name;
		}
		return $headers;
	}

	public function querySize(): int
	{
		$size = 5000;
		return $size;
	}


	public function failed(Throwable $exception): void
	{
		$file = fopen('./debug.log', 'w+');
		fwrite($file, var_export($exception, true));
		fclose($file);
		//var_dump($exception);
	}

	private function getQuery()
	{
		$default_lang_id = Language::defaultLang()->id;
		$userTagIds = Auth::user()->tagIds();

		$this->taxonomies = Taxonomy::join('taxonomy_lang', function ($join) use ($default_lang_id) {
			$join->on('taxonomy_lang.taxonomy_id', '=', 'taxonomy.id')
				->where('taxonomy_lang.lang_id', '=', $default_lang_id);
		})->get();

		$querySelect = array(
			'service.id',
			'start_date',
			'end_date',
			'location_city.name as city',
			'location_province.name as province',
			'location.latitude',
			'location.longitude',
			'partner.name as partner',
			DB::raw('GROUP_CONCAT(DISTINCT(category_term.name) SEPARATOR ";") as category')
		);

		$query = Service::select(
			$querySelect
		);

		$query
			->join('partner_lang as partner', function ($join) use ($default_lang_id) {
				$join->on('partner.partner_id', '=', 'service.partner_id')
					->where('partner.lang_id', '=', $default_lang_id);
			})
			->join('location as location', function ($join) use ($default_lang_id) {
				$join->on('location.id', '=', 'service.location_id');
			})
			->join('term_lang as location_city', function ($join) use ($default_lang_id) {
				$join->on('location_city.term_id', '=', 'location.city_id')
					->where('location_city.lang_id', '=', $default_lang_id);
			})
			->join('term_lang as location_province', function ($join) use ($default_lang_id) {
				$join->on('location_province.term_id', '=', 'location.district_id')
					->where('location_province.lang_id', '=', $default_lang_id);
			})
			->join('service_category as service_category', function ($join) use ($default_lang_id) {
				$join->on('service_category.service_id', '=', 'service.id');
			})
			->join('term_lang as category_term', function ($join) use ($default_lang_id) {
				$join->on('category_term.term_id', '=', 'service_category.category_term_id')
					->where('category_term.lang_id', '=', $default_lang_id);
			});

		if (isset($userTagIds) && $userTagIds != null && is_array($userTagIds)) {
			if (!empty($userTagIds)) {
				$query->whereIn('tag_id', $userTagIds);
			}
		}

		$query->groupBy('service.id');
		$query->orderBy('service.id', 'DESC');

		$query->take(self::querySize());

		return $query;
	}
}
