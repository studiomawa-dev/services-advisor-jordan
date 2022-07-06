<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', __('app.Name:')) !!}
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
</div>

<!-- Email Field -->
<div class="form-group col-sm-6">
    {!! Form::label('email', __('app.Email:')) !!}
    {!! Form::email('email', null, ['class' => 'form-control']) !!}
</div>

<!-- Phone Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('phone', __('app.Phone:')) !!}
    {!! Form::textarea('phone', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit(__('app.Save'), ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('services.contacts.index') !!}" class="btn btn-default">{{ __('app.Cancel') }}</a>
</div>