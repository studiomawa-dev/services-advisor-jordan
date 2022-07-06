<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent as Model;
use App\Traits\Userstamps;

/**
 * Class Partner
 * @package App\Models\Settings
 * @version May 26, 2019, 1:05 pm UTC
 *
 * @property integer type_id
 * @property integer logo_id
 */
class Partner extends Model
{
	use SoftDeletes;
	use Userstamps;

	public $table = 'partner';

	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
	const CREATED_BY = 'created_by';
	const UPDATED_BY = 'updated_by';
	const DELETED_BY = 'deleted_by';

	protected $dateFormat = 'U';

	public $fillable = [
		'tag_id',
		'type_id',
		'logo_id',
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
		'tag_id' => 'integer',
		'type_id' => 'integer',
		'logo_id' => 'integer'
	];

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public static $rules = [];

	public function tag()
	{
		return $this->belongsTo('App\Models\Settings\Tag');
	}

	public function lang()
	{
		$default_lang_id = Language::defaultLang()->id;
		return $this->hasOne('App\Models\Settings\PartnerLang')->where('lang_id', $default_lang_id)->orWhereNotNull('name');
	}

	public function langs()
	{
		return $this->hasMany('App\Models\Settings\PartnerLang');
	}

	public function logo()
	{
		return $this->belongsTo('App\Models\Contents\Media');
	}

	public function type()
	{
		return $this->belongsTo('App\Models\Definitions\Term');
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
