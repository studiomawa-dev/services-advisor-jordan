<?php

namespace App\DataTables\Settings;

use Request;
use App\Models\Settings\Partner;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use App\Models\Settings\Language;
use Illuminate\Support\Facades\DB;

class PartnerDataTable extends DataTable
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

		$query
			->with('tag')
			->with([
				'langs' =>
				function ($query) use ($default_lang_id) {
					$query->where('lang_id', $default_lang_id);
				}
			])->with('logo');

		if (isset($src) && $src != null && strlen($src) > 0) {
			if (is_numeric($src)) {
				$query->where('id', $src);
			} else {
				$query->whereHas('langs', function ($query) use ($src) {
					$query
						->where('name', 'like', '%' . $src . '%')
						->orWhere('full_name', 'like', '%' . $src . '%');
				});
			}
		}

		$dataTable = new EloquentDataTable($query);

		$dataTable->editColumn('id', 'settings.partners.datatables_logo');

		return $dataTable
			->addColumn('tag', function ($data) {
				return $data->tag ? $data->tag->name : '';
			})
			->addColumn('action', 'settings.partners.datatables_actions')
			->rawColumns(['id', 'action']);
	}

	/**
	 * Get query source of dataTable.
	 *
	 * @param \App\Models\Partner $model
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function query(Partner $model)
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
				"title" => "#",
				"data" => "id"
			],
			[
				"name" => "name",
				"title" => __("app.Name"),
				"data" => "langs[0].name",
				"orderable" => false
			],
			[
				"name" => "tag",
				"title" => __('app.Tag'),
				"data" => "tag"
			],
		];
	}

	/**
	 * Get filename for export.
	 *
	 * @return string
	 */
	protected function filename()
	{
		return 'partnersdatatable_' . time();
	}
}
