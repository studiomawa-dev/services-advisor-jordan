<?php

namespace App\Repositories\Settings;

use App\Models\Settings\User;
use App\Models\Settings\UserRole;
use App\Repositories\BaseRepository;
use App\Models\Settings\UserPartner;

/**
 * Class UserRepository
 * @package App\Repositories\Settings
 * @version May 27, 2019, 8:25 pm UTC
 */

class UserRepository extends BaseRepository
{
	/**
	 * @var array
	 */
	protected $fieldSearchable = [
		'name',
		'username',
		'email',
		'email_verified_at',
		'password',
		'remember_token'
	];

	/**
	 * Return searchable fields
	 *
	 * @return array
	 */
	public function getFieldsSearchable()
	{
		return $this->fieldSearchable;
	}

	/**
	 * Configure the Model
	 **/
	public function model()
	{
		return User::class;
	}

	public function getUsersForSelect($placeholder = false)
	{
		$all_users = $this->model()::whereNull('deleted_at')->get();

		$users = [];
		if ($placeholder) {
			$users[null] = 'Select User';
		}
		foreach ($all_users as $user) {
			$users[$user->id . ''] = $user->name;
		}
		return $users;
	}

	public function getByUsername($username)
	{
		$user = $this->model->newQuery()
			->where('username', trim($username))
			//->whereNull('deleted_at')
			->with('photo')
			->first();

		if ($user != null) {
			$user->roles = UserRole::getUserRoles($user->id);
			$user->partners = UserPartner::getUserPartners($user->id);
		}

		return $user;
	}

	public function getByEmail($email) {
		$user = $this->model->newQuery()
			->where('email', trim($email))
			->with('photo')
			->first();

		if ($user != null) {
			$user->roles = UserRole::getUserRoles($user->id);
			$user->partners = UserPartner::getUserPartners($user->id);
		}

		return $user;
	}

	public function getCount()
	{
		$query = $this->model->newQuery();

		$result = $query
			->whereNull('deleted_at')
			->count();

		return $result;
	}


}
