
@if($published)
	@if(strtotime($publish_date)>time())
		<i class="mdi mdi-clock-outline" style="color: #ff6d00;vertical-align: text-top;font-size: 18px;" title="Scheduled"></i>
	@else
		@if(strtotime($end_date)<time())
			<i class="mdi mdi-calendar-remove-outline" style="color: #ea4235;vertical-align: text-top;font-size: 18px;" title="Expired"></i>
		@else
			@if($backendonly)
				<i class="mdi mdi-calendar-text-outline" style="color: #b7196e;vertical-align: text-top;font-size: 18px;" title="Backend Only"></i>
			@else
				<i class="mdi mdi-calendar-check-outline" style="color: #34a855;vertical-align: text-top;font-size: 18px;" title="Published"></i>
			@endif
		@endif
	@endif
@else
	<i class="mdi mdi-calendar-outline" style="color: #4385f5;vertical-align: text-top;font-size: 18px;" title="Unpublished"></i>
@endif

{{ $id }}

@if($is_remote)
	<img src="{{ url('/img/icon-remote.svg') }}" alt="Remote Service" title="Remote Service" style="width: 18px; margin-top: -2px;" />
@endif
