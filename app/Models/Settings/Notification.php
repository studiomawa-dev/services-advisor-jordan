<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\SoftDeletes;
use Eloquent as Model;
use App\Traits\Userstamps;

/**
 * Class Notification
 * @package App\Models\Settings
 * @version May 26, 2019, 1:05 pm UTC
 *
 * @property integer type_id
 * @property integer logo_id
 */
class Notification extends Model
{
	use SoftDeletes;
	use Userstamps;

	public $table = 'notification';

	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
	const CREATED_BY = 'created_by';
	const UPDATED_BY = 'updated_by';
	const DELETED_BY = 'deleted_by';

	protected $dateFormat = 'U';

	public $fillable = [
		'title',
		'message',
		'sending_date',
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
		'title' => 'string',
		'message' => 'string'
	];

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public static $rules = [];

	public function lang()
	{
		$default_lang_id = Language::defaultLang()->id;
		return $this->hasOne('App\Models\Settings\NotificationLang')->where('lang_id', $default_lang_id)->orWhereNotNull('title');
	}

	public function langs()
	{
		return $this->hasMany('App\Models\Settings\NotificationLang');
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
