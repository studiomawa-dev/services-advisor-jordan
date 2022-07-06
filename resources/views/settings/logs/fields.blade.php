<!-- Level Field -->
<div class="form-group col-sm-6">
    {!! Form::label('level', 'Level:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('level', 0) !!}
        {!! Form::checkbox('level', '1', null) !!} 1
    </label>
</div>

<!-- Username Field -->
<div class="form-group col-sm-6">
    {!! Form::label('username', 'Username:') !!}
    {!! Form::text('username', null, ['class' => 'form-control']) !!}
</div>

<!-- Ipaddress Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ipaddress', 'Ipaddress:') !!}
    {!! Form::text('ipaddress', null, ['class' => 'form-control']) !!}
</div>

<!-- Category Field -->
<div class="form-group col-sm-6">
    {!! Form::label('category', 'Category:') !!}
    {!! Form::text('category', null, ['class' => 'form-control']) !!}
</div>

<!-- Type Field -->
<div class="form-group col-sm-6">
    {!! Form::label('type', 'Type:') !!}
    {!! Form::text('type', null, ['class' => 'form-control']) !!}
</div>

<!-- Message Field -->
<div class="form-group col-sm-6">
    {!! Form::label('message', 'Message:') !!}
    {!! Form::text('message', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('settings.logs.index') !!}" class="btn btn-default">Cancel</a>
</div>
