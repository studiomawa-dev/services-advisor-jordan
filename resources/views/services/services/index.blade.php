@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-12">
		<div class="card index-card">
			<div class="card-header">
				<h4 class="card-title my-1 float-left">{{ __('app.Services') }}</h4>
				@if(!Auth::user()->isInRole('viewer'))
				<a class="btn btn-primary float-right ml-2" href="{!! route('services.services.create') !!}">{{ __('app.Add New') }}</a>
				@if(Auth::user()->isSysAdmin())
				<a class="btn btn-secondary float-right mr-2" target="_blank" href="{{ route('services.export') }}">{{ __('app.Export Services') }}</a>
				<a class="btn btn-secondary float-right mr-2" target="_blank" href="{{ route('services.import') }}">{{ __('app.Import Services') }}</a>
				@endif
				@endif
			</div>
			<div class="card-body px-0 py-0" style="display: flex;">

				<form id="filter-form" action="" class="px-1 py-1 table-filter-form" style="width: 20%;">
					<div class="form-row" style="display: flex; flex-direction: column;">

						<div class="col-sm-8 col-sm-12 mb-2 mb-lg-1">
							<select id="category-select" name="category" data-value="{{$category_id}}" class="form-control js-select" data-placeholder="{{ __('app.Select Category') }}">
								<option value="">{{ __('app.Select Category') }}</option>
								{!! $category_terms !!}
							</select>
						</div>

						<div class="col-sm-12 mb-2 mb-lg-1">
							{!! Form::select('partner', $partners, $partner_id, ['id'=>'partner-select', 'class' => 'form-control js-select', 'data-placeholder' => __('app.Select Organization')]) !!}
						</div>

						<div class="col-sm-12 mb-2 mb-lg-1">
							{!! Form::select('accessibility', $accessibility_terms, $accessibility_id, ['id'=>'accessibility-select', 'class' => 'form-control js-select', 'data-placeholder' => __('app.Select Accesibility')]) !!}
						</div>

						<div class="col-sm-12 mb-2 mb-lg-1">
							{!! Form::select('intake_criteria', $intake_criteria_terms, $intake_criteria_id, ['id'=>'intake-criteria-select', 'class' => 'form-control js-select', 'data-placeholder' => __('app.Select Intake Criteria')]) !!}
						</div>

						<div class="col-sm-12 mb-2 mb-lg-1">
							{!! Form::select('referral_method', $referral_method_terms, $referral_method_id, ['id'=>'referral_method-select', 'class' => 'form-control js-select', 'data-placeholder' => __('app.Select Referral Method')]) !!}
						</div>

						<div class="col-sm-12 mb-2 mb-lg-1">
							<select name="city" id="city-select" class="form-control js-select" data-placeholder="{{ __('app.Select City') }}"></select>
						</div>

						<div class="col-sm-12 mb-2 mb-lg-1">
							<select name="district" id="district-select" class="form-control js-select" data-placeholder="{{ __('app.Select District') }}"></select>
						</div>

						<div class="col-sm-12 mb-2 mb-lg-1">
							{!! Form::select('status', [1 => __('app.Published'), 2 => __('app.Unpublished'), 3 => __('app.Scheduled'), 4 => __('app.Expired')], $status_id, ['id'=>'status-select', 'class' => 'form-control js-select', 'placeholder' => __('app.Select Status')]) !!}
						</div>

						<div class="col-sm-12 mb-2 mb-lg-1 mt-3">
							<div class="row">
								<div class="col-6" style="padding-right: 5px;">
									<button class="btn btn-primary btn-block" type="submit" data-toggle="tooltip" title="{{ __('app.Filter') }}"><i class="mdi mdi-filter"></i></button>
								</div>
								<div class="col-6" style="padding-left: 5px;">
									<button class="btn btn-outline-primary btn-block" type="button" onclick="resetFilter()" data-toggle="tooltip" title="{{ __('app.Clear Filter') }}"><i class="mdi mdi-filter-remove"></i></button>
								</div>
							</div>
						</div>

					</div>
				</form>

				<div class="content" style="width: 80%;">
					<div class="clearfix"></div>

					@include('flash::message')

					<div class="clearfix"></div>
					<div class="box box-primary">
						<div class="box-body">
							@include('services.services.table')
						</div>
					</div>
					<div class="text-center">

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
	var status_id = <?= $status_id == null ? -1 : $status_id ?>;

	$(document).ready(function() {
		initCities();

		$('#category-select').val($('#category-select').data('value'));
		$('#category-select').select2({
			allowClear: true
		}).trigger('change');

		if (!(status_id > 0)) {
			$('#status-select').val('').trigger('change');
		}
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

			var selectedCity = parseInt(citySelect.val());
			if (isNaN(selectedCity) || selectedCity == 0) return;

			var city = cities.filter(item => {
				return item[0] == selectedCity;
			})[0];

			var districts = city.children;
			districtSelect.empty();

			$.each(districts, function() {
				districtSelect.append($("<option />").val(this[0]).text(this[2]))
			});
			districtSelect.val('');
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
		$('input[name=sid]').val('');
		//$('#tag-select').val('').trigger('change');
		$('#category-select').val('').trigger('change');
		$('#intake-criteria-select').val('').trigger('change');
		$('#city-select').val('').trigger('change');
		$('#district-select').val('').trigger('change');
		$('#partner-select').val('').trigger('change');
		$('#status-select').val('').trigger('change');
		$('#filter-form').submit();
	}
</script>
@endsection