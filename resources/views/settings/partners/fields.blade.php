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
					$('#logo_id').val(data.id);
					$('#preview_image').attr('src', '{{asset("media")}}/' + data.filename);
				} else if (data.fail) {
					$('#preview_image').attr('src', '{{asset("media/noimage.png")}}');
					alert(data.errors['file']);
				}
				$('#loading').css('display', 'none');
			},
			error: function(xhr, status, error) {
				alert(xhr.responseText);
				$('#preview_image').attr('src', '{{asset("media/noimage.png")}}');
			}
		});
	}

	function removeFile() {
		if ($('#logo_id').val() != '')
			if (confirm('Are you sure want to remove profile picture?')) {
				$('#preview_image').attr('src', '{{asset("media/noimage.png")}}');
				$('#logo_id').val('');
				$('#loading').css('display', 'none');
			}
	}
</script>
@endsection

<!-- Type Id Tag -->
<div class="form-group col-sm-6">
	{!! Form::label('tag_id', __('app.Tags:')) !!}
	{!! Form::select('tag_id', $tags, null, ['class' => 'form-control js-select']) !!}
</div>

<!-- Type Id Field -->
<div class="form-group col-sm-6">
	{!! Form::label('type_id', __('app.Type:')) !!}
	{!! Form::select('type_id', $partnerTypes, null, ['class' => 'form-control js-select']) !!}
</div>


<div class="form-group col-sm-6 partner-logo-upload">
	{!! Form::label('logo_id', __('app.Logo:')) !!}
	<center>
		<div class="partner-logo-container" id="image">
			@if($partner->logo != null)
			<img id="preview_image" class="preview" src="{{ URL::to('/') }}/media/{{ $partner->logo->filename }}" alt="">
			@else
			<img id="preview_image" class="preview" src="{{asset('media/noimage.png')}}" />
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
		{!! Form::hidden('logo_id', null, ['class' => 'form-control', 'id' => 'logo_id']) !!}
	</center>
</div>

<div class="col-sm-12 mt-3">
	<nav>
		<div class="nav nav-tabs" id="nav-tab" role="tablist">
			@foreach($langs as $lang)
			<a class="nav-item nav-link {{ $loop->index == 0 ? 'active' : '' }}" id="lang-{{ $lang->id }}-tab" data-toggle="tab" href="#lang-{{ $lang->id }}" role="tab" aria-controls="lang-{{ $lang->id }}"" aria-selected=" false">{{ $lang->name }}</a>
			@endforeach
		</div>
	</nav>
	<div class="tab-content">
		@foreach($langs as $lang)
		<div class="tab-pane fade {{ $loop->index == 0 ? 'show active' : '' }}" id="lang-{{ $lang->id }}" role="tabpanel" aria-labelledby="lang-{{ $lang->id }}-tab">
			<div class="row mt-3">
				<div class="form-group col-sm-12">
					{!! Form::label('langs[l'.$lang->id.'][name]', __('app.Name:')) !!}
					{!! Form::text('langs[l'.$lang->id.'][name]', null, ['class' => 'form-control']) !!}
				</div>
				<div class="form-group col-sm-12">
					{!! Form::label('langs[l'.$lang->id.'][full_name]', __('app.Full Name:')) !!}
					{!! Form::text('langs[l'.$lang->id.'][full_name]', null, ['class' => 'form-control']) !!}
				</div>
				<div class="form-group col-sm-12">
					{!! Form::label('langs[l'.$lang->id.'][url]', __('app.Link:')) !!}
					{!! Form::text('langs[l'.$lang->id.'][url]', null, ['class' => 'form-control']) !!}
				</div>
				<div class="form-group col-sm-12">
					{!! Form::label('langs[l'.$lang->id.'][description]', __('app.Description:')) !!}
					{!! Form::textarea('langs[l'.$lang->id.'][description]', null, ['class' => 'form-control']) !!}
				</div>
				{!! Form::hidden('langs[l'.$lang->id.'][slug]', null, ['class' => 'form-control']) !!}
			</div>
		</div>
		@endforeach
	</div>
</div>


<!-- Submit Field -->
<div class="form-group col-sm-12">
	{!! Form::submit(__('app.Save'), ['class' => 'btn btn-primary']) !!}
	<a href="{!! route('settings.partners.index') !!}" class="btn btn-default">{{ __('app.Cancel') }}</a>
</div>