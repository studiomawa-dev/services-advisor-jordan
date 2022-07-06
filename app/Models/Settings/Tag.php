<?php

namespace App\Models\Settings;

use Eloquent as Model;

/**
 * Class Tag
 * @package App\Models\Settings
 * @version Apr 12, 2022, 8:25 pm UTC
 *
 * @property string code
 * @property string name
 */
class Tag extends Model
{
    public $table = 'tag';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const CREATED_BY = 'created_by';
    const UPDATED_BY = 'updated_by';
    const DELETED_BY = 'deleted_by';

    public $fillable = [
        'code',
        'name',

    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'code' => 'string',
        'name' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
        'code' => 'required',
    ];
}
