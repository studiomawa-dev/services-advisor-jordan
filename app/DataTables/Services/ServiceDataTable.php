<?php

namespace App\DataTables\Services;

use Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Services\Service;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use App\Models\Settings\Language;
use Illuminate\Support\Facades\DB;

class ServiceDataTable extends DataTable
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
		$userTagIds = Auth::user()->tagIds();

		$tag = Request::get('tag');
		$sid = Request::get('sid');
		$category = Request::get('category');
		$accessibility = Request::get('accessibility');
		$intake_criteria = Request::get('intake_criteria');
		$referral_method = Request::get('referral_method');
		$city = Request::get('city');
		$district = Request::get('district');
		$sub_district = Request::get('sub_district');
		$neighborhood = Request::get('neighborhood');
		$partner = Request::get('partner');
		$status = Request::get('status');

		$query
			->with([
				'categories.term.langs' =>
				function ($query) use ($default_lang_id) {
					$query->where('lang_id', $default_lang_id);
				}
			])
			->with('partner')
			->with('tag')
			->with([
				'partner.langs' =>
				function ($query) use ($default_lang_id) {
					$query->where('lang_id', $default_lang_id);
				}
			])
			->with([
				'location.city.langs' =>
				function ($query) use ($default_lang_id) {
					$query->where('lang_id', $default_lang_id);
				}
			])
			->with([
				'location.district.langs' =>
				function ($query) use ($default_lang_id) {
					$query->where('lang_id', $default_lang_id);
				}
			])
			->with([
				'location.sub_district.langs' =>
				function ($query) use ($default_lang_id) {
					$query->where('lang_id', $default_lang_id);
				}
			]);

		if (isset($sid) && $sid != null && strlen($sid) > 0) {
			if (is_numeric($sid)) {
				$query->where('id', $sid);
			}
		}

		if (isset($tag) && $tag != null && strlen($tag) > 0) {
			if (is_numeric($tag)) {
				$query->where('tag_id', $tag);
			}
		}

		if (isset($userTagIds) && $userTagIds != null && is_array($userTagIds)) {
			if (!empty($userTagIds)) {
				$query->whereIn('tag_id', $userTagIds);
			}
		}

		if (isset($category) && $category != null && is_numeric($category)) {
			$query->whereHas('terms', function ($query) use ($category) {
				$query->where('term_id', $category);
			});
		}

		if (isset($accessibility) && $accessibility != null && is_numeric($accessibility)) {
			$query->whereHas('terms', function ($query) use ($accessibility) {
				$query->where('term_id', $accessibility);
			});
		}

		if (isset($intake_criteria) && $intake_criteria != null && is_numeric($intake_criteria)) {
			$query->whereHas('terms', function ($query) use ($intake_criteria) {
				$query->where('term_id', $intake_criteria);
			});
		}

		if (isset($referral_method) && $referral_method != null && is_numeric($referral_method)) {
			$query->whereHas('terms', function ($query) use ($referral_method) {
				$query->where('term_id', $referral_method);
			});
		}

		if (isset($city) && $city != null && is_numeric($city)) {
			$query->whereHas('location', function ($query) use ($city) {
				$query->where('city_id', $city);
			});
		}

		if (isset($district) && $district != null && is_numeric($district)) {
			$query->whereHas('location', function ($query) use ($district) {
				$query->where('district_id', $district);
			});
		}

		if (isset($sub_district) && $sub_district != null && is_numeric($sub_district)) {
			$query->whereHas('location', function ($query) use ($sub_district) {
				$query->where('sub_district_id', $sub_district);
			});
		}

		if (isset($partner) && $partner != null && is_numeric($partner)) {
			$query->whereHas('partner', function ($query) use ($partner) {
				$query->where('partner_id', $partner);
			});
		}

		if (isset($status) && $status != null && strlen($status) > 0) {
			if (is_numeric($status)) {
				if ($status == 1) { // Published
					$query->where('published', 1);
					$query->where('end_date', '>', date('Y-m-d h:i:s'));
					$query->where('publish_date', '<=', date('Y-m-d h:i:s'));
				} else if ($status == 2) { // Unpublished
					$query->where('published', 0);
				} else if ($status == 3) { // Scheduled
					$query->where('published', 1);
					$query->where('publish_date', '>', date('Y-m-d h:i:s'));
				} else if ($status == 4) { // Expired
					$query->where('published', 1);
					$query->where('end_date', '<', date('Y-m-d h:i:s'));
				}
			}
		}

		$dataTable = new EloquentDataTable($query);

		$dataTable->editColumn('id', 'services.services.datatables_statuses');

		return $dataTable
			->addColumn('province', function ($data) {
				if ($data != null && $data->location != null && $data->location->city != null && $data->location->city->langs != null && count($data->location->city->langs) > 0) {
					$cityLang = $data->location->city->langs[0];
					return $cityLang->name;
				}
			})
			->addColumn('district', function ($data) {
				if ($data != null && $data->location != null && $data->location->district != null && $data->location->district->langs != null && count($data->location->district->langs) > 0) {
					$districtLang = $data->location->district->langs[0];
					return $districtLang->name;
				}
			})
			->addColumn('sub_district', function ($data) {
				if ($data != null && $data->location != null && $data->location->sub_district != null && $data->location->sub_district->langs != null && count($data->location->sub_district->langs) > 0) {
					$districtLang = $data->location->sub_district->langs[0];
					return $districtLang->name;
				}
			})
			->addColumn('organization', function ($data) {
				if ($data != null && $data->partner != null && $data->partner != null && $data->partner->langs != null && count($data->partner->langs) > 0) {
					$partnerLang = $data->partner->langs[0];
					return $partnerLang->name;
				}
			})
			->addColumn('tag', function ($data) {
				return $data->tag ? $data->tag->name : '';
			})
			->addColumn('category', function ($data) {
				$cats = [];
				if ($data != null && $data->categories != null && count($data->categories) > 0) {
					foreach ($data->categories as $category) {
						if ($category->term != null && $category->term->langs && count($category->term->langs) > 0 && !$category->deleted) {
							array_push($cats, '<span class="service-category-item term-' . $category->term->id . '" style="background-color:' . $category->term->color . '">' . $category->term->langs[0]->name . '</span>');
						}
					}
				}

				return implode('', $cats);
			}, false)
			->addColumn('action', 'services.services.datatables_actions')
			->rawColumns(['id', 'category', 'action']);
	}

	/**
	 * Get query source of dataTable.
	 *
	 * @param \App\Models\Service $model
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function query(Service $model)
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
				"title" => __('app.Id'),
				"data" => "id"
			],
			/* [
				"name" => "tag",
				"title" => __('app.Tag'),
				"data" => "tag"
			], */
			[
				"name" => "city",
				"title" => __('app.City'),
				"data" => "province", //location.city.langs[0].name
				"orderable" => false
			],
			[
				"name" => "district",
				"title" => __('app.District'),
				"data" => "district", //location.district.langs[0].name
				"orderable" => false
			],
			[
				"name" => "sub_district",
				"title" => __('app.Sub District'),
				"data" => "sub_district", //location.district.langs[0].name
				"orderable" => false
			],
			[
				"name" => "partner",
				"title" => __('app.Organization'),
				"data" => "organization", //partner.langs[0].name
				"orderable" => false
			],
			[
				"name" => "category",
				"title" => __('app.Category'),
				"data" => "category",
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
		return 'servicesdatatable_' . time();
	}
}
