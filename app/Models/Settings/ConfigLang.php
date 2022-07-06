<?php

namespace App\Models\Services;

use Eloquent as Model;

/**
 * Class LocationLang
 * @package App\Models\Services
 * @version May 20, 2019, 10:43 pm UTC
 *
 * @property integer location_id
 * @property boolean lang_id
 * @property string name
 * @property string address
 * @property string direction
 */
class ConfigLang extends Model
{

    public $table = 'config_lang';

    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'config_id',
        'lang_id',
        'value'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'config_id' => 'integer',
        'lang_id' => 'integer',
        'value' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];
}
