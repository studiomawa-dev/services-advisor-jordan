<?php

namespace App\Http\Controllers\Admin\Inbox;

use App\DataTables\Inbox\MessageDataTable;
use App\Http\Requests\Inbox;
use App\Http\Requests\Inbox\CreateMessageRequest;
use App\Http\Requests\Inbox\UpdateMessageRequest;
use App\Repositories\Inbox\MessageRepository;
use Flash;
use App\Http\Controllers\AppBaseController;
use Response;
use Request;
use App\Repositories\Settings\UserRepository;
use Chat;

class MessageController extends AppBaseController
{
	/** @var  MessageRepository */
	private $messageRepository;

	/** @var  UserRepository */
	private $userRepository;

	public function __construct(
		MessageRepository $messageRepo,
		UserRepository $userRepo
	) {
		$this->middleware('auth');
		$this->messageRepository = $messageRepo;
		$this->userRepository = $userRepo;
	}

	/**
	 * Display a listing of the Message.
	 *
	 * @param MessageDataTable $messageDataTable
	 * @return Response
	 */
	//MessageDataTable $messageDataTable
	public function index()
	{
		$to = Request::get('user_id');
		if ($to != null && is_numeric($to)) {
			$conversation = Chat::conversations()->between(auth()->user()->id, $to);

			if ($conversation == null) {
				$participants = [auth()->user()->id, $to];
				$conversation = Chat::createConversation($participants)->makePrivate();
			}

			if ($conversation != null && $conversation->id > 0) {
				return redirect()->action('Admin\Inbox\MessageController@index', ['conversation_id' => $conversation->id]);
			}
		}

		$conversations = Chat::conversation(Chat::conversations()->conversation)
			->setParticipant(auth()->user())
			->get()
			->toArray()['data'];
		$conversations = array_pluck($conversations, 'id');
		$users = $this->userRepository->getUsersForSelect(true);

		return view('inbox.messages.main', compact('conversations'))
			->with('users', $users);

		//return $messageDataTable->render('inbox.messages.index');
	}

	/**
	 * Show the form for creating a new Message.
	 *
	 * @return Response
	 */
	public function create()
	{
		$users = $this->userRepository->getUsersForSelect();

		return view('inbox.messages.create')
			->with('users', $users);
	}

	/**
	 * Store a newly created Message in storage.
	 *
	 * @param CreateMessageRequest $request
	 *
	 * @return Response
	 */
	public function store(CreateMessageRequest $request)
	{
		$input = $request->all();

		$message = $this->messageRepository->create($input);

		Flash::success('Message saved successfully.');

		return redirect(route('inbox.messages.index'));
	}

	/**
	 * Display the specified Message.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function show($id)
	{
		$message = $this->messageRepository->find($id);

		if (empty($message)) {
			Flash::error('Message not found');

			return redirect(route('inbox.messages.index'));
		}

		return view('inbox.messages.show')->with('message', $message);
	}

	/**
	 * Show the form for editing the specified Message.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function edit($id)
	{
		$message = $this->messageRepository->find($id);

		if (empty($message)) {
			Flash::error('Message not found');

			return redirect(route('inbox.messages.index'));
		}

		return view('inbox.messages.edit')->with('message', $message);
	}

	/**
	 * Update the specified Message in storage.
	 *
	 * @param  int              $id
	 * @param UpdateMessageRequest $request
	 *
	 * @return Response
	 */
	public function update($id, UpdateMessageRequest $request)
	{
		$message = $this->messageRepository->find($id);

		if (empty($message)) {
			Flash::error('Message not found');

			return redirect(route('inbox.messages.index'));
		}

		$message = $this->messageRepository->update($request->all(), $id);

		Flash::success('Message updated successfully.');

		return redirect(route('inbox.messages.index'));
	}

	/**
	 * Remove the specified Message from storage.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function destroy($id)
	{
		$message = $this->messageRepository->find($id);

		if (empty($message)) {
			Flash::error('Message not found');

			return redirect(route('inbox.messages.index'));
		}

		$this->messageRepository->delete($id);

		Flash::success('Message deleted successfully.');

		return redirect(route('inbox.messages.index'));
	}
}
