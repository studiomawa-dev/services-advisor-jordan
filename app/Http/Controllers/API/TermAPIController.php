<?php

namespace App\Http\Controllers\API;

use Response;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Repositories\Definitions\TermRepository;

/**
 * Class TermController
 * @package App\Http\Controllers\API
 */

class TermAPIController extends AppBaseController
{
	/** @var  TermRepository */
	private $termRepository;

	public function __construct(TermRepository $termRepo)
	{
		$this->termRepository = $termRepo;
	}

	/**
	 * Display a listing of the Category Terms.
	 * GET|HEAD /api/terms/categories
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function categories(Request $request)
	{
		$langCode = $request->get('lang');
		$terms = $this->termRepository->getCategoryTerms($langCode);
		return $this->sendResponse($terms, 'Terms retrieved successfully');
	}

	/**
	 * Display a listing of the Location Terms.
	 * GET|HEAD /api/terms/locations
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function locations(Request $request)
	{
		$langCode = $request->get('lang');
		$terms = $this->termRepository->getLocationTerms($langCode);
		return $this->sendResponse($terms, 'Terms retrieved successfully');
	}

	/**
	 * Display a listing of the Nationality Terms.
	 * GET|HEAD /api/terms/nationalities
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function nationalities(Request $request)
	{
		$langCode = $request->get('lang');
		$terms = $this->termRepository->getNationalityTerms($langCode);
		return $this->sendResponse($terms, 'Terms retrieved successfully');
	}

	/**
	 * Display a filtered listing of the Term.
	 * GET|HEAD /api/terms/filter
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function filter(Request $request)
	{
		$terms = $this->termRepository->all(
			$request->except(['skip', 'limit']),
			$request->get('skip'),
			$request->get('limit')
		);
		return $this->sendResponse($terms->toArray(), 'Terms retrieved successfully');
	}
}
