<?php

namespace App\Repositories\Services;

use App\Models\Services\Contact;
use App\Repositories\BaseRepository;
use stdClass;

/**
 * Class ContactRepository
 * @package App\Repositories\Services
 * @version May 27, 2019, 1:59 pm UTC
 */

class ContactRepository extends BaseRepository
{
	/**
     * @var array
     */
    protected $fieldSearchable = [
        'title',
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
        return Contact::class;
    }
}
