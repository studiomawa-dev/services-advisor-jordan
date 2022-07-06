@section('scripts')
<script>
	var location_id = <?= isset($location) && $location ? $location->id : 0 ?>;
	var location_city_id = <?= $location->city_id > 0 ? $location->city_id : ($city > 0 ? $city : 0) ?>;
	var location_district_id = <?= $location->district_id > 0 ? $location->district_id : ($district > 0 ? $district : 0) ?>;
	var location_sub_district_id = <?= $location->sub_district_id > 0 ? $location->sub_district_id : (isset($sub_district) && $sub_district > 0 ? $sub_district : 0) ?>;
	var location_neighborhood_id = <?= $location->neighborhood_id > 0 ? $location->neighborhood_id : (isset($neighborhood) && $neighborhood > 0 ? $neighborhood : 0) ?>;
	var locationsJson = JSON.parse('<?= json_encode($location_terms, JSON_HEX_APOS) ?>');

	mapboxgl.accessToken = 'pk.eyJ1IjoibG9tYmllIiwiYSI6IlAyVlJfU3MifQ.gMTKJU_NsIvulLTttw4-XA';
	var mapboxClient = mapboxSdk({
		accessToken: mapboxgl.accessToken
	});
	var coordinates = document.getElementById('coordinates');
	var map = new mapboxgl.Map({
		container: 'map',
		style: 'mapbox://styles/mapbox/streets-v11',
		center: [<?= $location->longitude ?>, <?= $location->latitude  ?>],
		zoom: <?= $location->longitude > 0 ? 15 : 5 ?>
	});

	var marker = new mapboxgl.Marker({
			draggable: true
		})
		.setLngLat([<?= $location->longitude ?>, <?= $location->latitude ?>])
		.addTo(map);

	var geocoder = new MapboxGeocoder({ // Initialize the geocoder
		accessToken: mapboxgl.accessToken, // Set the access token
		mapboxgl: mapboxgl, // Set the mapbox-gl instance
		marker: false, // Do not use the default marker style
	});

	function onDragEnd() {
		var lngLat = marker.getLngLat();
		coordinates.style.display = 'block';
		coordinates.innerHTML = 'Longitude: ' + lngLat.lng + '<br />Latitude: ' + lngLat.lat;
		$('input[name="latitude"]').val(lngLat.lat);
		$('input[name="longitude"]').val(lngLat.lng);
	}

	marker.on('dragend', onDragEnd);

	$(document).ready(function() {
		initCities();
		var disabledAttr = $('.service-locations').closest("form").attr('disabled');
		if (typeof disabledAttr !== typeof undefined && disabledAttr !== false) {
			marker.setDraggable(false);
			marker.off('dragend', onDragEnd);
		}

		$('.service-locations').closest("form").submit(function(event) {
			event.preventDefault();

			if (validateForm()) {
				$(this).unbind('submit').submit();
			} else {
				$([document.documentElement, document.body]).animate({
					scrollTop: $('.error').first().offset().top - 180
				}, 1000);
			}
		})
	});

	function initCities() {
		var citySelect = $('#city-select');
		var districtSelect = $('#district-select');
		var subDistrictSelect = $('#subDistrict-select');
		var neighborhoodSelect = $('#neighborhood-select');
		var latInput = $('#latitude');
		var lngInput = $('#longitude');

		var currentCity = [];
		var currentDistrict = [];
		var currentSubDistrict = [];
		var currentNeighborhood = [];

		var locations = [];

		for (var i = 0; i < locationsJson.length; i++) {
			var location = locationsJson[i];
			location.children = locationsJson.filter(item => {
				return item[1] == location[0];
			});

			locations.push(location);
		}

		var cities = locations.filter(item => {
			return item[1] == 0;
		});

		var onCitySelect = function(e) {

			var selectedCity = parseInt(citySelect.val());
			if (isNaN(selectedCity) || selectedCity == 0) return;

			currentCity = cities.filter(item => {
				return item[0] == selectedCity;
			})[0];

			var districts = currentCity.children;

			districtSelect.empty();
			districtSelect.append($("<option />").val(null).text('<?= __('app.Select District') ?>'))
			$.each(districts, function() {
				districtSelect.append($("<option />").attr('selected', this[0] === location_district_id ? "selected" : null).val(this[0]).text(this[2]))
			});

			if (location_district_id) {
				onDistrictSelect();
			}

			if (typeof e !== 'undefined')
				updateMap(07);
		}

		var onDistrictSelect = function(e) {

			if (currentCity.length == 0) return;

			var selectedDistrict = parseInt(districtSelect.val());
			if (isNaN(selectedDistrict) || selectedDistrict == 0) return;

			var districts = currentCity.children;

			currentDistrict = districts.filter(item => {
				return item[0] == selectedDistrict;
			})[0];

			var subDistricts = currentDistrict.children;

			subDistrictSelect.empty();
			subDistrictSelect.append($("<option />").val(null).text('<?= __('app.Select Sub District') ?>'))
			$.each(subDistricts, function() {
				subDistrictSelect.append($("<option />").attr('selected', this[0] === location_sub_district_id ? "selected" : null).val(this[0]).text(this[2]))
			});

			if (location_sub_district_id) {
				onSubDistrictSelect();
			}

			if (typeof e !== 'undefined')
				updateMap(12);
		};

		var onSubDistrictSelect = function(e) {

			var selectedSubDistrict = parseInt(subDistrictSelect.val());
			if (isNaN(selectedSubDistrict) || selectedSubDistrict == 0) return;

			var subDistricts = currentDistrict.children;

			currentSubDistrict = subDistricts.filter(item => {
				return item[0] == selectedSubDistrict;
			})[0];

			var neighborhoods = currentSubDistrict.children;

			neighborhoodSelect.empty();
			neighborhoodSelect.append($("<option />").val(null).text('<?= __('app.Select Neighborhood') ?>'))

			$.each(neighborhoods, function() {
				neighborhoodSelect.append($("<option />").attr('selected', this[0] === location_neighborhood_id ? "selected" : null).val(this[0]).text(this[2]))
			});

			if (location_neighborhood_id) {
				onNeighborhoodSelect();
			}

			if (typeof e !== 'undefined')
				updateMap(14);
		};

		var onNeighborhoodSelect = function(e) {

			if (typeof e !== 'undefined')
				updateMap(16);
		}

		var onLngLatChange = function() {
			var lat = latInput.val();
			var lng = lngInput.val();
			if (lat != undefined && parseFloat(lat) > 0 &&
				lng != undefined && parseFloat(lng) > 0) {
				centerMap([lng, lat]);
			}
		}

		citySelect.empty();
		citySelect.append($("<option />").val(null).text('<?= __('app.Select Province') ?>'));

		citySelect.on('change', onCitySelect);
		districtSelect.on('change', onDistrictSelect);
		subDistrictSelect.on('change', onSubDistrictSelect);
		neighborhoodSelect.on('change', onNeighborhoodSelect);

		latInput.on('change keyup', onLngLatChange);
		lngInput.on('change keyup', onLngLatChange);

		$.each(cities, function() {
			citySelect.append($("<option />").attr('selected', this[0] === location_city_id ? "selected" : null).val(this[0]).text(this[2]))
		});

		if (citySelect.val() > 0) {
			onCitySelect();
		} else {
			districtSelect.empty();
			districtSelect.append($("<option />").val(null).text('<?= __('app.Select District') ?>'))

			subDistrictSelect.empty();
			subDistrictSelect.append($("<option />").val(null).text('<?= __('app.Select Sub District') ?>'))

			neighborhoodSelect.empty();
			neighborhoodSelect.append($("<option />").val(null).text('<?= __('app.Select Neighborhood') ?>'))
		}

		if (location_city_id === 0 && location_district_id === 0 && location_sub_district_id === 0) {
			searchMap('<?php echo env("APP_COUNTRY") ?>', 4.5);
		} else if (location_city_id > 0 && location_district_id > 0 && location_sub_district_id > 0 && <?= $location->latitude ?> === 0 && <?= $location->longitude ?> === 0) {
			updateMap(location_id > 0 ? 18 : 12);
		}
	}

	function validateForm() {
		var hasError = false;
		$(".error").remove();

		var cityId = $('#city-select').val();
		if (isNaN(parseInt(cityId))) {
			hasError = true;
			$('#city-select').after('<span class="error">This field is required</span>');
		}

		var districtId = $('#district-select').val();
		if (isNaN(parseInt(districtId))) {
			hasError = true;
			$('#district-select').after('<span class="error">This field is required</span>');
		}

		/* var subDistrictId = $('#subDistrict-select').val();
		if (isNaN(parseInt(subDistrictId))) {
			hasError = true;
			$('#subDistrict-select').after('<span class="error">This field is required</span>');

		}
		var neighborhoodId = $('#neighborhood-select').val();
		if (isNaN(parseInt(neighborhoodId))) {
			hasError = true;
			$('#neighborhood-select').after('<span class="error">This field is required</span>');
		} */

		var langName = $('input[name="langs[l1][name]"]').val();
		if (langName == undefined || !(langName.length > 0)) {
			hasError = true;
			$('input[name="langs[l1][name]"]').after('<span class="error">This field is required</span>');
		}

		return !hasError;
	}

	function updateMap(zoom) {
		console.log(zoom);
		var citySelect = $('#city-select');
		var districtSelect = $('#district-select');
		var subDistrictSelect = $('#subDistrict-select');
		var neighborhoodSelect = $('#neighborhood-select');

		//var query = subDistrictSelect.find('option:selected').text() + ', ' + districtSelect.find('option:selected').text() + ', ' + citySelect.find('option:selected').text() + ', Jordan';

		let query = [];

		query.push('<?php echo env("APP_COUNTRY") ?>');

		if (citySelect.find('option:selected').val() > 0) {
			query.push(citySelect.find('option:selected').text())
		}

		if (districtSelect.find('option:selected').val() > 0) {
			query.push(districtSelect.find('option:selected').text())
		}

		if (subDistrictSelect.find('option:selected').val() > 0) {
			query.push(subDistrictSelect.find('option:selected').text())
		}

		if (neighborhoodSelect.find('option:selected').val() > 0) {
			query.push(neighborhoodSelect.find('option:selected').text())
		}
		searchMap(query.join('+'), zoom);
	}

	function centerMap(lngLat, zoom) {
		if (zoom == undefined) zoom = 12;
		map.flyTo({
			center: lngLat,
			zoom: zoom
		});
		marker.setLngLat(lngLat);
		onDragEnd();
	}

	function searchMap(query, zoom) {
		mapboxClient.geocoding.forwardGeocode({
				query: query,
				autocomplete: false,
				limit: 1
			})
			.send()
			.then(function(response) {
				if (response && response.body && response.body.features && response.body.features.length) {
					var feature = response.body.features[0];
					centerMap(feature.center, zoom);
				}
			});
	}
