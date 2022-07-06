<?php

namespace App\Repositories\Definitions;

use App\Models\Definitions\Taxonomy;
use App\Repositories\BaseRepository;
use App\Models\Settings\Language;

/**
 * Class TaxonomyRepository
 * @package App\Repositories\Definitions
 * @version May 11, 2019, 10:41 am UTC
 */

class TaxonomyRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'key',
        'order',
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
        return Taxonomy::class;
    }

    public function getWithDefaultLang($id)
    {
        $default_lang_id = Language::defaultLang()->id;

        return Taxonomy::where('id', $id)
            ->withAndWhereHas(
                'langs',
                function ($query) use ($default_lang_id) {
                    $query->where('lang_id', $default_lang_id);
                }
            )
            ->orderBy('order')
            ->first();
    }

    public function getAllWithDefaultLang()
    {
        $default_lang_id = Language::defaultLang()->id;

        return Taxonomy::withAndWhereHas(
            'langs',
            function ($query) use ($default_lang_id) {
                $query->where('lang_id', $default_lang_id);
            }
        )
            ->orderBy('order')
            ->get();
    }

    public function getForSelect()
    {
        $result = [];
        $allTaxonomies = $this->getAllWithDefaultLang();
        if ($allTaxonomies != null && count($allTaxonomies) > 0) {
            foreach ($allTaxonomies as $taxonomy) {
                $text = '';
                if ($taxonomy->langs != null && isset($taxonomy->langs[0]) && isset($taxonomy->langs[0]->name)) {
                    $text = $taxonomy->langs[0]->name;
                }
                $result[$taxonomy->id] = $text;
            }
        }
        return $result;
    }
}
