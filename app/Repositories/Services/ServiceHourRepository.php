<?php

namespace App\Repositories\Services;

use App\Models\Services\ServiceHour;
use App\Repositories\BaseRepository;

/**
 * Class ServiceHourRepository
 * @package App\Repositories\Services
 * @version May 31, 2019, 4:47 pm UTC
 */

class ServiceHourRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'service_id',
        'day',
        'start_hour',
        'end_hour'
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
        return ServiceHour::class;
	}

	public function insert($id, $hours)
    {
        ServiceHour::setServiceHours($id, $hours);
    }

    public function updateOrInsert($id, $input)
    {
        $serviceHours = $input['service_hours'];
        if ($serviceHours != null && strlen($serviceHours) > 0) {
            $serviceHoursJson = json_decode($serviceHours);

            ServiceHour::setServiceHours($id, $serviceHoursJson);
        }
    }
}
