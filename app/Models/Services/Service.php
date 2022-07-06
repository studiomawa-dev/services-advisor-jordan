<?php

namespace App\Models\Services;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Userstamps;

/**
 * Class Service
 * @package App\Models\Services
 * @version May 18, 2019, 11:02 am UTC
 *
 * @property string start_date
 * @property string end_date
 * @property integer partner_id
 * @property boolean published
 */
class Service extends Model
{
	use SoftDeletes;
	use Userstamps;

	public $table = 'service';

	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
	const CREATED_BY = 'created_by';
	const UPDATED_BY = 'updated_by';
	const DELETED_BY = 'deleted_by';

	protected $dateFormat = 'U';

	public $fillable = [
		'tag_id',
		'start_date',
		'end_date',
		'partner_id',
		'location_id',
		'published',
		'is_remote',
		'publish_date',
		'backendonly',
		'created_by',
		'updated_by',
		'deleted_by',
		'import_id',
	];

	/**
	 * The attributes that should be casted to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'id' => 'integer',
		'tag_id' => 'integer',
		'start_date' => 'datetime:Y-m-d',
		'end_date' => 'datetime:Y-m-d',
		'partner_id' => 'integer',
		'published' => 'boolean',
		'is_remote' => 'boolean',
		'publish_date' => 'datetime:Y-m-d',
		'backendonly' => 'boolean',
		'import_id' => 'integer',
	];

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public static $rules = [];

	public function partner()
	{
		return $this->belongsTo('App\Models\Settings\Partner');
	}

	public function tag()
	{
		return $this->belongsTo('App\Models\Settings\Tag');
	}

	public function location()
	{
		return $this->belongsTo('App\Models\Services\Location');
	}

	public function langs()
	{
		return $this->hasMany('App\Models\Services\ServiceLang');
	}

	public function hours()
	{
		return $this->hasMany('App\Models\Services\ServiceHour');
	}

	public function contacts()
	{
		return $this->hasMany('App\Models\Services\ServiceContact');
	}

	public function terms()
	{
		return $this->hasMany('App\Models\Services\ServiceTerm');
	}

	public function categories()
	{
		return $this->hasMany('App\Models\Services\ServiceCategory');
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
