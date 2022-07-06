<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Requests\Settings\UpdateSeoRequest;
use App\Repositories\Settings\SeoRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Response;

class SeoController extends AppBaseController
{
	/** @var  SeoRepository */
	private $seoRepository;

	public function __construct(SeoRepository $seoRepo)
	{
		$this->middleware('auth');
		$this->seoRepository = $seoRepo;
	}

	/**
	 * Display a listing of the Role.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		$seo_configs = $this->seoRepository->getSeoConfig()->all();

		return view('settings.seo.index')
			->with('seo_configs', $seo_configs);
	}



	/**
	 * Show the form for editing the specified Role.
	 *
	 * @param int $id
	 *
	 * @return Response
	 */
	public function edit($id)
	{
		$role = $this->seoRepository->find($id);

		if (empty($role)) {
			Flash::error('Role not found');

			return redirect(route('settings.seo.index'));
		}

		return view('settings.seo.edit')->with('role', $role);
	}

	/**
	 * Update the specified Role in storage.
	 *
	 * @param int $id
	 * @param UpdateSeoRequest $request
	 *
	 * @return Response
	 */
	public function update($id, UpdateSeoRequest $request)
	{
		$role = $this->seoRepository->find($id);

		if (empty($role)) {
			Flash::error('Role not found');

			return redirect(route('settings.seo.index'));
		}

		$role = $this->seoRepository->update($request->all(), $id);

		Flash::success('Role updated successfully.');

		return redirect(route('settings.seo.index'));
	}
}