</script>
@endsection

{!! Form::hidden('country_id', null, ['class' => 'form-control']) !!}

<div class="col-md-12">
	<div class="row service-locations">

		<!-- City Id Field -->
		<div class="form-group col-sm-3">
			{!! Form::label('city_id', __('app.Province:')) !!}
			{!! Form::select('city_id', [], null, ['class' => 'form-control', 'id' => 'city-select']) !!}
		</div>

		<!-- District Id Field -->
		<div class="form-group col-sm-3">
			{!! Form::label('district_id', __('app.District:')) !!}
			{!! Form::select('district_id', [], null, ['class' => 'form-control', 'id' => 'district-select']) !!}
		</div>

		<!-- Sub District Id Field -->
		<div class="form-group col-sm-3">
			{!! Form::label('sub_district_id', __('app.Sub District:')) !!}
			{!! Form::select('sub_district_id', [], null, ['class' => 'form-control', 'id' => 'subDistrict-select']) !!}
		</div>

		<!-- Neighborhood Id Field -->
		<div class="form-group col-sm-3">
			{!! Form::label('neighborhood_id', __('app.Neighborhood:')) !!}
			{!! Form::select('neighborhood_id', [], null, ['class' => 'form-control', 'id' => 'neighborhood-select']) !!}
		</div>

		<!-- Latitude Field -->
		<div class="form-group col-sm-6">
			{!! Form::label('latitude', __('app.Latitude:')) !!}
			{!! Form::text('latitude', null, ['class' => 'form-control']) !!}
		</div>

		<!-- Longitude Field -->
		<div class="form-group col-sm-6">
			{!! Form::label('longitude', __('app.Longitude:')) !!}
			{!! Form::text('longitude', null, ['class' => 'form-control']) !!}
		</div>
	</div>
