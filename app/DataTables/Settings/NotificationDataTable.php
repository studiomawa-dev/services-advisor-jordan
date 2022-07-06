<?php

namespace App\DataTables\Settings;

use Request;
use App\Models\Settings\Notification;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use App\Models\Settings\Language;
use Illuminate\Support\Facades\DB;

class NotificationDataTable extends DataTable
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
			->with([
				'langs' =>
				function ($query) use ($default_lang_id) {
					$query->where('lang_id', $default_lang_id);
				}
			]);

		if (isset($src) && $src != null && strlen($src) > 0) {
			if (is_numeric($src)) {
				$query->where('id', $src);
			} else {
				$query->whereHas('langs', function ($query) use ($src) {
					$query
						->where('title', 'like', '%' . $src . '%')
						->orWhere('message', 'like', '%' . $src . '%');
				});
			}
		}

		$dataTable = new EloquentDataTable($query);

		return $dataTable
			->addColumn('action', 'settings.notifications.datatables_actions')
			->rawColumns(['id', 'action']);
	}

	/**
	 * Get query source of dataTable.
	 *
	 * @param \App\Models\Notification $model
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function query(Notification $model)
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
				"name" => "title",
				"title" => "Title",
				"data" => "langs[0].title",
				"orderable"=> false
			],
			[
				"name" => "sending_date",
				"title" => "Sending Date",
				"data" => "sending_date",
				"orderable"=> false
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
		return 'notificationsdatatable_' . time();
	}
}
