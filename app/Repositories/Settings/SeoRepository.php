<?php

namespace App\Repositories\Settings;

use App\Models\Settings\Config;
use App\Repositories\BaseRepository;

/**
 * Class RoleRepository
 * @package App\Repositories\Settings
 * @version May 10, 2019, 9:05 pm UTC
 */

class SeoRepository extends BaseRepository
{
	/**
	 * @var array
	 */
	protected $fieldSearchable = [
		'name',
		'title'
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
		return Config::class;
	}

	public function getSeoConfig()
	{
		return $this->model->where('type', 'seo')->orderBy('title', 'asc')->get();
	}

	public function getValue(){
		return 'asd';
	}
}
