{!! Form::open(['route' => ['services.contacts.destroy', $id], 'method' => 'delete', 'class' => 'text-center']) !!}
<div class='btn-group'>
    <a href="{{ route('services.contacts.show', $id) }}" class='btn btn-default btn-xs'>
        <i class="mdi mdi-eye-outline"></i>
    </a>
	@if(Auth::user()->isAdmin())
    <a href="{{ route('services.contacts.edit', $id) }}" class='btn btn-default btn-xs'>
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
