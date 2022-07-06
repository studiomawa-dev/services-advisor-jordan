<!-- Name Field -->
<div class="form-group col-sm-6">
	{!! Form::label('name', __('app.Name:')) !!}
	{!! Form::text('name', null, ['class' => 'form-control']) !!}
</div>

<!-- Code Field -->
<div class="form-group col-sm-6">
	{!! Form::label('code', __('app.Code:')) !!}
	{!! Form::text('code', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group  col-sm-6">
	<div class="form-check form-check-flat mt-0">
		<label class="form-check-label">
			{!! Form::hidden('is_rtl', 0) !!}
			{!! Form::checkbox('is_rtl', '1', null, ['class' => 'form-check-input']) !!}
			{{ __('app.Is RTL') }}
			<i class="input-helper"></i>
		</label>
	</div>
</div>

<div class="form-group  col-sm-6">
	<div class="form-check form-check-flat mt-0">
		<label class="form-check-label">
			{!! Form::hidden('is_default', 0) !!}
			{!! Form::checkbox('is_default', '1', null, ['class' => 'form-check-input']) !!}
			{{ __('app.Is Default') }}
			<i class="input-helper"></i>
		</label>
	</div>
</div>

<div class="form-group  col-sm-6">
	<div class="form-check form-check-flat mt-0">
		<label class="form-check-label">
			{!! Form::hidden('is_backend', 0) !!}
			{!! Form::checkbox('is_backend', '1', null, ['class' => 'form-check-input']) !!}
			{{ __('app.Use On Backend') }}
			<i class="input-helper"></i>
		</label>
	</div>
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
	{!! Form::submit(__('app.Save'), ['class' => 'btn btn-primary']) !!}
	<a href="{!! route('settings.languages.index') !!}" class="btn btn-default">{{ __('app.Cancel') }}</a>
</div>