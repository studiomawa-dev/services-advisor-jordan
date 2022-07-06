{!! Form::open(['route' => ['settings.users.destroy', $id], 'method' => 'delete', 'class' => 'text-center']) !!}
<div class='btn-group'>
    <a href="{{ url('/admin/profile/'. $username) }}" class='btn btn-default btn-xs'>
        <i class="mdi mdi-eye-outline"></i>
    </a>

	@if((Auth::user()->isAdmin() || Auth::user()->isInPartners($partners)) && !Auth::user()->isInRole('viewer'))
    <a href="{{ route('settings.users.edit', $id) }}" class='btn btn-default btn-xs'>
        <i class="mdi mdi-square-edit-outline"></i>
    </a>
    {!! Form::button('<i class="mdi mdi-delete-outline"></i>', [
    'type' => 'submit',
    'class' => 'btn btn-danger btn-xs',
    'onclick' => "return confirm('Are you sure?')"
    ]) !!}
	@endif
</div>
{!! Form::close() !!}


