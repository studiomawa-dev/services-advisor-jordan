{!! Form::open(['route' => ['settings.notifications.destroy', $id], 'method' => 'delete', 'class' => 'text-center']) !!}
<div class='btn-group'>
    <a href="{{ route('settings.notifications.show', $id) }}" class='btn btn-default btn-xs'>
        <i class="mdi mdi-eye-outline"></i>
    </a>
	@if(Auth::user()->isAdmin())
    {!! Form::button('<i class="mdi mdi-delete-outline"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-danger btn-xs',
        'onclick' => "return confirm('Are you sure?')"
    ]) !!}
	@endif
</div>
{!! Form::close() !!}
