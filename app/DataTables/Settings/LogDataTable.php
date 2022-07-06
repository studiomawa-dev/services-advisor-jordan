<?php

namespace App\DataTables\Settings;

use Request;
use App\Models\Settings\Log;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Illuminate\Support\Carbon;

class LogDataTable extends DataTable
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

        if (isset($src) && strlen($src) > 0) {
            $query->where('username', 'like', '%' . $src . '%');
            $query->orWhere('ipaddress', 'like', '%' . $src . '%');
            $query->orWhere('category', 'like', '%' . $src . '%');
            $query->orWhere('type', 'like', '%' . $src . '%');
            $query->orWhere('message', 'like', '%' . $src . '%');
        }


        $dataTable = new EloquentDataTable($query);
        $dataTable->editColumn('created_at', function ($user) {
            return $user->created_at ? with(new Carbon($user->created_at))->format('d/m/Y H:i:s') : '';
        });

        return $dataTable;
        //return $dataTable->addColumn('action', 'settings.logs.datatables_actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Log $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Log $model)
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
            ->parameters([
                'order'     => [[0, 'desc']]
            ]);
        /*
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
            */
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
                "name" => "created_at",
                "title" => __("app.Created At"),
                "data" => "created_at"
            ],
            [
                "name" => "username",
                "title" => __("app.Username"),
                "data" => "username"
            ],
            [
                "name" => "ipaddress",
                "title" => __("app.IP Address"),
                "data" => "ipaddress"
            ],
            [
                "name" => "category",
                "title" => __("app.Category"),
                "data" => "category"
            ],
            [
                "name" => "type",
                "title" => __("app.Type"),
                "data" => "type"
            ],
            [
                "name" => "message",
                "title" => __("app.Message"),
                "data" => "message"
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
        return 'logsdatatable_' . time();
    }
}
