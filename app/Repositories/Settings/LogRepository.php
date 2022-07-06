<?php

namespace App\Repositories\Settings;

use App\Models\Settings\Log;
use App\Repositories\BaseRepository;

/**
 * Class LogRepository
 * @package App\Repositories\Settings
 * @version August 7, 2019, 6:42 pm UTC
*/

class LogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'level',
        'username',
        'ipaddress',
        'category',
        'type',
        'message'
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
        return Log::class;
    }
}
