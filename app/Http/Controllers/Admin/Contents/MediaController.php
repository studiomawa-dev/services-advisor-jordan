<?php

namespace App\Http\Controllers\Admin\Contents;

use App\DataTables\Contents\MediaDataTable;
use App\Http\Requests\Contents;
use App\Http\Requests\Contents\CreateMediaRequest;
use App\Http\Requests\Contents\UpdateMediaRequest;
use App\Repositories\Contents\MediaRepository;
use Flash;
use App\Http\Controllers\AppBaseController;
use Response;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Facades\Validator;

class MediaController extends AppBaseController
{
	/** @var  MediaRepository */
	private $mediaRepository;

	public function __construct(MediaRepository $mediaRepo)
	{
		$this->middleware('auth');
		$this->mediaRepository = $mediaRepo;
	}

	/**
	 * Display a listing of the Media.
	 *
	 * @param MediaDataTable $mediaDataTable
	 * @return Response
	 */
	public function index(MediaDataTable $mediaDataTable)
	{
		return $mediaDataTable->render('contents.media.index');
	}

	/**
	 * Show the form for creating a new Media.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('contents.media.create');
	}

	/**
	 * Store a newly created Media in storage.
	 *
	 * @param CreateMediaRequest $request
	 *
	 * @return Response
	 */
	public function store(CreateMediaRequest $request)
	{
		$input = $request->all();

		$media = $this->mediaRepository->create($input);

		Flash::success('Media saved successfully.');

		return redirect(route('contents.media.index'));
	}

	/**
	 * Display the specified Media.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function show($id)
	{
		$media = $this->mediaRepository->find($id);

		if (empty($media)) {
			Flash::error('Media not found');

			return redirect(route('contents.media.index'));
		}

		return view('contents.media.show')->with('media', $media);
	}

	/**
	 * Show the form for editing the specified Media.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function edit($id)
	{
		$media = $this->mediaRepository->find($id);

		if (empty($media)) {
			Flash::error('Media not found');

			return redirect(route('contents.media.index'));
		}

		return view('contents.media.edit')->with('media', $media);
	}

	/**
	 * Update the specified Media in storage.
	 *
	 * @param  int              $id
	 * @param UpdateMediaRequest $request
	 *
	 * @return Response
	 */
	public function update($id, UpdateMediaRequest $request)
	{
		$media = $this->mediaRepository->find($id);

		if (empty($media)) {
			Flash::error('Media not found');

			return redirect(route('contents.media.index'));
		}

		$media = $this->mediaRepository->update($request->all(), $id);

		Flash::success('Media updated successfully.');

		return redirect(route('contents.media.index'));
	}

	/**
	 * Remove the specified Media from storage.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function destroy($id)
	{
		$media = $this->mediaRepository->find($id);

		if (empty($media)) {
			Flash::error('Media not found');

			return redirect(route('contents.media.index'));
		}

		$this->mediaRepository->delete($id);

		Flash::success('Media deleted successfully.');

		return redirect(route('contents.media.index'));
	}

	/**
	 * Upload the specified Media in storage.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function upload(Request $request)
	{
		if ($request->isMethod('post')) {

			$validator = Validator::make(
				$request->all(),
				[
					'file' => 'image',
				],
				[
					'file.image' => 'The file must be an image (jpeg, png, bmp, gif, or svg)'
				]
			);

			if ($validator->fails()) {
				return array(
					'fail' => true,
					'errors' => $validator->errors()
				);
			}

			$file = $request->file('file');

			$mimeType = $file->getMimeType();
			$fileSize = $file->getSize();
			$extension = $file->getClientOriginalExtension();
			$width = 0;
			$height = 0;
			$type = '';

			if (strpos($mimeType, 'image') > -1) {
				$tmpFile = $_FILES['file']['tmp_name'];
				list($width, $height) = getimagesize($tmpFile);
				$type = 'image';
			}

			$dir = 'media/';
			$fileName = time() . uniqid() . '.' . $extension;
			$file->move($dir, $fileName);

			$input = [
				'filename' => $fileName,
				'filemime' => $mimeType,
				'type' => $type,
				'filesize' => $fileSize,
				'status' => 1,
				'width' => $width,
				'height' => $height
			];


			$media = $this->mediaRepository->create($input);

			return $media;
		}
	}
}
