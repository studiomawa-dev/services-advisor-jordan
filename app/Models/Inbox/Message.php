<?php

namespace App\Models\Inbox;

use Eloquent as Model;

/**
 * Class Message
 * @package App\Models\Inbox
 * @version May 27, 2019, 4:47 pm UTC
 *
 * @property integer from
 * @property integer to
 * @property string title
 * @property string body
 * @property boolean is_read
 * @property boolean deleted
 */
class Message extends Model
{

	public $table = 'message';
	public $timestamps = false;

	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';



	public $fillable = [
		'from',
		'to',
		'title',
		'body',
		'is_read',
		'deleted'
	];

	/**
	 * The attributes that should be casted to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'id' => 'integer',
		'from' => 'integer',
		'to' => 'integer',
		'title' => 'string',
		'body' => 'string',
		'is_read' => 'boolean',
		'deleted' => 'boolean'
	];

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public static $rules = [
		'to' => 'required',
		'title' => 'required',
		'body' => 'required',
	];
}
