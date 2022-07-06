<div class="col-12">
	<div class="row">
		<div class="col-md-6">

			<!-- Name Field -->
			<div class="form-group col-sm-12">
				{!! Form::label('name', __('app.Firstname Surname:')) !!}
				{!! Form::text('name', null, ['class' => 'form-control']) !!}
			</div>

			<div class="form-group col-sm-12">
				{!! Form::label('username', __('app.Username:')) !!}
				{!! Form::text('username', null, ['class' => 'form-control']) !!}
			</div>

			<!-- Email Field -->
			<div class="form-group col-sm-12">
				{!! Form::label('email', __('app.Email:')) !!}
				{!! Form::email('email', null, ['class' => 'form-control']) !!}
			</div>

			<!-- Phone Field -->
			<div class="form-group col-sm-12">
				{!! Form::label('phone', __('app.Phone:')) !!}
				{!! Form::tel('phone', null, ['class' => 'form-control']) !!}
			</div>

			<!-- Tag Id Field -->
			<div class="form-group col-sm-12">
				{!! Form::label('tag_id', __('app.Tags:')) !!}
				@if(!Auth::user()->isInRole('viewer'))
				{!! Form::select('tag_id[]', $tags, null, ['class' => 'form-control js-select', 'multiple'=>false]) !!}
				@else
				{!! Form::select('tag_id[]', $tags, null, ['class' => 'form-control js-select', 'multiple'=>false, 'disabled' => 'disabled']) !!}
				@endif
			</div>

			<!-- Role Id Field -->
			<div class="form-group col-sm-12">
				{!! Form::label('role_id', __('app.Roles:')) !!}
				@if(!Auth::user()->isInRole('viewer'))
				{!! Form::select('role_id[]', $roles, null, ['class' => 'form-control js-select', 'multiple'=>true]) !!}
				@else
				{!! Form::select('role_id[]', $roles, null, ['class' => 'form-control js-select', 'multiple'=>true, 'disabled' => 'disabled']) !!}
				@endif
			</div>

			<!-- Partner Id Field -->
			<div class="form-group col-sm-12">
				{!! Form::label('partner_id', __('app.Organizations:')) !!}
				@if(!Auth::user()->isInRole('viewer'))
				{!! Form::select('partner_id[]', $partners, null, ['class' => 'form-control js-select', 'multiple'=>true]) !!}
				@else
				{!! Form::select('partner_id[]', $partners, null, ['class' => 'form-control js-select', 'multiple'=>true, 'disabled' => 'disabled']) !!}
				@endif
			</div>

			<!-- Feedback Term Id Field -->
			<div class="form-group col-sm-12 feedback-select">
				{!! Form::label('feedback_term_id', __('app.Feedback Categories:')) !!}
				@if(!Auth::user()->isInRole('viewer'))
				{!! Form::select('feedback_term_id[]', $categoryTerms, null, ['class' => 'form-control js-select', 'multiple'=>true]) !!}
				@else
				{!! Form::select('feedback_term_id[]', $categoryTerms, null, ['class' => 'form-control js-select', 'multiple'=>true, 'disabled' => 'disabled']) !!}
				@endif
			</div>

			<!-- Password Field -->
			<div class="form-group col-sm-12">
				{!! Form::label('password', __('app.Password:')) !!}
				{!! Form::password('password', ['class' => 'form-control']) !!}
			</div>

			<div class="form-group col-sm-12">
				<label class="checkbox-inline">
					<div class="form-check form-check-flat mt-0">
						<label class="form-check-label">
							{!! Form::checkbox('active', null, null, ['class' => 'form-check-input']) !!}
							{{ __('app.Active') }}
						</label>
					</div>
				</label>
			</div>
		</div>
		<div class="col-md-6 user-photo-upload">

			<center>
				<div class="user-photo-container" id="image">
					@if($user->photo != null)
					<img id="preview_image" class="preview" src="{{ URL::to('/') }}/media/{{ $user->photo->filename }}" alt="">
					@else
					<img id="preview_image" class="preview" src="{{asset('media/profileimg.png')}}" />
					@endif
					<div id="loading" class="loading">
						<img src="{{asset('img/loader.svg')}}" alt="">
					</div>
				</div>
				<div class="mt-2">
					<button type="button" onclick="changeProfile()" class="btn btn-light btn-fw">
						<i class="mdi mdi-upload"></i> {{ __('app.Change') }}
					</button>&nbsp;&nbsp;
					<button type="button" onclick="removeFile()" class="btn btn-danger btn-fw">
						<i class="mdi mdi-delete"></i>
						{{ __('app.Remove') }}
					</button>
				</div>
				<input type="file" id="file" class="d-none" />
				{!! Form::hidden('photo_id', null, ['class' => 'form-control', 'id' => 'photo_id']) !!}
			</center>

		</div>
	</div>
</div>


<!-- Submit Field -->
<div class="form-group col-sm-12">
	{!! Form::submit(__('app.Save'), ['class' => 'btn btn-primary']) !!}
	<a href="{!! route('settings.users.index') !!}" class="btn btn-default">{{ __('app.Cancel') }}</a>
</div>


@section('scripts')
<script>
	function changeProfile() {
		$('#file').click();
	}
	$('#file').change(function() {
		if ($(this).val() != '') {
			upload(this);

		}
	});

	$('#role_id').on('change', checkRoleIsContact);
	checkRoleIsContact();

	function checkRoleIsContact(e) {
		if ($('#role_id').val() == 5) {
			$('.feedback-select').show();
		} else {
			$('.feedback-select').hide();
		}
	}

	function upload(img) {
		var form_data = new FormData();
		form_data.append('file', img.files[0]);
		form_data.append('_token', '{{csrf_token()}}');
		$('#loading').css('display', 'block');
		$.ajax({
			url: "{{url('admin/contents/medias/upload')}}",
			data: form_data,
			type: 'POST',
			contentType: false,
			processData: false,
			success: function(data) {
				if (data.id) {
					$('#photo_id').val(data.id);
					$('#preview_image').attr('src', '{{asset("media")}}/' + data.filename);
				} else if (data.fail) {
					$('#preview_image').attr('src', '{{asset("media/profileimg.png")}}');
					alert(data.errors['file']);
				}
				$('#loading').css('display', 'none');
			},
			error: function(xhr, status, error) {
				alert(xhr.responseText);
				$('#preview_image').attr('src', '{{asset("media/profileimg.png")}}');
			}
		});
	}

	function removeFile() {
		if ($('#photo_id').val() != '')
			if (confirm('<?= __('app.Are you sure want to remove profile picture?') ?>')) {
				$('#preview_image').attr('src', '{{asset("media/profileimg.png")}}');
				$('#photo_id').val('');
				$('#loading').css('display', 'none');
			}
	}
</script>
@endsection