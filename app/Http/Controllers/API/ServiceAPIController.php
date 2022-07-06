<?php

namespace App\Http\Controllers\API;

use App;
use Cache;
use Mail;
use Response;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Repositories\Services\ServiceRepository;
use App\Repositories\Services\ServiceContactRepository;
use App\Repositories\Definitions\TermRepository;
use App\Models\Settings\Language;
use App\Repositories\Settings\PartnerRepository;

/**
 * Class ServiceController
 * @package App\Http\Controllers\API
 */

class ServiceAPIController extends AppBaseController
{
	private $tokenSalt = 'JY5Z2nKUMRvHFt6saD';
	private $cacheExpireTime = 600; // seconds
	private $token = '';

	/** @var  ServiceRepository */
	private $serviceRepository;

	/** @var  ServiceContactRepository */
	private $serviceContactRepository;

	/** @var  TermRepository */
	private $termRepository;

	/** @var  PartnerRepository */
	private $partnerRepository;

	public function __construct(
		ServiceRepository $serviceRepo,
		ServiceContactRepository $serviceContactRepo,
		TermRepository $termRepo,
		PartnerRepository $partnerRepo
	) {
		$this->serviceRepository = $serviceRepo;
		$this->serviceContactRepository = $serviceContactRepo;
		$this->termRepository = $termRepo;
		$this->partnerRepository = $partnerRepo;

		$this->token = md5($this->tokenSalt . date('YmdHi'));
	}

	private function validateToken()
	{
		$token = request()->bearerToken();
		if ($token != $this->token) {
			return $this->sendError('Invalid Authorization', 403);
		}

		return true;
	}

	public function initialData(Request $request)
	{
		$lang = $request->get('lang');

		return Cache::remember('initial-data-' . $lang, $this->cacheExpireTime, function () use ($lang) {

			$data = array(
				'version' => $this->serviceRepository->getLastUpdateTime(),
				'terms' => $this->termRepository->getAll($lang),
				'partners' => $this->partnerRepository->getAll($lang)
			);
			//dd($data);
			return $this->sendResponse($data, '');
		});
	}

