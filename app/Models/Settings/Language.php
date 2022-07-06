<?php

namespace App\Models\Settings;

use Eloquent as Model;

/**
 * Class Language
 * @package App\Models\Settings
 * @version May 10, 2019, 10:23 pm UTC
 *
 * @property string name
 * @property string code
 * @property boolean is_default
 * @property boolean is_backend
 * @property boolean is_rtl
 * @property string fb_lang_code
 */
class Language extends Model
{

    public $table = 'language';

    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'name',
        'code',
        'is_default',
        'is_backend',
        'is_rtl',
        'fb_lang_code'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'code' => 'string',
        'is_default' => 'boolean',
        'is_backend' => 'boolean',
        'is_rtl' => 'boolean',
        'fb_lang_code' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];

    private static $defaultLang;
    public static function defaultLang()
    {
        if (self::$defaultLang == null) {
            $loc = app()->getLocale();

            $currentLanguage = \App\Models\Settings\Language::where('code', $loc)->first();
            //$currentLanguage = null;
            if ($currentLanguage == null) {
                $currentLanguage = \App\Models\Settings\Language::where('code', $loc)->orWhere('is_default', 1)->first();
            }

            self::$defaultLang = $currentLanguage;
        }

        return self::$defaultLang;
    }

    public static function getLangByCode($langCode)
    {
        $lang = \App\Models\Settings\Language::where('code', $langCode)->first();
        if ($lang == null) {
            $lang = \App\Models\Settings\Language::where('code', 'en')->first();
        }
        return $lang;
    }

    public static function getAll()
    {
        return \App\Models\Settings\Language::get();
    }

    public static function getBackendLangs()
    {
        return \App\Models\Settings\Language::where('is_backend', 1)->get();
    }
}
