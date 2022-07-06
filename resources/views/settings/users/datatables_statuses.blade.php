@if(strtotime($last_action) > time() - 5000)
	<i class="mdi mdi-radiobox-marked" style="color: #5dacac;vertical-align: text-top;font-size: 18px;" title="Online"></i>
@else
	<i class="mdi mdi-radiobox-blank" style="color: #dddddd;vertical-align: text-top;font-size: 18px;" title="Offline ({{ date('d.m.Y H:i:s', strtotime($last_action)) }})"></i>
@endif
	{{ $id }}
