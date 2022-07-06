<?php

namespace App\Models\Services;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Userstamps;
use Eloquent as Model;

/**
 * Class Location
 * @package App\Models\Services
 * @version May 20, 2019, 10:35 pm UTC
 *
 * @property integer country_id
 * @property integer city_id
 * @property integer district_id
 * @property integer sub_district_id
 * @property integer neighborhood_id
 * @property float latitude
 * @property float longitude
 */
class Location extends Model
{
	use SoftDeletes;
	use Userstamps;

	public $table = 'location';

	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
	const CREATED_BY = 'created_by';
	const UPDATED_BY = 'updated_by';
	const DELETED_BY = 'deleted_by';

	protected $primaryKey = "id";
	protected $dateFormat = 'U';

	public $fillable = [
		'country_id',
		'city_id',
		'district_id',
		'sub_district_id',
		'neighborhood_id',
		'partner_ids',
		'latitude',
		'longitude',
		'created_by',
		'updated_by',
		'deleted_by'
	];

	/**
	 * The attributes that should be casted to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'id' => 'integer',
		'country_id' => 'integer',
		'city_id' => 'integer',
		'district_id' => 'integer',
		'sub_district_id' => 'integer',
		'neighborhood_id' => 'integer',
		'latitude' => 'float',
		'longitude' => 'float'
	];

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public static $rules = [];

	public function langs()
	{
		return $this->hasMany('App\Models\Services\LocationLang');
	}

	public function city()
	{
		return $this->belongsTo('App\Models\Definitions\Term');
	}

	public function district()
	{
		return $this->belongsTo('App\Models\Definitions\Term');
	}

	public function sub_district()
	{
		return $this->belongsTo('App\Models\Definitions\Term');
	}

	public function neighborhood()
	{
		return $this->belongsTo('App\Models\Definitions\Term');
	}

	public function partner()
	{
		return $this->belongsTo('App\Models\Settings\Partner');
	}

	public function creator()
	{
		return $this->belongsTo('App\Models\Settings\User', 'created_by');
	}

	public function editor()
	{
		return $this->belongsTo('App\Models\Settings\User', 'updated_by');
	}
}
