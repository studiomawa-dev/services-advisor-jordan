<?php

namespace App\Http\Controllers\Admin\Definitions;

use Illuminate\Http\Request;
use Flash;
use Response;
use Illuminate\Support\Arr;

use App\Http\Controllers\AppBaseController;

use App\Http\Requests\Definitions\CreateTermRequest;
use App\Http\Requests\Definitions\UpdateTermRequest;
use App\Repositories\Definitions\TermRepository;
use App\Repositories\Definitions\TaxonomyRepository;
use App\Repositories\Settings\LanguageRepository;
use App\Repositories\Settings\TagRepository;

use App\Models\Settings\Log;

class TermController extends AppBaseController
{
	/** @var TagRepository */
	private $tagRepository;

	/** @var TermRepository */
	private $termRepository;

	private $taxonomyRepository;
	private $langRepository;

	public function __construct(
		TagRepository $tagRepository,
		TermRepository $termRepo,
		TaxonomyRepository $taxonomyRepo,
		LanguageRepository $langRepo
	) {
		$this->middleware('auth');
		$this->tagRepository = $tagRepository;
		$this->termRepository = $termRepo;
		$this->taxonomyRepository = $taxonomyRepo;
		$this->langRepository = $langRepo;
	}

	/**
	 * Display a listing of the Term.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		$taxonomyId = $request->query('taxonomy');

		$taxonomy = $this->taxonomyRepository->getWithDefaultLang($taxonomyId);
		$terms = $this->termRepository->getNestedTerms($taxonomyId);

		return view('definitions.terms.index')
			->with('taxonomy', $taxonomy)
			->with('terms', $terms);
	}

	/**
	 * Show the form for creating a new Term.
	 *
	 * @return Response
	 */
	public function create(Request $request)
	{
		$taxonomyId = $request->query('taxonomy');
		if (isset($taxonomyId) && $taxonomyId != null) $taxonomyId = intval($taxonomyId);
		$term = new \stdClass();
		$term->tag_id = 0;
		$term->taxonomy_id = $taxonomyId;
		$term->order = 0;
		$term->parent_id = 0;
		$term->id = 0;
		$taxonomies = $this->taxonomyRepository->getForSelect();
		$langs = $this->langRepository->all();

		if (!isset($term->langs) || $term->langs == null) {
			$term->langs = [];
		}
		$termLangs = $term->langs;

		foreach ($langs as $lang) {
			$currentTermLang = null;
			foreach ($termLangs as $termLang) {
				if ($termLang->lang_id == $lang->id) {
					$currentTermLang = $termLang;
				}
			}

			if ($currentTermLang == null) {
				$currentTermLang = new \stdClass();
				$currentTermLang->name = '';
				$currentTermLang->lang_id = $lang->id;
			}
			$term->langs['l' . $lang->id] = $currentTermLang;
		}

		$terms =  [];
		if ($taxonomyId == 12) {
			$terms = $this->termRepository->getCategoryTerms('en');
		} else if ($taxonomyId == 13) {
			$terms = $this->termRepository->getRootLocationTermsForSelect('en');
		} else {
			$terms = $this->termRepository->getByTaxonomy($taxonomyId);
		}

		$tags = $this->tagRepository->getForSelect();

		return view('definitions.terms.create')
			->with('tags', $tags)
			->with('taxonomies', $taxonomies)
			->with('langs', $langs)
			->with('terms', $terms)
			->with('term', $term);
	}

	/**
	 * Store a newly created Term in storage.
	 *
	 * @param CreateTermRequest $request
	 *
	 * @return Response
	 */
	public function store(CreateTermRequest $request)
	{
		$input = $request->all();

		$term = $this->termRepository->createWithLangs($input);

		if ($term !== false) {
			Flash::success('Term saved successfully.');
			Log::warning('Term', 'Create', 'Term created successfully.');

			return redirect(route('definitions.terms.index', ['taxonomy' => $term->taxonomy_id]));
		}

		Flash::error('Term could not save.');

		return redirect()->back();
	}

	/**
	 * Display the specified Term.
	 *
	 * @param int $id
	 *
	 * @return Response
	 */
	public function show($id)
	{
		$term = $this->termRepository->find($id);

		if (empty($term)) {
			Flash::error('Term not found');

			return redirect(route('definitions.terms.index'));
		}

		return view('definitions.terms.show')->with('term', $term);
	}

	/**
	 * Show the form for editing the specified Term.
	 *
	 * @param int $id
	 *
	 * @return Response
	 */
	public function edit($id)
	{
		$term = $this->termRepository->findWithLangs($id);
		$taxonomies = $this->taxonomyRepository->getForSelect();
		$langs = $this->langRepository->all();

		if (!isset($term->langs) || $term->langs == null) {
			$term->langs = [];
		}
		$termLangs = $term->langs;

		foreach ($langs as $lang) {
			$currentTermLang = null;
			foreach ($termLangs as $termLang) {
				if ($termLang->lang_id == $lang->id) {
					$currentTermLang = $termLang;
				}
			}

			if ($currentTermLang == null) {
				$currentTermLang = new \stdClass();
				$currentTermLang->name = '';
				$currentTermLang->lang_id = $lang->id;
			}
			$term->langs['l' . $lang->id] = $currentTermLang;
		}

		if (empty($term)) {
			Flash::error('Term not found');

			return redirect(route('definitions.terms.index'));
		}


		$terms = $this->termRepository->getTerms($term->taxonomy_id, 'en');


		$tags = $this->tagRepository->getForSelect();


		return view('definitions.terms.edit')
			->with('tags', $tags)
			->with('taxonomies', $taxonomies)
			->with('langs', $langs)
			->with('terms', $terms)
			->with('term', $term);
	}

	/**
	 * Update the specified Term in storage.
	 *
	 * @param int $id
	 * @param UpdateTermRequest $request
	 *
	 * @return Response
	 */
	public function update($id, UpdateTermRequest $request)
	{
		$term = $this->termRepository->find($id);

		if (empty($term)) {
			Flash::error('Term not found');

			return redirect(route('definitions.terms.index'));
		}

		$term = $this->termRepository->updateWithLangs($request->all(), $id);

		Flash::success('Term updated successfully.');
		Log::warning('Term', 'Update', 'Term updated successfully.');

		return redirect(route('definitions.terms.index', ['taxonomy' => $term->taxonomy_id]));
	}

	/**
	 * Remove the specified Term from storage.
	 *
	 * @param int $id
	 *
	 * @throws \Exception
	 *
	 * @return Response
	 */
	public function destroy($id)
	{
		$term = $this->termRepository->find($id);

		if (empty($term)) {
			Flash::error('Term not found');

			return redirect(route('definitions.terms.index'));
		}

		$term->deleted = 1;
		$this->termRepository->update($term->toArray(), $id);

		Flash::success('Term deleted successfully.');
		Log::warning('Term', 'Delete', 'Term deleted successfully.');

		return redirect(route('definitions.terms.index', ['taxonomy' => $term->taxonomy_id]));
	}
}
