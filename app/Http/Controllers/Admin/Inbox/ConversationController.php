<?php

namespace App\Http\Controllers\Admin\Inbox;

use Chat;
use App\Events\MessageSent;
use App\Message;
use Illuminate\Http\Request;
use Musonza\Chat\Models\Conversation;
use App\Http\Controllers\AppBaseController;

class ConversationController extends AppBaseController
{
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function index()
	{
		//$conversations = Chat::conversations()->conversation->all();
		$conversations = Chat::conversations()->for(auth()->user())->limit(99999)->get();

		if ($conversations != null && count($conversations) > 0) {
			foreach ($conversations as $conversation) {
				$conversation->receiver = $conversation->users->where('id', '!=', auth()->user()->id)->first();
			}
		}

		return response($conversations);
	}

	public function store(Request $request)
	{
		$to = $request->post('to');
		if (!isset($to) && $to == null) return response('no-receiver', 400);

		$conversation = Chat::conversations()->between(auth()->user()->id, $to);

		if ($conversation == null) {
			$participants = [auth()->user()->id, $to];
			$conversation = Chat::createConversation($participants)->makePrivate();
		}

		return response($conversation);
	}

	public function participants($conversationId)
	{
		$participants = Chat::conversations()->getById($conversationId)->users;

		return response($participants);
	}

	public function join(Request $request, Conversation $conversation)
	{
		Chat::conversation($conversation)->addParticipants(auth()->user());
		return response('');
	}

	public function leaveConversation(Request $request, Conversation $conversation)
	{
		Chat::conversation($conversation)->removeParticipants(auth()->user());
		return response('');
	}

	public function getMessages(Request $request, Conversation $conversation)
	{
		$messages = Chat::conversation($conversation)->for(auth()->user())->getMessages();

		return response($messages);
	}

	public function deleteMessages(Conversation $conversation)
	{
		Chat::conversation($conversation)->for(auth()->user())->clear();
		return response('');
	}

	public function sendMessage(Request $request, Conversation $conversation)
	{
		$message = Chat::message($request->message)
			->from(auth()->user())
			->to($conversation)
			->send();

		return response($message);
	}
}
