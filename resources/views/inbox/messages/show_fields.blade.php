<!-- Id Field -->
<div class="form-group">
    {!! Form::label('id', 'Id:') !!}
    <p>{!! $message->id !!}</p>
</div>

<!-- From Field -->
<div class="form-group">
    {!! Form::label('from', 'From:') !!}
    <p>{!! $message->from !!}</p>
</div>

<!-- To Field -->
<div class="form-group">
    {!! Form::label('to', 'To:') !!}
    <p>{!! $message->to !!}</p>
</div>

<!-- Title Field -->
<div class="form-group">
    {!! Form::label('title', 'Title:') !!}
    <p>{!! $message->title !!}</p>
</div>

<!-- Body Field -->
<div class="form-group">
    {!! Form::label('body', 'Body:') !!}
    <p>{!! $message->body !!}</p>
</div>

<!-- Is Read Field -->
<div class="form-group">
    {!! Form::label('is_read', 'Is Read:') !!}
    <p>{!! $message->is_read !!}</p>
</div>

<!-- Deleted Field -->
<div class="form-group">
    {!! Form::label('deleted', 'Deleted:') !!}
    <p>{!! $message->deleted !!}</p>
</div>

