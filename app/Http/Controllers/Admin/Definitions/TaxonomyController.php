<?php

namespace App\Http\Controllers\Admin\Definitions;

use App\Http\Requests\Definitions\CreateTaxonomyRequest;
use App\Http\Requests\Definitions\UpdateTaxonomyRequest;
use App\Repositories\Definitions\TaxonomyRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Response;

class TaxonomyController extends AppBaseController
{
	/** @var  TaxonomyRepository */
	private $taxonomyRepository;

	public function __construct(TaxonomyRepository $taxonomyRepo)
	{
		$this->middleware('auth');
		$this->taxonomyRepository = $taxonomyRepo;
	}

	/**
	 * Display a listing of the Taxonomy.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		$taxonomies = $this->taxonomyRepository->all();

		return view('definitions.taxonomies.index')
			->with('taxonomies', $taxonomies);
	}

	/**
	 * Show the form for creating a new Taxonomy.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('definitions.taxonomies.create');
	}

	/**
	 * Store a newly created Taxonomy in storage.
	 *
	 * @param CreateTaxonomyRequest $request
	 *
	 * @return Response
	 */
	public function store(CreateTaxonomyRequest $request)
	{
		$input = $request->all();

		$taxonomy = $this->taxonomyRepository->create($input);

		Flash::success('Taxonomy saved successfully.');

		return redirect(route('definitions.taxonomies.index'));
	}

	/**
	 * Display the specified Taxonomy.
	 *
	 * @param int $id
	 *
	 * @return Response
	 */
	public function show($id)
	{
		$taxonomy = $this->taxonomyRepository->find($id);

		if (empty($taxonomy)) {
			Flash::error('Taxonomy not found');

			return redirect(route('definitions.taxonomies.index'));
		}

		return view('definitions.taxonomies.show')->with('taxonomy', $taxonomy);
	}

	/**
	 * Show the form for editing the specified Taxonomy.
	 *
	 * @param int $id
	 *
	 * @return Response
	 */
	public function edit($id)
	{
		$taxonomy = $this->taxonomyRepository->find($id);

		if (empty($taxonomy)) {
			Flash::error('Taxonomy not found');

			return redirect(route('definitions.taxonomies.index'));
		}

		return view('definitions.taxonomies.edit')->with('taxonomy', $taxonomy);
	}

	/**
	 * Update the specified Taxonomy in storage.
	 *
	 * @param int $id
	 * @param UpdateTaxonomyRequest $request
	 *
	 * @return Response
	 */
	public function update($id, UpdateTaxonomyRequest $request)
	{
		$taxonomy = $this->taxonomyRepository->find($id);

		if (empty($taxonomy)) {
			Flash::error('Taxonomy not found');

			return redirect(route('definitions.taxonomies.index'));
		}

		$taxonomy = $this->taxonomyRepository->update($request->all(), $id);

		Flash::success('Taxonomy updated successfully.');

		return redirect(route('definitions.taxonomies.index'));
	}

	/**
	 * Remove the specified Taxonomy from storage.
	 *
	 * @param int $id
	 *
	 * @throws \Exception
	 *
	 * @return Response
	 */
	public function destroy($id)
	{
		$taxonomy = $this->taxonomyRepository->find($id);

		if (empty($taxonomy)) {
			Flash::error('Taxonomy not found');

			return redirect(route('definitions.taxonomies.index'));
		}

		$this->taxonomyRepository->delete($id);

		Flash::success('Taxonomy deleted successfully.');

		return redirect(route('definitions.taxonomies.index'));
	}
}
