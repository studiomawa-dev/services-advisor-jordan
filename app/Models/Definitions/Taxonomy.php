<?php

namespace App\Models\Definitions;

use Eloquent as Model;

/**
 * Class Taxonomy
 * @package App\Models\Definitions
 * @version May 11, 2019, 10:41 am UTC
 *
 * @property string key
 * @property integer order
 * @property boolean deleted
 */
class Taxonomy extends Model
{

    public $table = 'taxonomy';

    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'key',
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
        'key' => 'string',
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
        return $this->hasMany('App\Models\Definitions\TaxonomyLang');
    }

    public static function allLang()
    {
        $defaultLang = \App\Models\Settings\Language::defaultLang();
        $defaultLangId = $defaultLang == null ? 0 : $defaultLang->id;

        $items = \App\Models\Definitions\Taxonomy::where('deleted', 0)->get();

        foreach ($items as $item) {
            $taxonomyLang = $item->langs()->where('lang_id', $defaultLangId)->where('deleted', 0)->first();
            if ($taxonomyLang != null) {
                $item->name = $taxonomyLang->name;
            }
        }

        return $items;
    }


    public function scopeWithAndWhereHas($query, $relation, $constraint)
    {
        return $query->whereHas($relation, $constraint)
            ->with([$relation => $constraint]);
    }
}
