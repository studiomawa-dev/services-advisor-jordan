@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-12">
		<div class="card index-card">
			<div class="card-header">
				<h4 class="card-title my-1 float-left">{{ __('app.Locations') }}</h4>
				@if(!Auth::user()->isInRole('viewer'))
				<a class="btn btn-primary float-right" href="{!! route('services.locations.create') !!}">{{ __('app.Add New') }}</a>
				@endif
			</div>
			<div class="card-body px-0 py-0">
				<div class="row">
					<div class="col-12">
						<form id="filter-form" action="" class="px-1 py-1 table-filter-form">
							<div class="form-row">
								<div class="col-lg-4 col-md-6 col-sm-12 mb-2 mb-lg-0">
									{{ Form::text('src', $src, ['class' => 'form-control', 'placeholder' => __('app.Search Id or Name')]) }}
								</div>
								<div class="col-lg-3 col-md-6 col-sm-12 mb-2 mb-lg-0">
									<select name="city" id="city-select" class="form-control js-select" data-placeholder="{{ __('app.Select Province') }}"></select>
								</div>
								<div class="col-lg-3 col-md-6 col-sm-12 mb-2 mb-lg-0">
									<select name="district" id="district-select" class="form-control js-select" data-placeholder="{{ __('app.Select District') }}"></select>
								</div>
								<div class="col-6 col-lg-1 col-md-3 col-sm-6">
									<button class="btn btn-primary btn-block" type="submit"><i class="mdi mdi-filter"></i></button>
								</div>
								<div class="col-6 col-lg-1 col-md-3 col-sm-6">
									<button class="btn btn-outline-primary btn-block" type="button" onclick="resetFilter()" data-toggle="tooltip" title="Clear Filter"><i class="mdi mdi-filter-remove"></i></button>
								</div>
							</div>
						</form>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<div class="content">
							<div class="clearfix"></div>

							@include('flash::message')

							<div class="clearfix"></div>
							<div class="box box-primary">
								<div class="box-body">
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
@endsection

@section('additionalscripts')
<script>
	var locationsJson = JSON.parse('<?= json_encode($location_terms, JSON_HEX_APOS) ?>');
	var city_id = <?= $city_id == null ? -1 : $city_id ?>;
	var district_id = <?= $district_id == null ? -1 : $district_id ?>;

	$(document).ready(function() {
		initCities();
	});

	function initCities() {
		var citySelect = $('#city-select');
		var districtSelect = $('#district-select');

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

		var onCitySelect = function() {
			districtSelect.empty();
			districtSelect.prepend($("<option />").val(null).text('Select District'));

			var selectedCity = parseInt(citySelect.val());
			if (isNaN(selectedCity) || selectedCity == 0) return;

			var city = cities.filter(item => {
				return item[0] == selectedCity;
			})[0];

			var districts = city.children;

			$.each(districts, function() {
				districtSelect.append($("<option />").val(this[0]).text(this[2]))
			});
		}

		citySelect.empty();
		citySelect.append($("<option />").val(null).text('Select Province'));
		districtSelect.prepend($("<option />").val(null).text('Select District'));

		citySelect.on('change', onCitySelect);

		$.each(cities, function() {
			citySelect.append($("<option />").val(this[0]).text(this[2]))
		});

		if (city_id != undefined && city_id > 0) {
			citySelect.val(city_id);
			onCitySelect();
		}

		if (district_id != undefined && district_id > 0) {
			districtSelect.val(district_id);
		} else {
			districtSelect.val(null);
		}
	}

	function resetFilter() {
		$('input[name=src]').val('');
		$('#city-select').val('').trigger('change');
		$('#district-select').val('').trigger('change');
		$('#filter-form').submit();
	}
</script>
@endsection