</div>

<div class="col-md-12">
	<div style="height: 400px">
		<div id='map' style="height: 400px"></div>
		<pre id='coordinates' class='coordinates' style="background: rgba(0,0,0,0.5);color: #fff;position: absolute;bottom: 40px;left: 18px;padding:5px 10px;margin: 0;font-size: 11px;line-height: 18px;border-radius: 3px;display: none;"></pre>
	</div>
</div>


<div class="col-sm-12 mt-3">
	<nav>
		<div class="nav nav-tabs" id="nav-tab" role="tablist">
			@foreach($langs as $lang)
			<a class="nav-item nav-link {{ $loop->index == 0 ? 'active' : '' }}" id="lang-{{ $lang->id }}-tab" data-toggle="tab" href="#lang-{{ $lang->id }}" role="tab" aria-controls="lang-{{ $lang->id }}"" aria-selected=" false">{{ $lang->name }}</a>
			@endforeach
		</div>
	</nav>
	<div class="tab-content">
		@foreach($langs as $lang)
		<div class="tab-pane fade {{ $loop->index == 0 ? 'show active' : '' }}" id="lang-{{ $lang->id }}" data-lang-id="{{ $lang->id }}" role="tabpanel" aria-labelledby="lang-{{ $lang->id }}-tab">
			<div class="row">
				<div class="form-group col-sm-12 mt-3">
					{!! Form::label('langs[l'.$lang->id.'][name]', __('app.Title:')) !!}
					{!! Form::text('langs[l'.$lang->id.'][name]', null, ['class' => 'form-control']) !!}
				</div>
				<div class="form-group col-sm-12">
					{!! Form::label('langs[l'.$lang->id.'][address]', __('app.Address:')) !!}
					{!! Form::textarea('langs[l'.$lang->id.'][address]', null, ['class' => 'form-control', 'rows' => 4]) !!}
				</div>
				<div class="form-group col-sm-12">
					{!! Form::label('langs[l'.$lang->id.'][direction]', __('app.Direction:')) !!}
					{!! Form::textarea('langs[l'.$lang->id.'][direction]', null, ['class' => 'form-control', 'rows' => 4]) !!}
				</div>
				<div class="form-group col-sm-12">
					<button type="button" href="{!! route('services.locations.index') !!}" class="btn btn-sm btn-outline-primary mb-3 copy-btn" onclick="copyToOthers({{ $lang->id }})"><i class="mdi mdi-content-duplicate"></i><span class="text">{{ __('app.Copy to other languages') }}</span></button>
				</div>
			</div>
		</div>
		@endforeach


	</div>
