<?php

namespace App\Models\Services;

use Eloquent as Model;

/**
 * Class ServiceLang
 * @package App\Models\Services
 * @version May 18, 2019, 1:22 pm UTC
 *
 * @property integer service_id
 * @property boolean lang_id
 * @property string name
 * @property string additional
 * @property string comment
 * @property string phone
 * @property string link
 */
class ServiceLang extends Model
{

    public $table = 'service_lang';

    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'service_id',
        'lang_id',
        'name',
        'slug',
        'additional',
        'comments',
        'phone',
        'link'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'service_id' => 'integer',
        'lang_id' => 'integer',
        'name' => 'string',
        'additional' => 'string',
        'comments' => 'string',
        'phone' => 'string',
        'link' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];
}
