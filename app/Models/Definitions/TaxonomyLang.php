<?php

namespace App\Models\Definitions;

use Eloquent as Model;

/**
 * Class TaxonomyLang
 * @package App\Models\Definitions
 * @version May 11, 2019, 11:10 am UTC
 *
 * @property integer taxonomy_id
 * @property boolean lang_id
 * @property string name
 * @property boolean deleted
 */
class TaxonomyLang extends Model
{

    public $table = 'taxonomy_lang';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'taxonomy_id',
        'lang_id',
        'name',
        'deleted'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'taxonomy_id' => 'integer',
        'lang_id' => 'integer',
        'name' => 'string',
        'deleted' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'taxonomy_id' => 'required',
        'lang_id' => 'required',
        'name' => 'required',
        'deleted' => 'required'
    ];
}
