{!! Form::open(['route' => ['settings.logs.destroy', $id], 'method' => 'delete']) !!}
<div class='btn-group'>
    <a href="{{ route('settings.logs.show', $id) }}" class='btn btn-default btn-xs'>
        <i class="mdi mdi-eye-outline"></i>
    </a>
    <a href="{{ route('settings.logs.edit', $id) }}" class='btn btn-default btn-xs'>
        <i class="mdi mdi-square-edit-outline"></i>
    </a>
    {!! Form::button('<i class="mdi mdi-delete-outline"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-danger btn-xs',
        'onclick' => "return confirm('Are you sure?')"
    ]) !!}
</div>
{!! Form::close() !!}
