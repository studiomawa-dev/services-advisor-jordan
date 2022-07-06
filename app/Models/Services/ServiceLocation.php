<?php

namespace App\Models\Services;

use Eloquent as Model;

/**
 * Class ServiceLocation
 * @package App\Models\Services
 * @version May 31, 2019, 4:47 pm UTC
 *
 * @property integer service_id
 * @property integer location_id
 */
class ServiceLocation extends Model
{

    public $table = 'service_location';

    const CREATED_AT = null;
    const UPDATED_AT = null;

    public $fillable = [
        'service_id',
        'location_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'service_id' => 'integer',
        'location_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];
}
