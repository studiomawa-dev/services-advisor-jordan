<!-- Id Field -->
<div class="form-group">
    {!! Form::label('id', 'Id:') !!}
    <p>{!! $term->id !!}</p>
</div>

<!-- Taxonomy Id Field -->
<div class="form-group">
    {!! Form::label('taxonomy_id', 'Taxonomy Id:') !!}
    <p>{!! $term->taxonomy_id !!}</p>
</div>

<!-- Order Field -->
<div class="form-group">
    {!! Form::label('order', 'Order:') !!}
    <p>{!! $term->order !!}</p>
</div>

<!-- Deleted Field -->
<div class="form-group">
    {!! Form::label('deleted', 'Deleted:') !!}
    <p>{!! $term->deleted !!}</p>
</div>

