<?php

namespace App\Http\Controllers\Admin\Import;

use Flash;
use Response;
use Illuminate\Support\Facades\Auth;
use Request;
use App\Http\Controllers\AppBaseController;
use App\Imports\TermsImport;
use App\Models\Definitions\Taxonomy;
use Maatwebsite\Excel\Facades\Excel;

class IndexController extends AppBaseController
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the Service.
     *
     * @param ServiceDataTable $serviceDataTable
     * @return Response
     */
    public function index()
    {
        $routes = [
            'import/user',
            'import/partners',
            'import/locations',
            'import/category',
            'import/terms',
            'import/services',
        ];

        $links = [];
        foreach ($routes as $route) {
            $links[] = '<a href="' . route($route) . '" target="_blank">' . $route . '</a>';
        }

        return '<ul><li>' . implode('</li><li>', $links) . '</li></ul>';
    }
}
