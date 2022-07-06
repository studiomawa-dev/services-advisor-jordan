<?php

namespace App\Repositories\Inbox;

use App\Models\Inbox\Message;
use App\Repositories\BaseRepository;

/**
 * Class MessageRepository
 * @package App\Repositories\Inbox
 * @version May 27, 2019, 4:47 pm UTC
*/

class MessageRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'from',
        'to',
        'title',
        'body',
        'is_read',
        'deleted'
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
        return Message::class;
    }
}
