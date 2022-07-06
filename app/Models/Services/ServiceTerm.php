<?php

namespace App\Models\Services;

use Eloquent as Model;
use App\Models\Definitions\Term;

/**
 * Class ServiceTerm
 * @package App\Models\Services
 * @version May 31, 2019, 4:47 pm UTC
 *
 * @property integer service_id
 * @property integer term_id
 * @property integer taxonomy_id
 */
class ServiceTerm extends Model
{

	public $table = 'service_term';

	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';



	public $fillable = [
		'service_id',
		'term_id',
		'taxonomy_id'
	];

	/**
	 * The attributes that should be casted to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'id' => 'integer',
		'service_id' => 'integer',
		'term_id' => 'integer',
		'taxonomy_id' => 'integer'
	];

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public static $rules = [];

	public function term()
	{
		return $this->belongsTo('App\Models\Definitions\Term');
	}

	public static function getServiceTermIds($serviceId)
	{
		$termIds = [];
		$serviceTerms = self::where('service_id', $serviceId)->get();
		if ($serviceTerms != null && count($serviceTerms) > 0) {
			foreach ($serviceTerms as $serviceTerm) {
				$termIds[] =  $serviceTerm->term_id;
			}
		}
		return $termIds;
	}

	public static function setServiceTermIds($serviceId, $termIds)
	{
		$currentTermIds = self::getServiceTermIds($serviceId);
		$itemsToAdd = array_diff($termIds, $currentTermIds);
		$itemsToRemove = array_diff($currentTermIds, $termIds);

		if (count($itemsToRemove) > 0) {
			self::where('service_id', $serviceId)->whereIn('term_id', $itemsToRemove)->delete();
		}

		if (count($itemsToAdd) > 0) {
			$items = [];
			foreach ($itemsToAdd as $itemToAdd) {
				$term = Term::where('id', $itemToAdd)->firstOrFail();
				$items[] = ['service_id' => $serviceId, 'term_id' => $itemToAdd, 'taxonomy_id' => $term->taxonomy_id];
			}

			self::insert($items);
		}
	}
}
