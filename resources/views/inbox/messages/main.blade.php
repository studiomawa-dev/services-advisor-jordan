@extends('layouts.app')

@section('content')

@php
$conversationId = Request::query('conversation_id');
@endphp
<div class="container conversation-page">
	<div class="row justify-content-center">
		<div class="col-md-3">
			<div>
				<div class="new-conversation-actions">
					{!! Form::select('to', $users, null, ['id'=>'user-select', 'class' => 'form-control js-select', 'data-placeholder'=>'Select User']) !!}
					<button class="btn btn-primary btn-block" onclick="createConversation()">New Conversation</button>
				</div>
				<ul id="conversations" class="conversation"></ul>
			</div>
		</div>
		<div class="col-md-9">
			<div class="container chats">
				<div class="row">
					<div class="col-md-12">
						<div class="card card-default">
							@if($conversationId)
							<div class="card-header">
								<div id="messagesHeader" class="float-left">Messages</div>
								<button class="btn btn-danger btn-sm btn-icon float-right" onclick="leaveConversation({{ $conversationId }})"><i class="mdi mdi-delete"></i></button>
							</div>
							<div class="card-body">
								<div data-conversation="{{ $conversationId }}">
									<ul id="messages" class="chat">
									</ul>
								</div>
							</div>
							<div class="card-footer">
								<div id="chatForm" data-conversation="{{ $conversationId }}" data-user="{{ auth()->user() }}">
									<div class="input-group">
										<input id="newMessage" type="text" name="message" class="form-control input-sm" placeholder="Type your message here..." onkeyup="onMessageKeyUp(event)">
										&nbsp;&nbsp;
										<span class="input-group-btn">
											<button class="btn btn-primary btn-sm" id="btn-chat" onclick="sendMessage()">Send</button>
										</span>
									</div>
								</div>
							</div>
							@else
							<div class="card-body">
								<div>Conversation not found.</div>
							</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection


@section('scripts')
<script>
	var participants = [];
	var conversations = [];
	var messages = [];

	var currentUserId = <?= auth()->user()->id ?>;
	var conversationId = <?= intval($conversationId) > 0 ? $conversationId : -1  ?>;

	$(document).ready(function() {
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		if (conversationId > 0) {
			getParticipants();
			fetchMessages();
		}

		fetchConversations();

		$('#user-select').val(-1);
		$("#user-select option[value='" + currentUserId + "']").remove();
	});

	function fetchMessages() {
		$.getJSON('/admin/inbox/conversations/' + conversationId + '/messages', function(result) {
			messages = result.data;
			updateMessages();
		});
	}

	function deleteMessages() {
		$.ajax({
			url: '/admin/inbox/conversations/' + conversationId + '/messages',
			type: 'DELETE',
			success: function(result) {
				messages = result.data;
				updateMessages();
			}
		});
	}

	function createConversation() {
		var receiver = $('#user-select').val();
		if (receiver == undefined) return;

		$.post('/admin/inbox/conversations', {
			to: receiver
		}, function(data) {
			if (data != undefined && data.id != undefined && data.id > 0) {
				showConversation(data.id);
			}
		});
	}

	function fetchConversations() {
		$.getJSON('/admin/inbox/conversations', function(result) {
			conversations = result.data;
			updateConversations();
		});
	}

	function showConversation(id) {
		window.location.href = '/admin/inbox/messages?conversation_id=' + id;
	}

	function isParticipant(id) {
		return window.conversations.indexOf(id) !== -1;
	}

	function leaveConversation(id) {
		$.ajax({
			url: '/admin/inbox/conversations/' + id + '/users',
			type: 'DELETE',
			success: function(result) {
				window.location.href = '/admin/inbox/messages';
			}
		});
	}

	function joinConversation(id) {
		$.post('/admin/inbox/conversations/' + id + '/users', function(data) {
			window.location.href = '/admin/inbox/messages?conversation_id=' + id;
		});
	}

	function sendMessage() {
		var newMessage = $('#newMessage').val();
		if (newMessage == undefined || newMessage.length == 0) return;

		$.post('/admin/inbox/conversations/' + conversationId + '/messages', {
			message: newMessage
		}, function(data) {
			$('#newMessage').val('');
			location.reload();
		});
	}

	function onMessageKeyUp(event) {
		if (event.key === 'Enter') {
			sendMessage();
		}
	}

	function getParticipants() {
		$.getJSON('/admin/inbox/conversations/' + conversationId + '/users', function(result) {
			participants = result;
			updateParticipants();
		});
	}

	function updateMessages() {
		var container = $('#messages');
		container.empty();

		if (messages != undefined && messages.length > 0) {
			for (var i = 0; i < messages.length; i++) {
				var message = messages[i];
				var messageTime = moment(message.created_at, "YYYY-MM-DD hh:mm:ss").format('DD.MM.YYYY hh:mm:ss');

				var messageHtml = `
					<li class="message-main-` + (message.sender.id == currentUserId ? 'sender' : 'receiver') + ` clearfix">
						<div class="chat-body clearfix ` + (message.sender.id == currentUserId ? 'sender' : 'receiver') + `">
							<p class="message-text">` + message.body + `</p>
							<p class="message-time">` + messageTime + `</p>
						</div>
					</li>`;

				container.append(messageHtml);
			}
		}

		var chats = $('.chats .card-body');
		chats.scrollTop(chats.prop("scrollHeight"));
	}

	function updateParticipants() {
		var container = $('#conversationParticipants');
		container.empty();

		if (participants != undefined && participants.length > 0) {
			for (var i = 0; i < participants.length; i++) {
				var participant = participants[i];
				if (participant.id != currentUserId) {
					$('#messagesHeader').text(participant.name);
				}

				var participantHtml = `
					<li class="left clearfix">
						<div class="chat-body clearfix">
							<div class="header">
								<strong class="primary-font">` + participant.name + `</strong>
							</div>
						</div>
					</li>`;

				container.append(participantHtml);
			}
		}
	}

	function updateConversations() {
		var container = $('#conversations');
		container.empty();

		if (conversations != undefined && conversations.length > 0) {
			for (var i = 0; i < conversations.length; i++) {
				var conversation = conversations[i];

				var title = 'Conversation ' + conversation.id;
				if (conversation != undefined && conversation.receiver != undefined && conversation.receiver.name != undefined) {
					title = conversation.receiver.name;
				}

				var itemClass = conversationId == conversation.id ? 'active' : '';

				var conversationHtml = `
					<li class="` + itemClass + ` clearfix" onclick="showConversation(` + conversation.id + `)">
						<strong class="primary-font"> ` + title + `</strong>
					</li>`;

				container.append(conversationHtml);
			}
		}
	}
</script>
@endsection
