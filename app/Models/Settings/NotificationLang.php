<?php

namespace App\Models\Settings;

use Eloquent as Model;

/**
 * Class NotificationLang
 * @package App\Models\Settings
 * @version May 27, 2019, 4:54 pm UTC
 *
 * @property integer notification_id
 * @property boolean lang_id
 * @property string name
 * @property string full_name
 * @property string url
 * @property string description
 */
class NotificationLang extends Model
{

	public $table = 'notification_lang';

	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';

	public $timestamps = false;

	public $fillable = [
		'notification_id',
		'lang_id',
		'title',
		'message',
		'payload',
		'report_name'
	];

	/**
	 * The attributes that should be casted to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'id' => 'integer',
		'notification_id' => 'integer',
		'lang_id' => 'integer',
		'title' => 'string',
		'message' => 'string',
		'payload' => 'string',
		'report_name' => 'string'
	];

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public static $rules = [
		'title' => 'required'
	];
}
