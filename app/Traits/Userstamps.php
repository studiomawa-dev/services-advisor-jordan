<?php

namespace App\Traits;

trait Userstamps
{
    /**
     * Whether we're currently maintaing userstamps.
     *
     * @param bool
     */
    protected $userstamping = true;
    /**
     * Boot the userstamps trait for a model.
     *
     * @return void
     */
    public static function bootUserstamps()
    {
        static::registerListeners();
    }
    /**
     * Register events we need to listen for.
     *
     * @return void
     */
    public static function registerListeners()
    {
        static::creating('App\Traits\Listeners\Creating@handle');
        static::updating('App\Traits\Listeners\Updating@handle');
        if (static::usingSoftDeletes()) {
            static::deleting('App\Traits\Listeners\Deleting@handle');
            static::restoring('App\Traits\Listeners\Restoring@handle');
        }
    }
    /**
     * Has the model loaded the SoftDeletes trait.
     *
     * @return bool
     */
    public static function usingSoftDeletes()
    {
        static $usingSoftDeletes;
        if (is_null($usingSoftDeletes)) {
            return $usingSoftDeletes = in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive(get_called_class()));
        }
        return $usingSoftDeletes;
    }
    /**
     * Get the user that created the model.
     */
    public function creator()
    {
        return $this->belongsTo($this->getUserClass(), $this->getCreatedByColumn());
    }
    /**
     * Get the user that edited the model.
     */
    public function editor()
    {
        return $this->belongsTo($this->getUserClass(), $this->getUpdatedByColumn());
    }
    /**
     * Get the user that deleted the model.
     */
    public function destroyer()
    {
        return $this->belongsTo($this->getUserClass(), $this->getDeletedByColumn());
    }
    /**
     * Get the name of the "created by" column.
     *
     * @return string
     */
    public function getCreatedByColumn()
    {
        return defined('static::CREATED_BY') ? static::CREATED_BY : 'created_by';
    }
    /**
     * Get the name of the "updated by" column.
     *
     * @return string
     */
    public function getUpdatedByColumn()
    {
        return defined('static::UPDATED_BY') ? static::UPDATED_BY : 'updated_by';
    }
    /**
     * Get the name of the "deleted by" column.
     *
     * @return string
     */
    public function getDeletedByColumn()
    {
        return defined('static::DELETED_BY') ? static::DELETED_BY : 'deleted_by';
    }
    /**
     * Check if we're maintaing Userstamps on the model.
     *
     * @return bool
     */
    public function isUserstamping()
    {
        return $this->userstamping;
    }
    /**
     * Stop maintaining Userstamps on the model.
     *
     * @return void
     */
    public function stopUserstamping()
    {
        $this->userstamping = false;
    }
    /**
     * Start maintaining Userstamps on the model.
     *
     * @return void
     */
    public function startUserstamping()
    {
        $this->userstamping = true;
    }
    /**
     * Get the class being used to provide a User.
     *
     * @return string
     */
    protected function getUserClass()
    {
        if (get_class(auth()) === 'Illuminate\Auth\Guard') {
            return auth()->getProvider()->getModel();
        }
        return auth()->guard()->getProvider()->getModel();
    }
}
