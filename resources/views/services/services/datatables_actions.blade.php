{!! Form::open(['route' => ['services.services.destroy', $id], 'method' => 'delete', 'class' => 'text-center']) !!}
<div class='btn-group'>
    <a href="{{ route('services.services.show', $id) }}" class='btn btn-default btn-xs'>
        <i class="mdi mdi-eye-outline"></i>
    </a>
	@if((Auth::user()->isAdmin() || Auth::user()->isInPartner($partner)) && !Auth::user()->isInRole('viewer'))
    <a href="{{ route('services.services.edit', $id) }}" class='btn btn-default btn-xs'>
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
