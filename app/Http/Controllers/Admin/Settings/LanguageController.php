<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Requests\Settings\CreateLanguageRequest;
use App\Http\Requests\Settings\UpdateLanguageRequest;
use App\Repositories\Settings\LanguageRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Response;

class LanguageController extends AppBaseController
{
	/** @var  LanguageRepository */
	private $languageRepository;

	public function __construct(LanguageRepository $languageRepo)
	{
		$this->middleware('auth');
		$this->languageRepository = $languageRepo;
	}

	/**
	 * Display a listing of the Language.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		$languages = $this->languageRepository->all();

		return view('settings.languages.index')
			->with('languages', $languages);
	}

	/**
	 * Show the form for creating a new Language.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('settings.languages.create');
	}

	/**
	 * Store a newly created Language in storage.
	 *
	 * @param CreateLanguageRequest $request
	 *
	 * @return Response
	 */
	public function store(CreateLanguageRequest $request)
	{
		$input = $request->all();

		$language = $this->languageRepository->create($input);

		Flash::success('Language saved successfully.');

		return redirect(route('settings.languages.index'));
	}

	/**
	 * Display the specified Language.
	 *
	 * @param int $id
	 *
	 * @return Response
	 */
	public function show($id)
	{
		$language = $this->languageRepository->find($id);

		if (empty($language)) {
			Flash::error('Language not found');

			return redirect(route('settings.languages.index'));
		}

		return view('settings.languages.show')->with('language', $language);
	}

	/**
	 * Show the form for editing the specified Language.
	 *
	 * @param int $id
	 *
	 * @return Response
	 */
	public function edit($id)
	{
		$language = $this->languageRepository->find($id);

		if (empty($language)) {
			Flash::error('Language not found');

			return redirect(route('settings.languages.index'));
		}

		return view('settings.languages.edit')->with('language', $language);
	}

	/**
	 * Update the specified Language in storage.
	 *
	 * @param int $id
	 * @param UpdateLanguageRequest $request
	 *
	 * @return Response
	 */
	public function update($id, UpdateLanguageRequest $request)
	{
		$language = $this->languageRepository->find($id);

		if (empty($language)) {
			Flash::error('Language not found');

			return redirect(route('settings.languages.index'));
		}

		$language = $this->languageRepository->update($request->all(), $id);

		Flash::success('Language updated successfully.');

		return redirect(route('settings.languages.index'));
	}

	/**
	 * Remove the specified Language from storage.
	 *
	 * @param int $id
	 *
	 * @throws \Exception
	 *
	 * @return Response
	 */
	public function destroy($id)
	{
		$language = $this->languageRepository->find($id);

		if (empty($language)) {
			Flash::error('Language not found');

			return redirect(route('settings.languages.index'));
		}

		$this->languageRepository->delete($id);

		Flash::success('Language deleted successfully.');

		return redirect(route('settings.languages.index'));
	}
}
