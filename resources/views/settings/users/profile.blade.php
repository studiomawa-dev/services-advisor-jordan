@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-md-4 grid-margin stretch-card">
		<div class="card text-center">
			<div class="card-body">
				<div class="form-group user-photo-upload">
					<center>
						<div class="user-photo-container" id="image">
							@if($user->photo != null)
							<img id="preview_image" class="preview rounded-circle" src="{{ URL::to('/') }}/media/{{ $user->photo->filename }}" alt="">
							@else
							<img id="preview_image" class="preview rounded-circle" src="{{asset('media/profileimg.png')}}" />
							@endif
							<div id="loading" class="loading">
								<img src="{{asset('img/loader.svg')}}" alt="">
							</div>
						</div>
					</center>
				</div>

				<h4>{{$user->name}}</h4>
				<p class="text-muted">
					@if(strtotime($user->last_action) > time() - 5000)
					<i title="Online" class="link-icon mdi mdi-radiobox-marked" style="color:  #5dacac"></i>
					@else
					<i title="Offline" class="link-icon mdi mdi-radiobox-blank"></i>
					@endif
					{{ '@' . $user->username}}
				</p>
				<p>
					@if(isset($user->phone) && $user->phone != null)
					<a class="btn btn-clear" href="tel:{{ $user->phone }}">{{ $user->phone }}</a><br />
					@endif
					@if(isset($user->email) && $user->email != null)
					<a class="btn btn-clear" href="mailto:{{ $user->email }}">{{ $user->email }}</a><br />
					@endif
				</p>

				@if($isMe)
				<a class="btn btn-primary btn-sm mt-3" href="/admin/settings/users/{{$user->id}}/edit">Edit Profile</a>
				@else
				<a class="btn btn-primary btn-sm mt-3" href="/admin/inbox/messages?user_id={{$user->id}}">Send Message</a>
				@endif
				<div class="border-top pt-3 d-none">
					<div class="row">
						<div class="col-4">
							<h6>5896</h6>
							<p>Post</p>
						</div>
						<div class="col-4">
							<h6>1596</h6>
							<p>Followers</p>
						</div>
						<div class="col-4">
							<h6>7896</h6>
							<p>Likes</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-4 grid-margin stretch-card">
		<div class="card">
			<div class="card-body">
				<h4 class="card-title">Organizations</h4>
				<div class="preview-list">

					@if($user->partners != null && count($user->partners) > 0)
					@foreach($user->partners as $partner)

					<div class="preview-item border-bottom px-0" style="cursor:pointer" onclick="window.location='{{ url('/admin/settings/partners/' . $partner->id) }}'">
						<div class="preview-thumbnail">
							@if($partner->logo == null)
							<img src="{{asset('media/noimage.png')}}" alt="image" class="rounded-circle">
							@else
							<img src="{{ url('media/' . $partner->logo->filename) }}" alt="image" class="rounded-circle">
							@endif
						</div>
						<div class="preview-item-content d-flex flex-grow">
							<div class="flex-grow">
								<h6 class="preview-subject">{{ $partner->langs[0]->name }}</h6>
								<p>{{ $partner->langs[0]->full_name }}</p>
							</div>
						</div>
					</div>

					@endforeach
					@endif

				</div>
			</div>
		</div>
	</div>
	<div class="col-md-4 grid-margin stretch-card">
		<div class="card">
			<div class="card-body">
				<h4 class="card-title">Feedback Categories</h4>
				<div class="preview-list">

					@if($user->feedbacks != null && count($user->feedbacks) > 0)
					@foreach($user->feedbacks as $feedback)

					<div class="preview-item border-bottom px-0">
						<div class="preview-item-content d-flex flex-grow">
							<div class="flex-grow">
								<h6 class="preview-subject">{{ $feedback }}</h6>
							</div>
						</div>
					</div>

					@endforeach
					@endif

				</div>
			</div>
		</div>
	</div>
</div>


@endsection