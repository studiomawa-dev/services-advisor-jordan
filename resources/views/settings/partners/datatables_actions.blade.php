{!! Form::open(['route' => ['settings.partners.destroy', $id], 'method' => 'delete', 'class' => 'text-center']) !!}
<div class='btn-group'>
    <a href="{{ route('settings.partners.show', $id) }}" class='btn btn-default btn-xs'>
        <i class="mdi mdi-eye-outline"></i>
    </a>
	@if((Auth::user()->isAdmin() || Auth::user()->isInPartnerId($id)) && !Auth::user()->isInRole('viewer'))
    <a href="{{ route('settings.partners.edit', $id) }}" class='btn btn-default btn-xs'>
        <i class="mdi mdi-square-edit-outline"></i>
    </a>
	@endif
	@if(Auth::user()->isAdmin())
    {!! Form::button('<i class="mdi mdi-delete-outline"></i>', [
    'type' => 'submit',
    'class' => 'btn btn-danger btn-xs',
    'onclick' => "return confirm('Are you sure?')"
    ]) !!}
	@endif
</div>
{!! Form::close() !!}