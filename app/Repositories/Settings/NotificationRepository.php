<?php

namespace App\Repositories\Settings;

use App\Models\Settings\Language;
use App\Models\Settings\Notification;
use App\Models\Settings\NotificationLang;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use stdClass;

/**
 * Class NotificationRepository
 * @package App\Repositories\Settings
 * @version May 27, 2019, 1:59 pm UTC
 */

class NotificationRepository extends BaseRepository
{
	/**
     * @var array
     */
    protected $fieldSearchable = [
        'title',
        'message'
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
        return Notification::class;
	}

	public function getCount()
	{
		$query = $this->model->newQuery();

		$result = $query
			->whereNull('deleted_at')
			->count();

		return $result;
	}



	public function getAll($langCode)
	{
		$lang = Language::getLangByCode($langCode);
		$langId = $lang == null ? Language::defaultLang()->id : $lang->id;
		$sql = "SELECT
					N.id, N.sending_date, N.is_sent, NL.title, NL.message, NL.payload
				FROM `notification` N
				LEFT JOIN  `notification_lang` NL ON NL.id = (SELECT NL2.id FROM `notification_lang` NL2 WHERE NL2.notification_id = N.id AND ((NL2.lang_id = $langId AND LENGTH(NL2.title)>0) OR LENGTH(NL2.title)>0) LIMIT 1)
				WHERE N.deleted_at IS NULL";

		$result = DB::select($sql);

		return $result;
	}

	public function getById($id, $langRepository) {
		$notification = $this->getWithData($id);

		$langs = $langRepository->all();

		if (empty($notification)) {
			return null;
		}

		if (!isset($notification->langs) || $notification->langs == null) {
			$notification->langs = [];
		}
		$notificationLangs = $notification->langs;

		foreach ($langs as $lang) {
			$currentNotificationLang = null;
			foreach ($notificationLangs as $notificationLang) {
				if ($notificationLang->lang_id == $lang->id) {
					$currentNotificationLang = $notificationLang;
				}
			}

			if ($currentNotificationLang == null) {
				$currentNotificationLang = new \stdClass();
				$currentNotificationLang->title = '';
				$currentNotificationLang->lang_id = $lang->id;
			}
			$notification->langs['l' . $lang->id] = $currentNotificationLang;
		}

		foreach ($notificationLangs as $key => $value) {
			if(strpos($key, "l") === false) {
				unset($notificationLangs[$key]);
			}
		}

		return $notification;
	}

	public function getWithData($id)
	{
		return $this->model()::with('langs')->find($id);
	}

	public function getFull($id)
	{
		return $this->model()::with('langs')->find($id);
	}

	public function setSent($notification, $response) {
		$notificationId = $notification->id;
		$messages = $response->getItems();
		foreach ($messages as $message) {
			if($message->isSuccess()) {
				$target = $message->target();
				$result = $message->result();
				$targetLang = $target->value();
				$targetLangId = intval(explode('-',$targetLang)[1]);



				if($result != null && isset($result['name']) && strlen($result['name'])) {
					$reportName = $result['name'];
					$affectedRows = DB::update('UPDATE notification_lang SET report_name = ? WHERE notification_id = ? AND lang_id = ?;', [$reportName, $notificationId, $targetLangId]);
				}
			}
		}

		DB::update('UPDATE `notification` SET is_sent = 1, sending_date = ? WHERE id = ?', [date('Y-m-d H:i:s'), $notificationId]);
	}

	public function saveLangs($input, $model)
	{
		$langs = $input['langs'];

		if ($langs != null && is_array($langs) && count($langs) > 0) {
			foreach ($langs as $key => $lang) {
				$langId = intval(str_replace('l', '', $key));
				$notificationLang = [];
				$notificationLang['title'] = ($lang['title'] == null) ? '' : $lang['title'];
				$notificationLang['message'] = $lang['message'];
				$notificationLang['payload'] = $lang['payload'];

				$item = NotificationLang::updateOrCreate(['notification_id' => $model->id, 'lang_id' => $langId], $notificationLang);
			}
		}

		return $model;
	}

	public function getRecents($langCode, $count)
	{
		$lang = Language::getLangByCode($langCode);
		$langId = $lang == null ? Language::defaultLang()->id : $lang->id;
		$sql = "SELECT N.id, N.sending_date, N.is_sent, NL.title, NL.message, NL.payload
			FROM `notification_lang` NL
			LEFT JOIN `notification` N ON N.id = NL.notification_id
			WHERE NL.lang_id = $langId AND N.deleted_at IS NULL AND NL.title IS NOT NULL AND LENGTH(NL.title)>0
			ORDER BY N.sending_date DESC
			LIMIT $count";

		$result = DB::select($sql);

		return $result;
	}

	/*
	public function getRecents($langCode, $count)
	{
		$lang = Language::getLangByCode($langCode);
		$langId = $lang == null ? Language::defaultLang()->id : $lang->id;
		$sql = "SELECT N.id, N.sending_date, N.is_sent, NL.title, NL.message, NL.payload
				FROM `notification` N
				LEFT JOIN `notification_lang` NL ON NL.id = (
					SELECT id FROM (
						SELECT NL2.* FROM `notification_lang` NL2  WHERE NL2.notification_id = N.id AND
								(NL2.lang_id = $langId AND LENGTH(NL2.title)>0)
						UNION ALL
						SELECT NL3.* FROM `notification_lang` NL3  WHERE NL3.notification_id = N.id AND
								(LENGTH(NL3.title)>0)
					) idq LIMIT 1
				)
				WHERE N.deleted_at IS NULL
				ORDER BY N.created_at, N.updated_at
				LIMIT $count";

		$result = DB::select($sql);

		return $result;
	}
	*/
}
