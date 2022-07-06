<?php

namespace App\Models\Services;

use Eloquent as Model;

/**
 * Class ServiceHour
 * @package App\Models\Services
 * @version May 31, 2019, 4:46 pm UTC
 *
 * @property integer service_id
 * @property integer day
 * @property integer start_hour
 * @property integer end_hour
 */
class ServiceHour extends Model
{

    public $table = 'service_hour';

    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'service_id',
        'day',
        'start_hour',
        'end_hour'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'service_id' => 'integer',
        'day' => 'integer',
        'start_hour' => 'integer',
        'end_hour' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];

    public static function getServiceHours($serviceId)
    {
        $hours = [];
        $serviceHours = self::where('service_id', $serviceId)->get();
        if ($serviceHours != null && count($serviceHours) > 0) {
            foreach ($serviceHours as $serviceHour) {
                $hours[] =  $serviceHour;
            }
        }
        return $hours;
    }

    public static function setServiceHours($serviceId, $hours)
    {
        self::where('service_id', $serviceId)->delete();

        if (count($hours) > 0) {
            foreach ($hours as $hour) {
                $items[] = ['service_id' => $serviceId, 'day' => $hour->day, 'start_hour' => $hour->start_hour, 'end_hour' => $hour->end_hour];
            }

            self::insert($items);
        }
        /*

        $itemsToAdd = [];
        $itemsToRemove = [];
        $itemsToUpdate = [];

        $currentHours = self::getServiceHours($serviceId);

        if (count($currentHours) == 0 && count($hours) > 0) {
            $itemsToAdd = $hours;
        } else if (count($currentHours) > 0 && count($hours) == 0) {
            $itemsToRemove = $currentHours;
        } else if (count($currentHours) > 0 && count($hours) > 0) {
            $currentHoursDays = [];
            $hoursDays = [];

            foreach ($currentHours as $currentHour) {
                if (!in_array($currentHour->day, $currentHoursDays))
                    $currentHoursDays[] = $currentHour->day;
            }

            foreach ($hours as $hour) {
                if (!in_array($hour->day, $hoursDays))
                    $hoursDays[] = $hour->day;
            }



            $itemsToAddDays = array_diff($hoursDays, $currentHoursDays);
            $itemsToRemoveDays = array_diff($currentHoursDays, $hoursDays);
            $itemsToUpdateDays = array_intersect($currentHoursDays, $hoursDays);

            if (count($itemsToAddDays) > 0) {
                foreach ($itemsToAddDays as $itemsToAddDay) {
                    foreach ($hours as $hour) {
                        if ($hour->day == $itemsToAddDay) {
                            $itemsToAdd[] = $hour;
                        }
                    }
                }
            }

            if (count($itemsToRemoveDays) > 0) {
                foreach ($itemsToRemoveDays as $itemsToRemoveDay) {
                    foreach ($currentHours as $currentHour) {
                        if ($currentHour->day == $itemsToRemoveDay) {
                            $itemsToRemove[] = $currentHour;
                        }
                    }
                }
            }

            if (count($itemsToUpdateDays) > 0) {
                foreach ($currentHours as $currentHour) {
                    foreach ($hours as $hour) {
                        if ($currentHour->day == $hour->day && ($currentHour->start_hour != $hour->start_hour || $currentHour->end_hour != $hour->end_hour)) {
                            $itemsToUpdate[] = $currentHour;
                        }
                    }
                }
            }
        }

        if (count($itemsToRemove) > 0) {
            $itemsToRemoveIds = [];
            foreach ($itemsToRemove as $itemToRemove) {
                $itemsToRemoveIds[] = $itemToRemove->id;
            }
            if (count($itemsToRemoveIds) > 0) {
                self::where('service_id', $serviceId)->whereIn('id', $itemsToRemoveIds)->delete();
            }
        }

        if (count($itemsToAdd) > 0) {
            $items = [];
            foreach ($itemsToAdd as $itemToAdd) {
                $items[] = ['service_id' => $serviceId, 'day' => $itemToAdd->day, 'start_hour' => $itemToAdd->start_hour, 'end_hour' => $itemToAdd->end_hour];
            }

            self::insert($items);
        }

        if (count($itemsToUpdate) > 0) {
            $items = [];
            foreach ($itemsToUpdate as $itemToUpdate) {
                self::where('service_id', $serviceId)->where('day', $itemToUpdate->day)->update(['start_hour' => $itemToUpdate->start_hour, 'end_hour' => $itemToUpdate->end_hour]);
            }
        }

        */
    }
}
