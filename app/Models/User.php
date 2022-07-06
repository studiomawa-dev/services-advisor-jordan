<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Musonza\Chat\Traits\Messageable;

class User extends Authenticatable
{
	use Notifiable;
	use Messageable;

	public $table = 'user';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', 'email', 'password',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token',
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'email_verified_at' => 'datetime',
	];

	public function roles()
	{
		return $this->belongsToMany('App\Models\Settings\Role', 'user_role', 'user_id', 'role_id');
	}

	public function tags()
	{
		return $this->belongsToMany('App\Models\Settings\Tag', 'user_tag', 'user_id', 'tag_id');
	}

	public function partners()
	{
		return $this->belongsToMany('App\Models\Settings\Partner', 'user_partner', 'user_id', 'partner_id');
	}

	public function roleIds()
	{
		$roles = $this->roles()->get();
		$roleIds = [];
		foreach ($roles as $role) {
			array_push($roleIds, $role->id);
		}
		return $roleIds;
	}

	public function isSysAdmin()
	{
		$roles = $this->roles()->get();

		foreach ($roles as $role) {
			if ($role->name == 'sysadmin') {
				return true;
			}
		}

		return false;
	}

	public function isAdmin()
	{
		$roles = $this->roles()->get();

		foreach ($roles as $role) {
			if ($role->name == 'sysadmin' || $role->name == 'admin') {
				return true;
			}
		}

		return false;
	}

	public function isInRole($roleName)
	{
		$roles = $this->roles()->get();
		foreach ($roles as  $role) {
			if ($role->name == $roleName && count($roles) == 1) {
				return true;
			}
		}

		return false;
	}

	public function isInRoleById($role)
	{
		return $this->isInRoleId($role['id']);
	}

	public function isInRoleId($roleId)
	{
		$roleIds = $this->roleIds();
		return in_array($roleId, $roleIds);
	}

	public function isInRoles($userRoles)
	{
		$userRoleIds = [];
		foreach ($userRoles as $userRole) {
			array_push($userRoleIds, $userRole['id']);
		}
		return $this->isInRoleIds($userRoleIds);
	}

	public function isInRoleIds($userRoleIds)
	{
		$result = false;
		$roleIds = $this->roleIds();
		foreach ($userRoleIds as $userRoleId) {
			if (in_array($userRoleId, $roleIds)) {
				$result = true;
				break;
			}
		}
		return $result;
	}

	public function tagIds()
	{
		$tags = $this->tags()->get();

		$tagIds = [];
		foreach ($tags as $tag) {
			array_push($tagIds, $tag->id);
		}

		if (in_array(3, $tagIds)) {
			return [1, 2, 3];
		}
		return $tagIds;
	}

	public function partnerIds()
	{
		$partners = $this->partners()->get();
		$partnerIds = [];
		foreach ($partners as $partner) {
			array_push($partnerIds, $partner->id);
		}
		return $partnerIds;
	}

	public function isInPartner($partner)
	{
		return $this->isInPartnerId($partner['id']);
	}

	public function isInPartnerId($partnerId)
	{
		$partnerIds = $this->partnerIds();
		return in_array($partnerId, $partnerIds);
	}

	public function isInPartners($userPartners)
	{
		$userPartnerIds = [];
		foreach ($userPartners as $userPartner) {
			array_push($userPartnerIds, $userPartner['id']);
		}
		return $this->isInPartnerIds($userPartnerIds);
	}

	public function isInPartnerIds($userPartnerIds)
	{
		$result = false;
		$partnerIds = $this->partnerIds();
		foreach ($userPartnerIds as $userPartnerId) {
			if (in_array($userPartnerId, $partnerIds)) {
				$result = true;
				break;
			}
		}
		return $result;
	}

	public function saveAction()
	{
		$user = Auth::user();
		if ($user) {

			$model = self::find($user->id);
			$model->last_action = date('Y-m-d H:i:s');
			$model->update();
			//DB::update('UPDATE [user] SET last_action = ? where id = ?', [date('Y-m-d H:i:s'), $user->id]);
		}
	}
}
