@section('scripts')
<script>
    $(document).ready(function() {
        $('#sending_date').datetimepicker({
            format: 'Y-m-d H:i:s',
            mask: true,
            timepicker: false,
            useCurrent: false
        });
	});
</script>
@endsection

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
					{!! Form::label('langs[l'.$lang->id.'][title]', 'Title:') !!}
					{!! Form::text('langs[l'.$lang->id.'][title]', null, ['class' => 'form-control']) !!}
				</div>
				<div class="form-group col-sm-12">
					{!! Form::label('langs[l'.$lang->id.'][message]', 'Message:') !!}
					{!! Form::textarea('langs[l'.$lang->id.'][message]', null, ['class' => 'form-control']) !!}
				</div>
				{!! Form::hidden('langs[l'.$lang->id.'][payload]', null, ['class' => 'form-control']) !!}
			</div>
		</div>
		@endforeach
	</div>
</div>

<!-- Sending_date Field
<div class="form-group col-sm-6">
    {!! Form::label('sending_date', 'Sending Date:') !!}
	{!! Form::text('sending_date', null, ['class' => 'form-control','id'=>'sending_date', 'autocomplete' => 'off']) !!}
</div>-->

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('settings.notifications.index') !!}" class="btn btn-default">Cancel</a>
</div>
