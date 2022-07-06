<!-- Filename Field -->
<div class="form-group col-sm-6">
    {!! Form::label('filename', 'Filename:') !!}
    {!! Form::text('filename', null, ['class' => 'form-control']) !!}
</div>

<!-- Filemime Field -->
<div class="form-group col-sm-6">
    {!! Form::label('filemime', 'Filemime:') !!}
    {!! Form::text('filemime', null, ['class' => 'form-control']) !!}
</div>

<!-- Type Field -->
<div class="form-group col-sm-6">
    {!! Form::label('type', 'Type:') !!}
    {!! Form::text('type', null, ['class' => 'form-control']) !!}
</div>

<!-- Filesize Field -->
<div class="form-group col-sm-6">
    {!! Form::label('filesize', 'Filesize:') !!}
    {!! Form::number('filesize', null, ['class' => 'form-control']) !!}
</div>

<!-- Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('status', 'Status:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('status', 0) !!}
        {!! Form::checkbox('status', '1', null) !!} 1
    </label>
</div>

<!-- Width Field -->
<div class="form-group col-sm-6">
    {!! Form::label('width', 'Width:') !!}
    {!! Form::number('width', null, ['class' => 'form-control']) !!}
</div>

<!-- Height Field -->
<div class="form-group col-sm-6">
    {!! Form::label('height', 'Height:') !!}
    {!! Form::number('height', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('contents.media.index') !!}" class="btn btn-default">Cancel</a>
</div>
