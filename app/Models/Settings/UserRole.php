<?php

namespace App\Models\Settings;

use Eloquent as Model;

/**
 * Class UserRole
 * @package App\Models\Settings
 * @version May 29, 2019, 2:19 pm UTC
 *
 * @property integer user_id
 * @property integer role_id
 */
class UserRole extends Model
{

	public $table = 'user_role';

	/* const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
 */
	public $fillable = [
		'user_id',
		'role_id'
	];

	/**
	 * The attributes that should be casted to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'id' => 'integer',
		'user_id' => 'integer',
		'role_id' => 'integer'
	];

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public static $rules = [
		'user_id' => 'required',
		'role_id' => 'required'
	];

	public static function getUsersByRoleId($roleId)
	{
		$userIds = self::getRoleUserIds($roleId);
		$users = [];
		if (count($userIds) > 0) {
			$users = User::whereIn('id', $userIds)
				->with('partners')
				->get();

			if ($users != null && count($users) > 0) {
				foreach ($users as $user) {
					$feedbackTermsNames = [];
					$feedbackTerms = User::getFeedbackTerms($user->id);

					if ($feedbackTerms != null && count($feedbackTerms) > 0) {
						foreach ($feedbackTerms as $feedbackTerm) {
							$feedbackTermsNames[] = $feedbackTerm->langs[0]->name;
						}
					}

					$user->feedbackTerms = implode(', ', $feedbackTermsNames);
				}
			}
		}

		return $users;
	}

	public static function getRoleUserIds($roleId)
	{
		$userIds = [];
		$roleUsers = self::where('role_id', $roleId)->get();
		if ($roleUsers != null && count($roleUsers) > 0) {
			foreach ($roleUsers as $roleUser) {
				$userIds[] =  $roleUser->user_id;
			}
		}

		return $userIds;
	}

	public static function getUserRoles($userId)
	{
		$roleIds = self::getUserRoleIds($userId);
		$roles = [];
		if (count($roleIds) > 0) {
			$roles = Role::whereIn('id', $roleIds)
				->get();
		}

		return $roles;
	}

	public static function getUserRoleIds($userId)
	{
		$roleIds = [];
		$userRoles = self::where('user_id', $userId)->get();
		if ($userRoles != null && count($userRoles) > 0) {
			foreach ($userRoles as $userRole) {
				$roleIds[] =  $userRole->role_id;
			}
		}
		return $roleIds;
	}

	public static function setRoleIds($userId, $roleIds)
	{
		$currentRoleIds = self::getUserRoleIds($userId);
		$itemsToAdd = array_diff($roleIds, $currentRoleIds);
		$itemsToRemove = array_diff($currentRoleIds, $roleIds);

		if (count($itemsToRemove) > 0) {
			self::where('user_id', $userId)->whereIn('role_id', $itemsToRemove)->delete();
		}

		if (count($itemsToAdd) > 0) {
			$items = [];
			foreach ($itemsToAdd as $itemToAdd) {
				$items[] = ['user_id' => $userId, 'role_id' => $itemToAdd];
			}

			self::insert($items);
		}
	}
}
