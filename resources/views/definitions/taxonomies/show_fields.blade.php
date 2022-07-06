<!-- Id Field -->
<div class="form-group">
    {!! Form::label('id', 'Id:') !!}
    <p>{!! $taxonomy->id !!}</p>
</div>

<!-- Key Field -->
<div class="form-group">
    {!! Form::label('key', 'Key:') !!}
    <p>{!! $taxonomy->key !!}</p>
</div>

<!-- Order Field -->
<div class="form-group">
    {!! Form::label('order', 'Order:') !!}
    <p>{!! $taxonomy->order !!}</p>
</div>

<!-- Deleted Field -->
<div class="form-group">
    {!! Form::label('deleted', 'Deleted:') !!}
    <p>{!! $taxonomy->deleted !!}</p>
</div>

