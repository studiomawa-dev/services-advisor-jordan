<?php

namespace App\Models\Services;

use Eloquent as Model;

/**
 * Class ServiceContact
 * @package App\Models\Services
 * @version May 31, 2019, 4:47 pm UTC
 *
 * @property integer service_id
 * @property integer contact_id
 * @property integer category_id
 */
class ServiceContact extends Model
{

	public $table = 'service_contact';

	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';

	public $fillable = [
		'service_id',
		'contact_id',
		'category_id'
	];

	/**
	 * The attributes that should be casted to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'id' => 'integer',
		'service_id' => 'integer',
		'contact_id' => 'integer',
		'category_id' => 'integer'
	];

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public static $rules = [];

	public function contact()
	{
		return $this->belongsTo('App\Models\Settings\User', 'contact_id');
	}
}
