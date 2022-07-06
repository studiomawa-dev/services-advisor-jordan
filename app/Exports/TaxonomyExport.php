<?php

namespace App\Exports;

use App\Models\Taxonomy;
use Maatwebsite\Excel\Concerns\FromCollection;

class TaxonomyExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Taxonomy::all();
    }
}
