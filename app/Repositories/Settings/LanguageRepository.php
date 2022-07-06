<?php

namespace App\Repositories\Settings;

use App\Models\Settings\Language;
use App\Repositories\BaseRepository;

/**
 * Class LanguageRepository
 * @package App\Repositories\Settings
 * @version May 10, 2019, 10:29 pm UTC
 */

class LanguageRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'code',
        'is_default',
        'is_backend',
        'is_rtl',
        'fb_lang_code'
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
        return Language::class;
    }
}
