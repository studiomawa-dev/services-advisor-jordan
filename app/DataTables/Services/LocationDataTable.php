<?php

namespace App\DataTables\Services;

use Request;
use App\Models\Services\Location;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use App\Models\Settings\Language;

class LocationDataTable extends DataTable
{
	/**
	 * Build DataTable class.
	 *
	 * @param mixed $query Results from query() method.
	 * @return \Yajra\DataTables\DataTableAbstract
	 */
	public function dataTable($query)
	{
		$default_lang_id = Language::defaultLang()->id;

		$src = Request::get('src');
		$city = Request::get('city');
		$district = Request::get('district');
		$sub_district = Request::get('sub_district');
		$neighborhood = Request::get('neighborhood');

		$query
			->with([
				'langs' =>
				function ($query) use ($default_lang_id) {
					$query->where('lang_id', $default_lang_id)->orWhereNotNull('name');
				}
			])
			->with('city')
			->with([
				'city.langs' =>
				function ($query) use ($default_lang_id) {
					$query->where('lang_id', $default_lang_id);
				}
			])
			->with('district')
			->with([
				'district.langs' =>
				function ($query) use ($default_lang_id) {
					$query->where('lang_id', $default_lang_id);
				}
			])
			->with('sub_district')
			->with([
				'sub_district.langs' =>
				function ($query) use ($default_lang_id) {
					$query->where('lang_id', $default_lang_id);
				}
			])
			->with('neighborhood')
			->with([
				'neighborhood.langs' =>
				function ($query) use ($default_lang_id) {
					$query->where('lang_id', $default_lang_id);
				}
			]);

		if ($city > 0) {
			$query->where('city_id', $city);
		}

		if ($district > 0) {
			$query->where('district_id', $district);
		}

		if ($sub_district > 0) {
			$query->where('sub_district_id', $sub_district);
		}

		if ($neighborhood > 0) {
			$query->where('neighborhood_id', $neighborhood);
		}

		if (isset($src) && $src != null && strlen($src) > 0) {
			if (is_numeric($src)) {
				$query->where('id', $src);
			} else {
				$query->whereHas('langs', function ($query) use ($src) {
					$query->where('name', 'like', '%' . $src . '%');
				});
			}
		}

		$dataTable = new EloquentDataTable($query);

		return $dataTable->addColumn('action', 'services.locations.datatables_actions');
	}

	/**
	 * Get query source of dataTable.
	 *
	 * @param \App\Models\Location $model
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function query(Location $model)
	{
		return $model->newQuery();
	}

	/**
	 * Optional method if you want to use html builder.
	 *
	 * @return \Yajra\DataTables\Html\Builder
	 */
	public function html()
	{
		return $this->builder()
			->columns($this->getColumns())
			->minifiedAjax()
			->addAction(['width' => '120px', 'printable' => false])
			->parameters([
				'dom'       => 'Bfrtip',
				'stateSave' => true,
				'order'     => [[0, 'desc']],
				'buttons'   => [
					['extend' => 'create', 'className' => 'btn btn-default btn-sm no-corner',],
					['extend' => 'export', 'className' => 'btn btn-default btn-sm no-corner',],
					['extend' => 'print', 'className' => 'btn btn-default btn-sm no-corner',],
					['extend' => 'reset', 'className' => 'btn btn-default btn-sm no-corner',],
					['extend' => 'reload', 'className' => 'btn btn-default btn-sm no-corner',],
				],
			]);
	}

	/**
	 * Get columns.
	 *
	 * @return array
	 */
	protected function getColumns()
	{
		return [
			[
				"name" => "id",
				"title" => __("app.Id"),
				"data" => "id"
			],
			[
				"name" => "name",
				"title" => __("app.Name"),
				"data" => "langs.0.name",
				"orderable" => false
			],
			[
				"name" => "city_id",
				"title" => __("app.City"),
				"data" => "city.langs[0].name",
				"orderable" => false
			],
			[
				"name" => "district_id",
				"title" => __("app.District"),
				"data" => "district.langs[0].name",
				"orderable" => false
			],
			[
				"name" => "sub_district_id",
				"title" => __("app.Sub District"),
				"data" => "function(item) { return item.sub_district ? item.sub_district.langs[0].name : ''; }",
				"orderable" => false
			],
			[
				"name" => "neighborhood_id",
				"title" => __("app.Neighborhood"),
				"data" => "function(item) { return item.neighborhood ? item.neighborhood.langs[0].name : ''; }",
				"orderable" => false
			]
		];
	}

	/**
	 * Get filename for export.
	 *
	 * @return string
	 */
	protected function filename()
	{
		return 'locationsdatatable_' . time();
	}
}
