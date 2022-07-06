<?php

namespace App\Models\Settings;

use Eloquent as Model;

/**
 * Class Role
 * @package App\Models\Settings
 * @version May 10, 2019, 9:05 pm UTC
 *
 * @property string name
 * @property string display_name
 */
class Role extends Model
{

    public $table = 'role';

    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'name',
        'display_name'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'display_name' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required'
    ];


}
