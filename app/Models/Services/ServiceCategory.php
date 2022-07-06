<?php

namespace App\Models\Services;

use Eloquent as Model;
use App\Models\Definitions\Term;

/**
 * Class ServiceCategory
 * @package App\Models\Services
 * @version May 31, 2019, 4:47 pm UTC
 *
 * @property integer service_id
 * @property integer category_term_id
 * @property integer category_no
 * @property boolean deleted
 */
class ServiceCategory extends Model
{
    public $table = 'service_category';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

	public $timestamps = false;

    public $fillable = [
        'service_id',
		'category_term_id',
		'category_no',
		'deleted'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'service_id' => 'integer',
        'category_term_id' => 'integer',
        'category_no' => 'integer',
        'deleted' => 'boolean',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

	public function term()
	{
		return $this->belongsTo('App\Models\Definitions\Term', 'category_term_id');
	}

	public static function getServiceCategoryTermIds($serviceId, $withMaxNo = false)
	{
		$categoryNo = 0;
		$termIds = [];
		$serviceTerms = self::where('service_id', $serviceId)->where('deleted', 0)->get();
		if ($serviceTerms != null && count($serviceTerms) > 0) {
			foreach ($serviceTerms as $serviceTerm) {
				$termIds[] =  $serviceTerm->category_term_id;
				$categoryNo = max($categoryNo, $serviceTerm->category_no);
			}
		}

		if($withMaxNo) {
			return [$termIds, $categoryNo];
		}

		return $termIds;
	}

	public static function setServiceCategoryTermIds($serviceId, $termIds)
	{
		$rootTerms = Term::getRootCategoryTermIds();

		$serviceRootTerms = array_intersect($rootTerms, $termIds);

		$data = self::getServiceCategoryTermIds($serviceId, true);
		$currentTermIds = $data[0];
		$categoryNo = $data[1];

		$itemsToAdd = array_diff($serviceRootTerms, $currentTermIds);
		$itemsToRemove = array_diff($currentTermIds, $serviceRootTerms);

		if (count($itemsToRemove) > 0) {
			self::where('service_id', $serviceId)->whereIn('category_term_id', $itemsToRemove)->update(['deleted' => 1]);
		}

		if (count($itemsToAdd) > 0) {
			$items = [];
			foreach ($itemsToAdd as $itemToAdd) {
				$categoryNo++;
				$term = Term::where('id', $itemToAdd)->firstOrFail();
				$items[] = ['service_id' => $serviceId, 'category_term_id' => $itemToAdd, 'category_no' => $categoryNo, 'deleted' => 0];
			}

			self::insert($items);
		}
	}
}
