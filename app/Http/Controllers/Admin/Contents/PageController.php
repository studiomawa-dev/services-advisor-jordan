<?php

namespace App\Http\Controllers\Admin\Contents;

use App\DataTables\Contents\PageDataTable;
use App\Http\Requests\Contents;
use App\Http\Requests\Contents\CreatePageRequest;
use App\Http\Requests\Contents\UpdatePageRequest;
use App\Repositories\Contents\PageRepository;
use Flash;
use App\Http\Controllers\AppBaseController;
use Response;

class PageController extends AppBaseController
{
	/** @var  PageRepository */
	private $pageRepository;

	public function __construct(PageRepository $pageRepo)
	{
		$this->middleware('auth');
		$this->pageRepository = $pageRepo;
	}

	/**
	 * Display a listing of the Page.
	 *
	 * @param PageDataTable $pageDataTable
	 * @return Response
	 */
	public function index(PageDataTable $pageDataTable)
	{
		return $pageDataTable->render('contents.pages.index');
	}

	/**
	 * Show the form for creating a new Page.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('contents.pages.create');
	}

	/**
	 * Store a newly created Page in storage.
	 *
	 * @param CreatePageRequest $request
	 *
	 * @return Response
	 */
	public function store(CreatePageRequest $request)
	{
		$input = $request->all();

		$page = $this->pageRepository->create($input);

		Flash::success('Page saved successfully.');

		return redirect(route('contents.pages.index'));
	}

	/**
	 * Display the specified Page.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function show($id)
	{
		$page = $this->pageRepository->find($id);

		if (empty($page)) {
			Flash::error('Page not found');

			return redirect(route('contents.pages.index'));
		}

		return view('contents.pages.show')->with('page', $page);
	}

	/**
	 * Show the form for editing the specified Page.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function edit($id)
	{
		$page = $this->pageRepository->find($id);

		if (empty($page)) {
			Flash::error('Page not found');

			return redirect(route('contents.pages.index'));
		}

		return view('contents.pages.edit')->with('page', $page);
	}

	/**
	 * Update the specified Page in storage.
	 *
	 * @param  int              $id
	 * @param UpdatePageRequest $request
	 *
	 * @return Response
	 */
	public function update($id, UpdatePageRequest $request)
	{
		$page = $this->pageRepository->find($id);

		if (empty($page)) {
			Flash::error('Page not found');

			return redirect(route('contents.pages.index'));
		}

		$page = $this->pageRepository->update($request->all(), $id);

		Flash::success('Page updated successfully.');

		return redirect(route('contents.pages.index'));
	}

	/**
	 * Remove the specified Page from storage.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function destroy($id)
	{
		$page = $this->pageRepository->find($id);

		if (empty($page)) {
			Flash::error('Page not found');

			return redirect(route('contents.pages.index'));
		}

		$this->pageRepository->delete($id);

		Flash::success('Page deleted successfully.');

		return redirect(route('contents.pages.index'));
	}
}
