<?php

namespace App\Repositories\Settings;

use App\Models\Settings\Role;
use App\Repositories\BaseRepository;

/**
 * Class RoleRepository
 * @package App\Repositories\Settings
 * @version May 10, 2019, 9:05 pm UTC
 */

class RoleRepository extends BaseRepository
{
	/**
	 * @var array
	 */
	protected $fieldSearchable = [
		'name',
		'display_name'
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
		return Role::class;
	}

	public function getRolesForSelect($placeholder = false)
	{
		$all_roles = $this->model()::where('deleted', 0)->orWhereNull('deleted')->get();

		$roles = [];
		if ($placeholder) {
			$roles[null] = 'Select Role';
		}
		foreach ($all_roles as $role) {
			$roles[$role->id . ''] = $role->display_name;
		}
		return $roles;
	}
}
