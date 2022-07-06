<?php

namespace App\Models\Definitions;

use Eloquent as Model;

/**
 * Class Term
 * @package App\Models\Definitions
 * @version May 11, 2019, 10:41 am UTC
 *
 * @property integer taxonomy_id
 * @property integer parent_id
 * @property integer tag_id
 * @property string color
 * @property integer order
 * @property boolean deleted
 */
class Term extends Model
{

    public $table = 'term';

    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $level = 0;
    public $index = 0;
    public $child_terms = [];

    public $fillable = [
        'tag_id',
        'taxonomy_id',
        'parent_id',
        'color',
        'order',
        'deleted'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'tag_id' => 'integer',
        'taxonomy_id' => 'integer',
        'color' => 'string',
        'order' => 'integer',
        'deleted' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'order' => 'required',
        'deleted' => 'required'
    ];

    public function langs()
    {
        return $this->hasMany('App\Models\Definitions\TermLang');
    }

    public static function getRootCategoryTermIds()
    {
        $termIds = [];
        $terms = self::where('taxonomy_id', 12)
            ->where('parent_id', 0)
            ->where('deleted', 0)
            ->get();

        if ($terms != null && count($terms) > 0) {
            foreach ($terms as $term) {
                $termIds[] =  $term->id;
            }
        }
        return $termIds;
    }

    public static function getParents($term_id, $termIds = [])
    {
        $term = self::where('id', (int) $term_id)->first();

        if ((int) $term->parent_id > 0) {
            $termIds[] = (int) $term->parent_id;
            return self::getParents($term->parent_id, $termIds);
        }

        return $termIds;
    }

    public static function getParentTagType($term_id)
    {
        $term = self::where('id', (int) $term_id)->first();
        if ($term->parent_id) {
            $subTerms = self::where('parent_id', (int) $term->parent_id)->where('deleted', 0)->get();
            $hasOne = false;
            $hasTwo = false;
            foreach ($subTerms as $subTerm) {
                if ($subTerm->tag_id == 3) {
                    $hasOne = true;
                    $hasTwo = true;
                } else
                if ($subTerm->tag_id == 1) $hasOne = true;
                else
                if ($subTerm->tag_id == 2) $hasTwo = true;
            }

            if ($hasOne && $hasTwo) {
                return 3;
            } elseif ($hasOne) {
                return 1;
            } elseif ($hasTwo) {
                return 2;
            }
        }

        return null;
    }

    public static function setParentTagType($term_id)
    {
        $term = Term::find($term_id);

        if ($term->parent_id) {
            $parentTagType = Term::getParentTagType($term_id);

            $parentTerm = Term::find($term->parent_id);
            $parentTerm->tag_id = $parentTagType;
            $parentTerm->save();

            self::setParentTagType($parentTerm->id);
        }
    }
}
