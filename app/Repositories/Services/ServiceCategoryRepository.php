<?php

namespace App\Repositories\Services;

use App\Models\Services\ServiceCategory;
use App\Repositories\BaseRepository;

/**
 * Class ServiceCategoryRepository
 * @package App\Repositories\Services
 * @version May 31, 2019, 4:47 pm UTC
*/

class ServiceCategoryRepository extends BaseRepository
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
        return ServiceCategory::class;
	}

	public static function getCategoryServiceTermIds($serviceId)
    {
        return ServiceCategory::getServiceCategoryTermIds($serviceId);
    }

    public static function setServiceCategoryTermIds($serviceId, $termIds)
    {
        ServiceCategory::setServiceCategoryTermIds($serviceId, $termIds);
    }
}
