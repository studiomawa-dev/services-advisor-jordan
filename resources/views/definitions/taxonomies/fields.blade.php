<!-- Key Field -->
<div class="form-group col-sm-6">
    {!! Form::label('key', 'Key:') !!}
    {!! Form::text('key', null, ['class' => 'form-control']) !!}
</div>

<!-- Order Field -->
<div class="form-group col-sm-6">
    {!! Form::label('order', 'Order:') !!}
    {!! Form::number('order', null, ['class' => 'form-control']) !!}
</div>

<!-- Deleted Field -->
<div class="form-group col-sm-6">
    {!! Form::label('deleted', 'Deleted:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('deleted', 0) !!}
        {!! Form::checkbox('deleted', '1', null) !!} 1
    </label>
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('definitions.taxonomies.index') !!}" class="btn btn-default">Cancel</a>
</div>
