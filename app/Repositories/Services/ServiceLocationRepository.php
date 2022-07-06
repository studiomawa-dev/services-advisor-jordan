<?php

namespace App\Repositories\Services;

use App\Models\Services\ServiceLocation;
use App\Repositories\BaseRepository;

/**
 * Class ServiceLocationRepository
 * @package App\Repositories\Services
 * @version May 31, 2019, 4:47 pm UTC
*/

class ServiceLocationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'service_id',
        'location_id'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ServiceLocation::class;
    }
}
