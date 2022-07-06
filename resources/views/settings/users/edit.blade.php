@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h4 class="card-title my-1">{{ __('app.User') }} | {{ __('app.Update') }}</h4>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-12">
						<div class="content">
							@include('flash::message')
							@include('common.errors')
							<div class="box box-primary">
								<div class="box-body">
									<div class="row">
										{!! Form::model($user, ['route' => ['settings.users.update', $user->id], 'method' => 'patch']) !!}

										@include('settings.users.fields')

										{!! Form::close() !!}
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection