<?php

namespace App\Http\Controllers\Admin\Settings;

use Flash;
use Response;
use Request;
use App\DataTables\Settings\PartnerDataTable;
use App\Http\Requests\Settings;
use App\Http\Requests\Settings\CreatePartnerRequest;
use App\Http\Requests\Settings\UpdatePartnerRequest;
use App\Repositories\Settings\PartnerRepository;
use App\Http\Controllers\AppBaseController;
use App\Repositories\Definitions\TermRepository;
use App\Repositories\Contents\MediaRepository;
use App\Repositories\Settings\LanguageRepository;
use App\Repositories\Settings\TagRepository;
use App\Models\Services\Service;
use App\Models\Settings\Language;
use App\Models\Settings\User;
use App\Models\Settings\UserPartner;

class PartnerController extends AppBaseController
{
	/** @var  PartnerRepository */
	private $partnerRepository;

	/** @var  TagRepository */
	private $tagRepository;

	/** @var  TermRepository */
	private $termRepository;

	/** @var  MediaRepository */
	private $mediaRepository;

	/** @var  LanguageRepository */
	private $langRepository;

	public function __construct(
		PartnerRepository $partnerRepo,
		TagRepository $tagRepo,
		TermRepository $termRepo,
		MediaRepository $mediaRepo,
		LanguageRepository $langRepo
	) {
		$this->middleware('auth');
		$this->partnerRepository = $partnerRepo;
		$this->tagRepository = $tagRepo;
		$this->termRepository = $termRepo;
		$this->mediaRepository = $mediaRepo;
		$this->langRepository = $langRepo;
	}

	/**
	 * Display a listing of the Partner.
	 *
	 * @param PartnerDataTable $partnerDataTable
	 * @return Response
	 */
	public function index(PartnerDataTable $partnerDataTable)
	{
		$src = Request::get('src');

		$pageData = array(
			'src' => $src,
		);

		return $partnerDataTable->render('settings.partners.index', $pageData);
	}

	/**
	 * Show the form for creating a new Partner.
	 *
	 * @return Response
	 */
	public function create()
	{
		$partner = new \stdClass();
		$partner->logo = null;
		$langs = $this->langRepository->all();
		$allPartnerTypes = $this->termRepository->getByTaxonomy(18);
		$tags = $this->tagRepository->getForSelect(true);

		$partnerTypes = [];

		foreach ($allPartnerTypes as $partnerType) {
			$partnerTypes[$partnerType->id] = $partnerType->langs[0]->name;
		}

		if (!isset($partner->langs) || $partner->langs == null) {
			$partner->langs = [];
		}
		$partnerLangs = $partner->langs;

		foreach ($langs as $lang) {
			$currentPartnerLang = null;
			foreach ($partnerLangs as $partnerLang) {
				if ($partnerLang->lang_id == $lang->id) {
					$currentPartnerLang = $partnerLang;
				}
			}

			if ($currentPartnerLang == null) {
				$currentPartnerLang = new \stdClass();
				$currentPartnerLang->name = '';
				$currentPartnerLang->lang_id = $lang->id;
			}
			$partner->langs['l' . $lang->id] = $currentPartnerLang;
		}

		if (empty($partner)) {
			Flash::error('Partner not found');

			return redirect(route('settings.partners.index'));
		}

		return view('settings.partners.create')
			->with('partner', $partner)
			->with('tags', $tags)
			->with('partnerTypes', $partnerTypes)
			->with('langs', $langs);
	}

	/**
	 * Store a newly created Partner in storage.
	 *
	 * @param CreatePartnerRequest $request
	 *
	 * @return Response
	 */
	public function store(CreatePartnerRequest $request)
	{
		$input = $request->all();

		$partner = $this->partnerRepository->create($input);
		$this->partnerRepository->saveLangs($input, $partner);

		Flash::success('Partner saved successfully.');

		return redirect(route('settings.partners.index'));
	}

	/**
	 * Display the specified Partner.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function show($id)
	{
		$partner = $this->partnerRepository->getFull($id);

		$default_lang_id = Language::defaultLang()->id;

		$users = UserPartner::getUsersByPartnerId($id);

		$services = Service::where('partner_id', $id)
			->with([
				'langs' =>
				function ($query) use ($default_lang_id) {
					$query->where('lang_id', $default_lang_id);
				}
			])
			->with([
				'categories.term.langs' =>
				function ($query) use ($default_lang_id) {
					$query->where('lang_id', $default_lang_id);
				}
			])
			->with([
				'location.city.langs' =>
				function ($query) use ($default_lang_id) {
					$query->where('lang_id', $default_lang_id);
				}
			])
			->with([
				'location.district.langs' =>
				function ($query) use ($default_lang_id) {
					$query->where('lang_id', $default_lang_id);
				}
			])
			->with('partner')
			->with([
				'partner.langs' =>
				function ($query) use ($default_lang_id) {
					$query->where('lang_id', $default_lang_id);
				}
			])->get();

		if (empty($partner)) {
			Flash::error('Partner not found');

			return redirect(route('settings.partners.index'));
		}

		return view('settings.partners.show')
			->with('partner', $partner)
			->with('services', $services)
			->with('users', $users);
	}

	/**
	 * Show the form for editing the specified Partner.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function edit($id)
	{
		$partner = $this->partnerRepository->getWithLogo($id);
		$langs = $this->langRepository->all();
		$tags = $this->tagRepository->getForSelect(true);
		$allPartnerTypes = $this->termRepository->getByTaxonomy(18);
		$partnerTypes = [];

		foreach ($allPartnerTypes as $partnerType) {
			$partnerTypes[$partnerType->id] = $partnerType->langs[0]->name;
		}

		if (!isset($partner->langs) || $partner->langs == null) {
			$partner->langs = [];
		}
		$partnerLangs = $partner->langs;

		foreach ($langs as $lang) {
			$currentPartnerLang = null;
			foreach ($partnerLangs as $partnerLang) {
				if ($partnerLang->lang_id == $lang->id) {
					$currentPartnerLang = $partnerLang;
				}
			}

			if ($currentPartnerLang == null) {
				$currentPartnerLang = new \stdClass();
				$currentPartnerLang->name = '';
				$currentPartnerLang->lang_id = $lang->id;
			}
			$partner->langs['l' . $lang->id] = $currentPartnerLang;
		}

		if (empty($partner)) {
			Flash::error('Partner not found');

			return redirect(route('settings.partners.index'));
		}

		return view('settings.partners.edit')
			->with('partner', $partner)
			->with('tags', $tags)
			->with('partnerTypes', $partnerTypes)
			->with('langs', $langs);
	}

	/**
	 * Update the specified Partner in storage.
	 *
	 * @param  int              $id
	 * @param UpdatePartnerRequest $request
	 *
	 * @return Response
	 */
	public function update($id, UpdatePartnerRequest $request)
	{
		$partner = $this->partnerRepository->find($id);

		if (empty($partner)) {
			Flash::error('Partner not found');

			return redirect(route('settings.partners.index'));
		}

		$input = $request->all();

		$partner->update($input);
		$partner->touch();

		$this->partnerRepository->saveLangs($input, $partner);

		Flash::success('Partner updated successfully.');

		return redirect(route('settings.partners.index'));
	}

	/**
	 * Remove the specified Partner from storage.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function destroy($id)
	{
		$partner = $this->partnerRepository->find($id);

		if (empty($partner)) {
			Flash::error('Partner not found');

			return redirect(route('settings.partners.index'));
		}

		$this->partnerRepository->delete($id);

		Flash::success('Partner deleted successfully.');

		return redirect(route('settings.partners.index'));
	}
}
