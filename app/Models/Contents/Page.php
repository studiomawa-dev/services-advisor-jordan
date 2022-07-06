<?php

namespace App\Models\Contents;

use Eloquent as Model;

/**
 * Class Page
 * @package App\Models\Contents
 * @version May 27, 2019, 2:06 pm UTC
 *
 * @property integer author_id
 * @property integer category_id
 * @property string title
 * @property string seo_title
 * @property string excerpt
 * @property string body
 * @property string image
 * @property string slug
 * @property string meta_description
 * @property string meta_keywords
 * @property string status
 * @property boolean featured
 */
class Page extends Model
{

    public $table = 'page';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'author_id',
        'category_id',
        'title',
        'seo_title',
        'excerpt',
        'body',
        'image',
        'slug',
        'meta_description',
        'meta_keywords',
        'status',
        'featured'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'author_id' => 'integer',
        'category_id' => 'integer',
        'title' => 'string',
        'seo_title' => 'string',
        'excerpt' => 'string',
        'body' => 'string',
        'image' => 'string',
        'slug' => 'string',
        'meta_description' => 'string',
        'meta_keywords' => 'string',
        'status' => 'string',
        'featured' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'author_id' => 'required',
        'title' => 'required',
        'body' => 'required',
        'slug' => 'required',
        'status' => 'required',
        'featured' => 'required'
    ];

    
}
