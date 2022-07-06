<?php

namespace App\Models\Definitions;

use Eloquent as Model;
use App\Models\Settings\Language;

/**
 * Class TermLang
 * @package App\Models\Definitions
 * @version May 11, 2019, 11:11 am UTC
 *
 * @property integer term_id
 * @property boolean lang_id
 * @property string name
 * @property boolean deleted
 */
class TermLang extends Model
{

    public $table = 'term_lang';

    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'term_id',
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
        'term_id' => 'integer',
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
        'term_id' => 'required',
        'lang_id' => 'required',
        'name' => 'required',
        'deleted' => 'required'
    ];
}
