<?php

namespace App\Repositories\Contents;

use App\Models\Contents\Page;
use App\Repositories\BaseRepository;

/**
 * Class PageRepository
 * @package App\Repositories\Contents
 * @version May 27, 2019, 2:06 pm UTC
*/

class PageRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'author_id',
        'category_id',
        'title',
        'seo_title',
        'excerpt',
        'body',
        'image',
        'slug',
        'meta_description',
        'meta_keywords',
        'status',
        'featured'
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
        return Page::class;
    }
}
