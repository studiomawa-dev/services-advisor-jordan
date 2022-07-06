<?php

namespace App\DataTables\Settings;

use Request;
use App\Models\Settings\User;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

class UserDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $src = Request::get('src');
        $role = Request::get('role');
        $tag = Request::get('tag');
        $partner = Request::get('partner');
        $category = Request::get('category');

        $query->with('partners');
        $query->with('tags');

        $query->where(function ($query) use ($src) {
            $query->where('username', 'like', '%' . $src . '%')
                ->orWhere('name', 'like', '%' . $src . '%')
                ->orWhere('email', 'like', '%' . $src . '%');
        });

        if (isset($role) && $role != null && is_numeric($role)) {
            $query->whereHas('roles', function ($query) use ($role) {
                $query->where('role.id', $role);
            });
        }

        if (isset($tag) && $tag != null && is_numeric($tag)) {
            $query->whereHas('tags', function ($query) use ($tag) {
                $query->where('tag.id', $tag);
            });
        }


        if (isset($partner) && $partner != null && is_numeric($partner)) {
            $query->whereHas('partners', function ($query) use ($partner) {
                $query->where('partner.id', $partner);
            });
        }

        if (isset($category) && $category != null && strlen($category) > 0) {
            if (is_numeric($category)) {
                $query->whereRaw("find_in_set('" . $category . "', feedback_ids)");
            }
        }

        $dataTable = new EloquentDataTable($query);
        $dataTable->editColumn('id', 'settings.users.datatables_statuses');
        return $dataTable
            ->addColumn('tag', function ($data) {
                $tags = [];
                if ($data != null && $data->tags != null && count($data->tags) > 0) {
                    foreach ($data->tags as $tag) {
                        array_push($tags, $tag->name);
                    }
                }

                return implode(', ', $tags);
            }, false)
            ->addColumn('partner', function ($data) {
                $partners = [];
                if ($data != null && $data->partners != null && count($data->partners) > 0) {
                    foreach ($data->partners as $partner) {
                        if ($partner->langs && count($partner->langs) > 0 && !$partner->deleted_at) {
                            array_push($partners, $partner->langs[0]->name);
                        }
                    }
                }

                return implode(', ', $partners);
            }, false)
            ->addColumn('action', 'settings.users.datatables_actions')
            ->rawColumns(['id', 'action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
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
                "title" => __('app.Name'),
                "data" => "name"
            ],
            [
                "name" => "email",
                "title" => __('app.E-Mail'),
                "data" => "email"
            ],
            [
                "name" => "tag",
                "title" => __('app.Tag'),
                "data" => "tag"
            ],
            [
                "name" => "partner",
                "title" => __('app.Organizations'),
                "data" => "partner"
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
        return 'usersdatatable_' . time();
    }
}
