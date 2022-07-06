{!! Form::open(['route' => ['services.locations.destroy', $id], 'method' => 'delete', 'class' => 'text-center']) !!}
<div class='btn-group table-actions'>
	<a href="{{ route('services.locations.show', $id) }}" class='btn btn-default btn-xs'>
		<i class="mdi mdi-eye-outline"></i>
	</a>
	@if((Auth::user()->isAdmin() || Auth::user()->isInPartnerIds(explode(',', $partner_ids == null ? '' : $partner_ids))) && !Auth::user()->isInRole('viewer'))
	<a href="{{ route('services.locations.edit', $id) }}" class='btn btn-default btn-xs'>
		<i class="mdi mdi-square-edit-outline"></i>
	</a>
	@endif
	<button type="button" class='btn btn-default btn-xs d-none btn-select btn-map' data-lat="{{ $latitude }}" data-lng="{{ $longitude }}" style="border-right: 1px solid #d5dcec;">
		<i class="mdi mdi-map-marker"></i>
	</button>
	<button type="button" onclick="selectLocation({{ $id }})" class='btn btn-default btn-xs btn-block d-none btn-select' style="font-size: .71rem;">
		Select
	</button>
	@if((Auth::user()->isAdmin() || Auth::user()->isInPartnerIds(explode(',', $partner_ids == null ? '' : $partner_ids))) && !Auth::user()->isInRole('viewer'))
	{!! Form::button('<i class="mdi mdi-delete-outline"></i>', [
	'type' => 'submit',
	'class' => 'btn btn-danger btn-xs',
	'onclick' => "return confirm('Are you sure?')"
	]) !!}
	@endif
</div>
{!! Form::close() !!}
