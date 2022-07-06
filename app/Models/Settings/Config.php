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
class Config extends Model
{

    public $table = 'config';

    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'name',
        'title',
		'type'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'title' => 'string',
        'type' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
    ];

	public function langs()
	{
		return $this->hasMany('App\Models\Settings\ConfigLang');
	}
}
