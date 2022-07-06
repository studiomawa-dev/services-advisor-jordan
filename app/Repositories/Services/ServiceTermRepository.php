<?php

namespace App\Repositories\Services;

use App\Models\Services\ServiceTerm;
use App\Repositories\BaseRepository;

/**
 * Class ServiceTermRepository
 * @package App\Repositories\Services
 * @version May 31, 2019, 4:47 pm UTC
 */

class ServiceTermRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'service_id',
        'term_id',
        'taxonomy_id'
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
        return ServiceTerm::class;
    }

    public static function getServiceTermIds($serviceId)
    {
        return ServiceTerm::getServiceTermIds($serviceId);
    }

    public static function setServiceTermIds($serviceId, $termIds)
    {
        ServiceTerm::setServiceTermIds($serviceId, $termIds);
    }
}
