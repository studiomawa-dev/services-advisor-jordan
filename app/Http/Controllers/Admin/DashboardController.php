<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\Services\ServiceDataTable;
use App\Repositories\Services\ServiceRepository;
use App\Repositories\Definitions\TermRepository;
use App\Repositories\Settings\PartnerRepository;
use App\Repositories\Services\LocationRepository;
use App\Repositories\Settings\UserRepository;

class DashboardController extends Controller
{

	/** @var  ServiceRepository */
	private $serviceRepository;

	/** @var  TermRepository */
	private $termRepository;

	/** @var  PartnerRepository */
	private $partnerRepository;

	/** @var  LocationRepository */
	private $locationRepository;

	/** @var  UserRepository */
	private $userRepository;


	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(
		ServiceRepository $serviceRepo,
		TermRepository $termRepo,
		PartnerRepository $partnerRepo,
		LocationRepository $locationRepo,
		UserRepository $userRepo
	) {
		$this->middleware('auth');
		$this->serviceRepository = $serviceRepo;
		$this->termRepository = $termRepo;
		$this->partnerRepository = $partnerRepo;
		$this->locationRepository = $locationRepo;
		$this->userRepository = $userRepo;
	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$services = $this->serviceRepository->getLastServices();
		$serviceCategoryCounts = $this->serviceRepository->getCategoryCounts();
		$serviceCount = $this->serviceRepository->getCount();
		$partnerCount = $this->partnerRepository->getCount();
		$locationCount = $this->locationRepository->getCount();
		$userCount = $this->userRepository->getCount();

		$recentPartners = $this->partnerRepository->getRecents(10);

		foreach ($recentPartners as $recentPartner) {
			if ($recentPartner->created_at == null) $recentPartner->created_at = 0;
			if ($recentPartner->updated_at == null) $recentPartner->updated_at = 0;
			if (
				strtotime($recentPartner->created_at) > strtotime($recentPartner->updated_at) ||
				strtotime($recentPartner->created_at) == strtotime($recentPartner->updated_at)
			) {
				if ($recentPartner->creator == null) continue;
				$recentPartner->statusText = __('app.Organization created');
				$recentPartner->actionTime = strtotime($recentPartner->created_at);
				$recentPartner->user = $recentPartner->creator;
			} else {
				if ($recentPartner->editor == null) continue;
				$recentPartner->statusText = __('app.Organization updated');
				$recentPartner->actionTime = strtotime($recentPartner->updated_at);
				$recentPartner->user = $recentPartner->editor;
			}
			$recentPartner->itemText = $recentPartner->langs[0]->name;
			$recentPartner->itemLink = '/admin/settings/partners/' . $recentPartner->id;
		}

		$recentLocations = $this->locationRepository->getRecents(10);
		foreach ($recentLocations as $recentLocation) {
			if ($recentLocation->created_at == null) $recentLocation->created_at = 0;
			if ($recentLocation->updated_at == null) $recentLocation->updated_at = 0;
			if (
				strtotime($recentLocation->created_at) > strtotime($recentLocation->updated_at) ||
				strtotime($recentLocation->created_at) == strtotime($recentLocation->updated_at)
			) {
				if ($recentLocation->creator == null) continue;
				$recentLocation->statusText = __('app.Location created');
				$recentLocation->actionTime = strtotime($recentLocation->created_at);
				$recentLocation->user = $recentLocation->creator;
			} else {
				if ($recentLocation->editor == null) continue;
				$recentLocation->statusText = __('app.Location updated');
				$recentLocation->actionTime = strtotime($recentLocation->updated_at);
				$recentLocation->user = $recentLocation->editor;
			}
			$recentLocation->itemText = $recentLocation->langs[0]->name;
			$recentLocation->itemLink = '/admin/services/locations/' . $recentLocation->id;
		}

		$recentServices = $this->serviceRepository->getRecents(10);
		foreach ($recentServices as $recentService) {
			try {
				if ($recentService->created_at == null) $recentService->created_at = 0;
				if ($recentService->updated_at == null) $recentService->updated_at = 0;
				if (
					strtotime($recentService->created_at) > strtotime($recentService->updated_at) ||
					strtotime($recentService->created_at) == strtotime($recentService->updated_at)
				) {
					if ($recentService->creator == null) continue;
					$recentService->statusText = __('app.Service created');
					$recentService->actionTime = strtotime($recentService->created_at);
					$recentService->user = $recentService->creator;
				} else {
					if ($recentService->editor == null) continue;
					$recentService->statusText = __('app.Service updated');
					$recentService->actionTime = strtotime($recentService->updated_at);
					$recentService->user = $recentService->editor;
				}
				if ($recentService->langs != null && count($recentService->langs) > 0 && $recentService->langs[0] != null) {
					$recentService->itemText = $recentService->langs[0]->name;
				} else {
					$recentService->itemText = $recentService->id;
				}
				$recentService->itemLink = '/admin/services/services/' . $recentService->id;
			} catch (Exception $e) {
			}
		}

		$updates = $recentServices->merge($recentLocations->merge($recentPartners))->where('user', '<>', null)->sortByDesc('actionTime')->take(10);

		return view('dashboard')
			->with('serviceCount', $serviceCount)
			->with('partnerCount', $partnerCount)
			->with('locationCount', $locationCount)
			->with('userCount', $userCount)
			->with('updates', $updates)
			->with('serviceCategoryCounts', $serviceCategoryCounts)
			->with('services', $services);
	}
}
