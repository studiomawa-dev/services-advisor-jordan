<?php

namespace App\Http\Controllers\API;

use Cache;
use Response;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Repositories\Settings\NotificationRepository;

/**
 * Class NotificationAPIController
 * @package App\Http\Controllers\API
 */

class NotificationAPIController extends AppBaseController
{
    /** @var  NotificationRepository */
    private $notificationRepository;

    public function __construct(NotificationRepository $notificationRepo)
    {
        $this->notificationRepository = $notificationRepo;
    }

    /**
     * Display a listing of the Notification with id and title.
     * GET|HEAD /api/notifications/list
     *
     * @param Request $request
     * @return Response
     */
    public function list(Request $request)
    {
		$langCode = $request->get('lang');


		$notifications = $this->notificationRepository->getRecents($langCode, 20);
		return $this->sendResponse($notifications, 'Notifications retrieved successfully');

        return Cache::remember('notifications-list-'.$langCode, 60, function () use ($langCode) {
            $notifications = $this->notificationRepository->getRecents($langCode, 20);
			return $this->sendResponse($notifications, 'Notifications retrieved successfully');
        });
    }
}
