<?php

namespace App\Models\Settings;

use Eloquent as Model;

/**
 * Class PartnerLang
 * @package App\Models\Settings
 * @version May 27, 2019, 4:54 pm UTC
 *
 * @property integer partner_id
 * @property boolean lang_id
 * @property string name
 * @property string full_name
 * @property string url
 * @property string description
 */
class PartnerLang extends Model
{

	public $table = 'partner_lang';

	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';

	public $timestamps = false;

	public $fillable = [
		'partner_id',
		'lang_id',
		'slug',
		'name',
		'full_name',
		'url',
		'description'
	];

	/**
	 * The attributes that should be casted to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'id' => 'integer',
		'partner_id' => 'integer',
		'lang_id' => 'integer',
		'name' => 'string',
		'full_name' => 'string',
		'url' => 'string',
		'description' => 'string'
	];

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public static $rules = [
		'name' => 'required'
	];

	public function partner()
	{
		return $this->belongsTo('App\Models\Settings\Partner');
	}
}
