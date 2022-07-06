<?php

namespace App\Models\Settings;

use Eloquent as Model;
use App\Models\Definitions\Term;
use App\Traits\Userstamps;

/**
 * Class User
 * @package App\Models\Settings
 * @version May 27, 2019, 8:25 pm UTC
 *
 * @property string name
 * @property string username
 * @property string email
 * @property string|\Carbon\Carbon email_verified_at
 * @property integer role_id
 * @property integer partner_id
 * @property string password
 * @property string remember_token
 */
class User extends Model
{
	use Userstamps;

	public $table = 'user';

	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
	const CREATED_BY = 'created_by';
	const UPDATED_BY = 'updated_by';
	const DELETED_BY = 'deleted_by';

	public $fillable = [
		'name',
		'username',
		'email',
		'email_verified_at',
		'photo_id',
		'feedback_ids',
		'password',
		'remember_token'
	];

	/**
	 * The attributes that should be casted to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'id' => 'integer',
		'name' => 'string',
		'username' => 'string',
		'email' => 'string',
		'email_verified_at' => 'datetime',
		'password' => 'string',
		'remember_token' => 'string'
	];

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public static $rules = [
		'name' => 'required',
		'username' => 'required',
		'email' => 'required'
	];

	public function roles()
	{
		return $this->belongsToMany('App\Models\Settings\Role', 'user_role', 'user_id', 'role_id');
	}

	public function photo()
	{
		return $this->belongsTo('App\Models\Contents\Media', 'photo_id');
	}

	public function tags()
	{
		return $this->belongsToMany('App\Models\Settings\tag', 'user_tag', 'user_id', 'tag_id');
	}

	public function partners()
	{
		return $this->belongsToMany('App\Models\Settings\Partner', 'user_partner', 'user_id', 'partner_id');
	}

	public static function getFeedbackTerms($userId)
	{
		$feedbackTerms = [];
		$user = self::find(1);
		if ($user != null) {
			$feedbackIdsStr = $user->feedback_ids;
			if ($feedbackIdsStr != null && strlen($feedbackIdsStr) > 1) {
				$feedbackIds = explode(',', $feedbackIdsStr);
				$feedbackTerms = Term::whereIn('id', $feedbackIds)
					->with('langs')
					->get();
			}
		}
		return $feedbackTerms;
	}
}
