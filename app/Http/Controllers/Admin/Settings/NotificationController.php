<?php

namespace App\Http\Controllers\Admin\Settings;

use Auth;
use Flash;
use Response;
use Request;
use App\DataTables\Settings\NotificationDataTable;
use App\Http\Requests\Settings;
use App\Http\Requests\Settings\CreateNotificationRequest;
use App\Http\Requests\Settings\UpdateNotificationRequest;
use App\Repositories\Settings\NotificationRepository;
use App\Http\Controllers\AppBaseController;
use App\Repositories\Definitions\TermRepository;
use App\Repositories\Contents\MediaRepository;
use App\Repositories\Settings\LanguageRepository;
use App\Models\Services\Service;
use App\Models\Settings\Language;
use App\Models\Settings\User;
use App\Models\Settings\UserNotification;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use App\Models\Settings\Log;

class NotificationController extends AppBaseController
{
	/** @var  NotificationRepository */
	private $notificationRepository;

	/** @var  TermRepository */
	private $termRepository;

	/** @var  MediaRepository */
	private $mediaRepository;

	/** @var  LanguageRepository */
	private $langRepository;

	/** @var  Messaging */
	private $messaging;

	public function __construct(
		NotificationRepository $notificationRepo,
		TermRepository $termRepo,
		MediaRepository $mediaRepo,
		LanguageRepository $langRepo,
		Messaging $messaging
	) {
		$this->middleware('auth');
		$this->notificationRepository = $notificationRepo;
		$this->termRepository = $termRepo;
		$this->mediaRepository = $mediaRepo;
		$this->langRepository = $langRepo;
		$this->messaging = $messaging;
	}

	/**
	 * Display a listing of the Notification.
	 *
	 * @param NotificationDataTable $notificationDataTable
	 * @return Response
	 */
	public function index(NotificationDataTable $notificationDataTable)
	{
		$src = Request::get('src');

		$pageData = array(
			'src' => $src,
		);

		return $notificationDataTable->render('settings.notifications.index', $pageData);
	}

	/**
	 * Show the form for creating a new Notification.
	 *
	 * @return Response
	 */
	public function create()
	{
		if (!Auth::user()->isAdmin()) {
			return redirect(route('settings.notifications.index'));
		}

		$notification = new \stdClass();
		$langs = $this->langRepository->all();

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

		if (empty($notification)) {
			Flash::error('Notification not found');

			return redirect(route('settings.notifications.index'));
		}

		return view('settings.notifications.create')
			->with('notification', $notification)
			->with('langs', $langs);
	}

	/**
	 * Store a newly created Notification in storage.
	 *
	 * @param CreateNotificationRequest $request
	 *
	 * @return Response
	 */
	public function store(CreateNotificationRequest $request)
	{
		$input = $request->all();

		$notification = $this->notificationRepository->create($input);
		$this->notificationRepository->saveLangs($input, $notification);

		$result = $this->sendMessage($notification->id);

		if($result) {
			$this->notificationRepository->setSent($notification, $result);
		}

		Flash::success('Notification saved successfully.');
		Log::info('Notification', 'Create', 'Notification saved successfully.');

		return redirect(route('settings.notifications.index'));
	}

	/**
	 * Display the specified Notification.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function show($id)
	{
		$notification = $this->notificationRepository->getById($id, $this->langRepository);
		$langs = $this->langRepository->all();

		if (empty($notification)) {
			Flash::error('Notification not found');

			return redirect(route('settings.notifications.index'));
		}

		return view('settings.notifications.show')
			->with('notification', $notification)
			->with('langs', $langs);
	}

	/**
	 * Show the form for editing the specified Notification.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function edit($id)
	{
		if (!Auth::user()->isAdmin()) {
			return redirect(route('settings.notifications.index'));
		}

		$notification = $this->notificationRepository->getById($id, $this->langRepository);
		$langs = $this->langRepository->all();

		if (empty($notification)) {
			Flash::error('Notification not found');

			return redirect(route('settings.notifications.index'));
		}

		return view('settings.notifications.edit')
			->with('notification', $notification)
			->with('langs', $langs);
	}

	/**
	 * Update the specified Notification in storage.
	 *
	 * @param  int              $id
	 * @param UpdateNotificationRequest $request
	 *
	 * @return Response
	 */
	public function update($id, UpdateNotificationRequest $request)
	{
		$notification = $this->notificationRepository->find($id);

		if (empty($notification)) {
			Flash::error('Notification not found');

			return redirect(route('settings.notifications.index'));
		}

		$input = $request->all();

		$notification->update($input);
		$notification->touch();

		$this->notificationRepository->saveLangs($input, $notification);

		Flash::success('Notification updated successfully.');
		Log::info('Notification', 'Update', 'Notification updated successfully.');

		return redirect(route('settings.notifications.index'));
	}

	/**
	 * Remove the specified Notification from storage.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function destroy($id)
	{
		$notification = $this->notificationRepository->find($id);

		if (empty($notification)) {
			Flash::error('Notification not found');

			return redirect(route('settings.notifications.index'));
		}

		$this->notificationRepository->delete($id);

		Flash::success('Notification deleted successfully.');
		Log::warning('Notification', 'Delete', 'Notification deleted successfully.');

		return redirect(route('settings.notifications.index'));
	}

	private function sendMessage($id) {

		$notification = $this->notificationRepository->getById($id, $this->langRepository);

		if (empty($notification)) {
			return null;
		}

		$messages = [];

		foreach ($notification->langs as $notificationLang) {
			if(isset($notificationLang->title) && strlen($notificationLang->title) > 0) {
				$title = $notificationLang->title;
				$body = $notificationLang->message;
				$topic = 'lang-'.$notificationLang->lang_id;

				$notificationMessage = \Kreait\Firebase\Messaging\Notification::fromArray([
					'title' => $title,
					'body' => $body,
				]);

				$message = CloudMessage::new();
				$message = $message->withTarget('topic', $topic);
				$message = $message->withNotification($notificationMessage);

				array_push($messages, $message);
			}
		}

		$sendReport = $this->messaging->sendAll($messages);

		return $sendReport;
	}
}
