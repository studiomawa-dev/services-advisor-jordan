@extends('layouts.full')

@section('content')
<div class="row">
	<div class="col-12  px-0 py-0">
		<div class="card index-card">
			<div class="card-header">
				<h4 class="card-title my-1 float-left">{{ __('app.Locations') }}</h4>
				<button class="close float-right ml-3 pt-1" type="button" onclick="closeModal()">&times;</button>
				<a class="btn btn-primary float-right" href="{!! route('services.locations.create', ['city'=>$city, 'district'=>$district]) !!}" target="_blank">Add New</a>

			</div>
			<div class="card-body px-0 py-0">
				<div class="row">
					<div class="col-12">
						<div class="content">
							<div class="clearfix"></div>

							@include('flash::message')

							<div class="clearfix"></div>
							<div class="box box-primary">
								<div class="box-body location-modal">
									@include('services.locations.table')
								</div>
							</div>
							<div class="text-center">

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12 px-0">
		<div style="height: 200px">
			<div id='map' style="height: 200px"></div>
		</div>
	</div>
</div>

<script type="text/javascript">
	function closeModal() {
		if (window.parent != undefined && window.parent.closeLocationModal != undefined) {
			window.parent.closeLocationModal();
		}
	}

	function selectLocation(id) {
		if (window.parent != undefined && window.parent.selectLocation != undefined) {
			window.parent.selectLocation(id);
		}
	}
</script>

@endsection

@section('pagescripts')
<script type="text/javascript">
	var map, marker;

	$(document).bind('DOMNodeInserted', function(event) {
		if (map == undefined) {
			var selectBtn = $(event.target).find('.btn-map');
			if (selectBtn.length > 0) {
				selectBtn = $(selectBtn[0]);
				var lat = selectBtn.data('lat');
				var lng = selectBtn.data('lng');
				initMap(lat, lng);
			}
		}
		if (event.target.tagName.toLowerCase() == 'tr') {
			$(event.target).bind('click', function() {
				$('tr.selected-row').removeClass('selected-row');
				$(this).addClass('selected-row');
				var selectBtn = $(this).find('.btn-map');
				if (selectBtn.length > 0) {
					selectBtn = $(selectBtn[0]);
					var lat = selectBtn.data('lat');
					var lng = selectBtn.data('lng');
					showLocation(lat, lng);
				}
			});
		}
		//console.log('inserted ' + event.target.nodeName +' in ' + event.relatedNode.nodeName); // parent
	});

	function initMap(lat, lng) {
		mapboxgl.accessToken = 'pk.eyJ1IjoibG9tYmllIiwiYSI6IlAyVlJfU3MifQ.gMTKJU_NsIvulLTttw4-XA';
		var mapboxClient = mapboxSdk({
			accessToken: mapboxgl.accessToken
		});
		map = new mapboxgl.Map({
			container: 'map',
			style: 'mapbox://styles/mapbox/streets-v11',
			center: [lng, lat],
			zoom: 10
		});

		marker = new mapboxgl.Marker({
				draggable: true
			})
			.setLngLat([0, 0])
			.addTo(map);

		console.log('map initialized');
	}

	function showLocation(lat, lng) {
		if (map == undefined) return;

		var lngLat = [lng, lat];
		map.flyTo({
			center: lngLat,
			zoom: 13
		});
		marker.setLngLat(lngLat);
	}
</script>
@endsection