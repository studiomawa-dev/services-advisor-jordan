<?php

namespace App\Repositories\Contents;

use App\Models\Contents\Media;
use App\Repositories\BaseRepository;

/**
 * Class MediaRepository
 * @package App\Repositories\Contents
 * @version May 30, 2019, 3:13 pm UTC
*/

class MediaRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'filename',
        'filemime',
        'type',
        'filesize',
        'status',
        'width',
        'height'
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
        return Media::class;
    }
}
