{!! Form::open(['route' => ['contents.media.destroy', $id], 'method' => 'delete']) !!}
<div class='btn-group'>
    <a href="{{ route('contents.media.show', $id) }}" class='btn btn-default btn-xs'>
        <i class="mdi mdi-eye-outline"></i>
    </a>
    <a href="{{ route('contents.media.edit', $id) }}" class='btn btn-default btn-xs'>
        <i class="mdi mdi-square-edit-outline"></i>
    </a>
    {!! Form::button('<i class="mdi mdi-delete-outline"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-danger btn-xs',
        'onclick' => "return confirm('Are you sure?')"
    ]) !!}
</div>
{!! Form::close() !!}