</div>

<!-- Submit Field -->
<div class="footer-buttons form-group col-sm-12">
	{!! Form::submit(__('app.Save'), ['class' => 'btn btn-primary']) !!}
	<a href="{!! route('services.locations.index') !!}" class="btn btn-default">{{ __('app.Cancel') }}</a>
</div>

@section('additionalscripts')
<script>
	function copyToOthers(mainLangId) {
		var langIds = $('.tab-content .tab-pane').map(function() {
			return $(this).data('lang-id');
		}).get();

		if (langIds != undefined && langIds.length > 1) {
			for (var i = 0; i < langIds.length; i++) {
				var langId = langIds[i];
				if (mainLangId != langId) {
					$('input[name="langs[l' + langId + '][name]"]').val($('input[name="langs[l' + mainLangId + '][name]').val());
					$('textarea[name="langs[l' + langId + '][address]"]').val($('textarea[name="langs[l' + mainLangId + '][address]').val());
					$('textarea[name="langs[l' + langId + '][direction]"]').val($('textarea[name="langs[l' + mainLangId + '][direction]').val());
				}
			}
		}

		$('.copy-btn .text').text('Copied!');
		setTimeout(() => {
			$('.copy-btn .text').text('<?= __('app.Copy to other languages') ?>');
		}, 1000);
	}
</script>
@endsection