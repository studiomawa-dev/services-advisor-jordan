<?php

namespace App\Http\Controllers\Admin\Settings;

use App\DataTables\Settings\LogDataTable;
use App\Http\Requests\Settings;
use App\Http\Requests\Settings\CreateLogRequest;
use App\Http\Requests\Settings\UpdateLogRequest;
use App\Repositories\Settings\LogRepository;
use Flash;
use App\Http\Controllers\AppBaseController;
use Response;
use Request;

class LogController extends AppBaseController
{
    /** @var  LogRepository */
    private $logRepository;

    public function __construct(LogRepository $logRepo)
    {
		$this->middleware('auth');
        $this->logRepository = $logRepo;
    }

    /**
     * Display a listing of the Log.
     *
     * @param LogDataTable $logDataTable
     * @return Response
     */
    public function index(LogDataTable $logDataTable)
    {
        $src = Request::get('src');

        return $logDataTable->render('settings.logs.index', array('src' => $src));
    }

    /**
     * Show the form for creating a new Log.
     *
     * @return Response
     */
    public function create()
    {
        return view('settings.logs.create');
    }

    /**
     * Store a newly created Log in storage.
     *
     * @param CreateLogRequest $request
     *
     * @return Response
     */
    public function store(CreateLogRequest $request)
    {
        $input = $request->all();

        $log = $this->logRepository->create($input);

        Flash::success('Log saved successfully.');

        return redirect(route('settings.logs.index'));
    }

    /**
     * Display the specified Log.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $log = $this->logRepository->find($id);

        if (empty($log)) {
            Flash::error('Log not found');

            return redirect(route('settings.logs.index'));
        }

        return view('settings.logs.show')->with('log', $log);
    }

    /**
     * Show the form for editing the specified Log.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $log = $this->logRepository->find($id);

        if (empty($log)) {
            Flash::error('Log not found');

            return redirect(route('settings.logs.index'));
        }

        return view('settings.logs.edit')->with('log', $log);
    }

    /**
     * Update the specified Log in storage.
     *
     * @param  int              $id
     * @param UpdateLogRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateLogRequest $request)
    {
        $log = $this->logRepository->find($id);

        if (empty($log)) {
            Flash::error('Log not found');

            return redirect(route('settings.logs.index'));
        }

        $log = $this->logRepository->update($request->all(), $id);

        Flash::success('Log updated successfully.');

        return redirect(route('settings.logs.index'));
    }

    /**
     * Remove the specified Log from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $log = $this->logRepository->find($id);

        if (empty($log)) {
            Flash::error('Log not found');

            return redirect(route('settings.logs.index'));
        }

        $this->logRepository->delete($id);

        Flash::success('Log deleted successfully.');

        return redirect(route('settings.logs.index'));
    }
}
