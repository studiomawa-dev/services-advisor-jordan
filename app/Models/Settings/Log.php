<?php

namespace App\Models\Settings;

use Request;
use Eloquent as Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Log
 * @package App\Models\Settings
 * @version August 7, 2019, 6:42 pm UTC
 *
 * @property boolean level
 * @property string username
 * @property string ipaddress
 * @property string category
 * @property string type
 * @property string message
 */
class Log extends Model
{
    use SoftDeletes;
    public $table = 'log';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dateFormat = 'U';

    public $fillable = [
        'level',
        'username',
        'ipaddress',
        'category',
        'type',
        'message'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'level' => 'integer',
        'username' => 'string',
        'ipaddress' => 'string',
        'category' => 'string',
        'type' => 'string',
        'message' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];

    public static function info($category, $type = '', $message = '')
    {
        self::add(0, $category, $type, $message);
    }

    public static function warning($category, $type = '', $message = '')
    {
        self::add(1, $category, $type, $message);
    }

    public static function error($category, $type = '', $message = '')
    {
        self::add(2, $category, $type, $message);
    }

    private static function add($level, $category, $type = '', $message = '')
    {
        $user = Auth::user();
        $username = 'guest';
        if ($user != null && $user->name != null) {
            $username = $user->username;
        }
        $log = new Log();
        $log->level = $level;
        $log->ipaddress = Request::ip();
        $log->username = $username;
        $log->category = $category;
        $log->type = $type;
        $log->message = $message;
        $log->save();
    }
}
