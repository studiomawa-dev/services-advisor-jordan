{!! Form::open(['route' => ['inbox.messages.destroy', $id], 'method' => 'delete']) !!}
<div class='btn-group'>
    <a href="{{ route('inbox.messages.show', $id) }}" class='btn btn-default btn-xs'>
        <i class="mdi mdi-eye-outline"></i>
    </a>
    <a href="{{ route('inbox.messages.edit', $id) }}" class='btn btn-default btn-xs'>
        <i class="mdi mdi-square-edit-outline"></i>
    </a>
    {!! Form::button('<i class="mdi mdi-delete-outline"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-danger btn-xs',
        'onclick' => "return confirm('Are you sure?')"
    ]) !!}
</div>
{!! Form::close() !!}
