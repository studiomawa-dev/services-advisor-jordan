<?php

namespace App\Models\Contents;

use Eloquent as Model;

/**
 * Class Media
 * @package App\Models\Contents
 * @version May 30, 2019, 3:13 pm UTC
 *
 * @property string filename
 * @property string filemime
 * @property string type
 * @property integer filesize
 * @property boolean status
 * @property integer width
 * @property integer height
 */
class Media extends Model
{

	public $table = 'media';

	public $timestamps = false;

	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';



	public $fillable = [
		'filename',
		'filemime',
		'type',
		'filesize',
		'status',
		'width',
		'height'
	];

	/**
	 * The attributes that should be casted to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'id' => 'integer',
		'filename' => 'string',
		'filemime' => 'string',
		'type' => 'string',
		'filesize' => 'integer',
		'status' => 'boolean',
		'width' => 'integer',
		'height' => 'integer'
	];

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public static $rules = [
		'filename' => 'required'
	];
}