	/**
	 * Display a listing of the Service Location.
	 * GET|HEAD /api/services/coordinates
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function coordinates(Request $request)
	{
		return Cache::remember('service-coordinates', $this->cacheExpireTime, function () {
			$services = $this->serviceRepository->getCoordinates(null);
			return $this->sendResponse($services, '');
		});
	}

	/**
	 * Display a listing of the Service Location.
	 * GET|HEAD /api/services
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function services(Request $request)
	{
		$tokenValidation = $this->validateToken();
		if ($tokenValidation !== true) {
			return $tokenValidation;
		}

		$tag  = $request->get('tag');
		return Cache::remember('services-' . $tag, $this->cacheExpireTime, function () use ($tag) {
			$services = $this->serviceRepository->getCoordinatesByTag($tag);
			return $this->sendResponse($services, '');
		});
	}

	/**
	 * Display a listing of the Terms and Partners.
	 * GET|HEAD /api/data
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function data(Request $request)
	{
		$tokenValidation = $this->validateToken();
		if ($tokenValidation !== true) {
			return $tokenValidation;
		}

		$tag  = $request->get('tag');
		$lang = $request->get('lang');

		if ($tag && $lang) {
			return Cache::remember('data-' . '-' . $tag . '-' . $lang, $this->cacheExpireTime, function () use ($tag, $lang) {

				$data = array(
					'version' => $this->serviceRepository->getLastUpdateTime(),
					'terms' => $this->termRepository->getAllByTag($tag, $lang),
					'partners' => $this->partnerRepository->getAllByTag($tag, $lang)
				);
				return $this->sendResponse($data, '');
			});
		}

		$data = array(
			'version' => $this->serviceRepository->getLastUpdateTime(),
			'terms' => [],
			'partners' => []
		);
		return $this->sendResponse($data, '');
	}

	public function categories(Request $request)
	{
		$lang = $request->get('lang');
		$tag  = $request->get('tag');

		$data = $this->termRepository->getParentCategories($tag, $lang);
		return $this->sendResponse($data, '');
	}

	public function clusters(Request $request)
	{
		$rootCategories = $this->termRepository->getRootCategoryTermsForSelect();
		$bounds = $request->get('bounds');
		$zoom = intval($request->get('zoom'));
		$clusters = [];

		if (isset($bounds) && strlen($bounds) > 0) {
			$boundsArr = explode(',', $bounds);
			if ($boundsArr != null && count($boundsArr) == 4) {
				$clusters = $this->serviceRepository->getClusters($boundsArr, $zoom, $rootCategories);
			}
		}

		return $this->sendResponse($clusters, 'Data retrieved successfully');
	}

	/**
	 * Display a listing of the Service Location.
	 * GET|HEAD /api/services/list
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function terms(Request $request)
	{
		return Cache::remember('service-terms', $this->cacheExpireTime, function () {
			$services = $this->serviceRepository->getTerms();
			return $this->sendResponse($services, '');
		});
	}

	/**
	 * Display an item of the Service.
	 * GET|HEAD /api/services/item/{id}
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function item($id, Request $request)
	{
		$lang = $request->get('lang');
		$service = $this->serviceRepository->getItem($id, $lang);

		return $this->sendResponse($service, '');
	}

	/**
	 * Display a filtered listing of the Service.
	 * GET|HEAD /api/services/filter
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function filter(Request $request)
	{
		$services = $this->serviceRepository->all(
			$request->except(['skip', 'limit']),
			$request->get('skip'),
			$request->get('limit')
		);
		return $this->sendResponse($services->toArray(), 'Services retrieved successfully');
	}

	/**
	 * Display a version number.
	 * GET|HEAD /api/services/version
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function version(Request $request)
	{
		return Cache::remember('service-version', $this->cacheExpireTime, function () {
			$version = $this->serviceRepository->getLastUpdateTime();
			return $this->sendResponse($version, '');
		});
	}

	public function feedback(Request $request)
	{
		$serviceId = $request->get('serviceId');
		$serviceUrl = $request->get('serviceUrl');
		$categoryIds = $request->get('categoryIds');
		$feedbackId = $request->get('feedbackId');
		$note = $request->get('note');

		$emailSubject = '';
		$emailBody = '';
		$errorMessage = 'An error occurred!';

		if (intval($serviceId) > 0 && strlen($serviceUrl) > 0 && strlen($categoryIds) > 0 && intval($feedbackId) > 0 && strlen($note) < 200) {
			$serviceUrl = env('APP_URL', '') . $serviceUrl;
			$serviceId = intval($serviceId);
			$feedbackId = intval($feedbackId);
			$checkedCategoryIds = [];
			$rawCategoryIds = explode(',', $categoryIds);

			foreach ($rawCategoryIds as $rawCategoryId) {
				$rawCategoryId = intval($rawCategoryId);
				if ($rawCategoryId > 0) {
					array_push($checkedCategoryIds, $rawCategoryId);
				}
			}

			if (strlen($note) > 0) $note = trim(htmlspecialchars($note));

			switch ($feedbackId) {
				case 1:
					$emailSubject = 'Services Not Available!';
					break;

				case 2:
					$emailSubject = 'Opening Hours Incorrect!';
					break;

				case 3:
					$emailSubject = 'Services Location Incorrect!';
					break;

				case 4:
				default:
					$emailSubject = 'Other';
					break;
			}

			if (count($checkedCategoryIds) > 0) {
				$users = $this->serviceContactRepository->getServiceContactUsers($serviceId, $checkedCategoryIds);
				$emails = [];
				if ($users == null || count($users) == 0) {
					$users = array(
						array('email' => env('MAIL_USERNAME'))
					);
				}
				if (count($users)) {
					foreach ($users as $user) {
						if (!in_array($user['email'], $emails)) {
							array_push($emails, $user['email']);
						}
					}

					if (count($emails) > 0) {
						$data = array('serviceId' => $serviceId, 'serviceUrl' => $serviceUrl, 'reportType' => $emailSubject, 'note' => $note);
						Mail::send('services/services/feedbackmail', $data, function ($message) use ($emails, $emailSubject) {
							$message->to($emails)->subject($emailSubject);
							$message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
						});

						return $this->sendResponse($serviceId, '');
					}

					$errorMessage = 'Email address not found!';
				} else {
					$errorMessage = 'Contact not found!';
				}
			} else {
				$errorMessage = 'Service category not found!';
			}
		}

		return $this->sendError($errorMessage);
	}

	public function list(Request $request)
	{
		$token = \Request::header('x-api-key');

		if ($token === '1f524b9101593efc890f861d2a057068') {
			$dev = false;
			$langCode = $request->get('lang');
			$page = (int) $request->get('page');

			if ($page <= 0) {
				$page = 1;
			}


			$services = $this->serviceRepository->getList($langCode, $page);
			return $this->sendResponse($services, '');


			/*if ($token === '1f524b9101593efc890f861d2a057068')
			{
				return Cache::remember('api-services-list-' . $langCode . '-' . $page, 600, function () use ($langCode, $page) {
					$services = $this->serviceRepository->getList($langCode, $page);
					return $this->sendResponse($services, '');
				});
			}*/
		}

		return ['status' => false, 'error' => 'Invalid api username or token'];
	}
}
