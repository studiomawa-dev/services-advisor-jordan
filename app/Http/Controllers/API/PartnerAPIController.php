<?php

namespace App\Http\Controllers\API;

use Cache;
use Response;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Repositories\Settings\PartnerRepository;

/**
 * Class PartnerController
 * @package App\Http\Controllers\API
 */

class PartnerAPIController extends AppBaseController
{
    /** @var  PartnerRepository */
    private $partnerRepository;

    public function __construct(PartnerRepository $partnerRepo)
    {
        $this->partnerRepository = $partnerRepo;
    }

    /**
     * Display a listing of the Partner with id and title.
     * GET|HEAD /api/partners/list
     *
     * @param Request $request
     * @return Response
     */
    public function list(Request $request)
    {
        $langId = $request->get('lang');
        return Cache::remember('partners-list', 60, function () use ($langId) {
            $partners = $this->partnerRepository->getPartnersForSelect($langId);
            return $this->sendResponse($partners, 'Partners retrieved successfully');
        });
    }

    /**
     * Display a filtered listing of the Partner.
     * GET|HEAD /api/partners/filter
     *
     * @param Request $request
     * @return Response
     */
    public function filter(Request $request)
    {
        return Cache::remember('partners-filter', 60, function () use ($request) {
            $partners = $this->partnerRepository->all(
                $request->except(['skip', 'limit']),
                $request->get('skip'),
                $request->get('limit')
            );
            return $this->sendResponse($partners->toArray(), 'Partners retrieved successfully');
        });
    }
}
