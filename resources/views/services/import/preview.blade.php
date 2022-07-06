@section('scripts')
<script>
	function parseJson(value) {
		value = value.replace(/\\n/g, "\\n")
			.replace(/\\"/g, '\\"')
			.replace(/\\&/g, "\\&")
			.replace(/\\r/g, "\\r")
			.replace(/\\t/g, "\\t")
			.replace(/\\b/g, "\\b")
			.replace(/\\f/g, "\\f");
		value = value.replace(/[\u0000-\u0019]+/g, "");
		return JSON.parse(value);
	}

	let turkishChars = {
		"ı": "i",
		"İ": "I",
		"ğ": "g",
		"Ğ": "G",
		"ü": "u",
		"Ü": "U",
		"ş": "s",
		"Ş": "S",
		"ö": "o",
		"Ö": "O",
		"ç": "c",
		"Ç": "C",
	};
	var regExpTR = new RegExp(Object.keys(turkishChars).join("|"), "gi");

	async function init_import() {

		let locationsJson = parseJson('<?= json_encode($inputValues['locations'], JSON_HEX_APOS) ?>');
		let organizationsJson = parseJson('<?= json_encode($inputValues['organizations'], JSON_HEX_APOS) ?>');
		let categoriesJson = parseJson('<?= json_encode($inputValues['categories'], JSON_HEX_APOS) ?>');
		let accessibility_terms_Json = parseJson('<?= json_encode($inputValues['accessibility_terms'], JSON_HEX_APOS) ?>');
		let legal_documents_required_Json = parseJson('<?= json_encode($inputValues['legal_documents_required'], JSON_HEX_APOS) ?>');
		let nationality_Json = parseJson('<?= json_encode($inputValues['nationality'], JSON_HEX_APOS) ?>');
		let gender_and_age_Json = parseJson('<?= json_encode($inputValues['gender_and_age'], JSON_HEX_APOS) ?>');
		let intake_criteria_terms_Json = parseJson('<?= json_encode($inputValues['intake_criteria_terms'], JSON_HEX_APOS) ?>');
		let coverage_terms = parseJson('<?= json_encode($inputValues['coverage_terms'], JSON_HEX_APOS) ?>');
		let referral_method_terms = parseJson('<?= json_encode($inputValues['referral_method_terms'], JSON_HEX_APOS) ?>');
		let referral_next_step_terms = parseJson('<?= json_encode($inputValues['referral_next_step_terms'], JSON_HEX_APOS) ?>');
		let response_delay_terms = parseJson('<?= json_encode($inputValues['response_delay_terms'], JSON_HEX_APOS) ?>');
		let feedback_mechanism_terms = parseJson('<?= json_encode($inputValues['feedback_mechanism_terms'], JSON_HEX_APOS) ?>');
		let feedback_delay_terms = parseJson('<?= json_encode($inputValues['feedback_delay_terms'], JSON_HEX_APOS) ?>');
		let complaints_mechanism_terms = parseJson('<?= json_encode($inputValues['complaints_mechanism_terms'], JSON_HEX_APOS) ?>');

		let _locations = [];

		for (let i = 0; i < locationsJson.length; i++) {
			let _location = locationsJson[i];
			_location.children = locationsJson.filter(item => {
				return item[1] == _location[0];
			});

			_locations.push(_location);
		}

		let cities = _locations.filter(item => {
			return item[1] == 0;
		});

		let onCityChange = function(citySelectInput) {

			let provinceCity = $(citySelectInput);
			let districtSelectInput = provinceCity.closest('tr').find('.district');

			selectedCity = provinceCity.val();

			if (isNaN(selectedCity) || selectedCity == 0) return;

			let city = cities.filter(item => {
				return item[0] == selectedCity;
			})[0];

			let districts = city.children;

			districtSelectInput.empty();

			$.each(districts, function() {

				let district_name = this[2];

				$(districtSelectInput).append($("<option />").val(this[0]).text(district_name));

				let file_district_name = $(districtSelectInput).parent().find('.cell_value').text();

				file_district_name = file_district_name.replace(regExpTR, function(matched) {
					return turkishChars[matched];
				});

				district_name = district_name.replace(regExpTR, function(matched) {
					return turkishChars[matched];
				});

				if (file_district_name == district_name) {
					$(districtSelectInput).find('option:last').attr('selected', 'selected');
				}
			});

			if ($(districtSelectInput).find('option:selected').length <= 0) {
				$(districtSelectInput).val('');
			}
		}

		let citySelect = $('.city');
		$(citySelect).each(function(k, cs) {
			$.each(cities, function() {

				let file_city_name = $(cs).parent().find('.cell_value').text();
				let city_name = this[2];

				$(cs).append($("<option />").val(this[0]).text(city_name));

				file_city_name = file_city_name.replace(regExpTR, function(matched) {
					return turkishChars[matched];
				});

				city_name = city_name.replace(regExpTR, function(matched) {
					return turkishChars[matched];
				});

				console.log(file_city_name, city_name);

				if (file_city_name == city_name) {
					$(cs).find('option:last').attr('selected', 'selected');
				}
			});

			onCityChange(cs);

			$(cs).on('change', function() {
				onCityChange(this);
			});
		});


		$('input[name*="start_date"], input[name*="end_date"]').datetimepicker({
			format: 'Y-m-d',
			mask: true,
			timepicker: false,
			useCurrent: false
		});

		loadSelectInput('.partner_id', organizationsJson);
		loadSelectInput('.categories', categoriesJson);
		loadSelectInput('.accessibility', accessibility_terms_Json);
		loadSelectInput('.legal_documents_required', legal_documents_required_Json);
		loadSelectInput('.nationality', nationality_Json);
		loadSelectInput('.gender_age', gender_and_age_Json);
		loadSelectInput('.intake_criteria', intake_criteria_terms_Json);
		loadSelectInput('.coverage', coverage_terms);
		loadSelectInput('.referral_method', referral_method_terms);
		loadSelectInput('.immediate_next_step_after_referral', referral_next_step_terms);
		loadSelectInput('.response_delay_after_referral', response_delay_terms);
		loadSelectInput('.feedback_mechanism', feedback_mechanism_terms);
		loadSelectInput('.feedback_delay', feedback_delay_terms);
		loadSelectInput('.complaints_mechanism', complaints_mechanism_terms);

		$('input[type="text"]').each((_, textInput) => {
			if ($(textInput).is("[name*='availability']")) {
				if ($(textInput).val()) {
					let validDays = ['sunday', 'sun', 'monday', 'mon', 'tuesday', 'tue',
						'wednesday', 'wed', 'thursday', 'thu', 'friday', 'fri',
						'saturday', 'sat', 'weekdays'
					];
					let availabilityInfo = $(textInput).val().split(';');
					if (availabilityInfo.length > 0) {
						availabilityInfo.forEach((avi) => {
							let aviContext = avi.split('-');
							if ($.inArray(aviContext[0].toLowerCase(), validDays) < 0) {
								addErrorRow(textInput, "Wrong Day Format: " + aviContext[0]);

							}
						});
					}
				}
			}
		});

		if ($('#terms-table tr.has-error').length > 0) {
			$('#process-info-items .error-count span').text($('#terms-table tr.has-error').length);
			$('#process-info-items .error-count').show();
		}

		let serviceCount = $('#process-info-items .service-count span').text();
		let errorCount = $('#process-info-items .error-count span').text()
		let successCount = parseInt(serviceCount) - parseInt(errorCount);

		$('#process-info-items .success-count span').text(successCount);

		return Promise.resolve(true);
	}

	function addErrorRow(input, error) {
		$(input).closest('tr').addClass('bg-warning has-error');
		let item_cell_value = $(input).parent().find('.cell_value');
		item_cell_value.append('<div class="bg-danger m-1 p-1">- [INVALID VALUE] : ' + error + '</div>');
	}

	function loadSelectInput(name, data) {
		$($(name)).each(function(k, cs) {

			let item_cell_value = $(cs).parent().find('.cell_value');
			let currentValue = item_cell_value.text();

			$.each(data, function(key, val) {
				$(cs).append($("<option />").val(key).text(val));
				if (currentValue.includes(';') === true) {
					if (currentValue.split(";").includes(val)) {
						$(cs).find('option:last').attr('selected', 'selected');
					}
				} else {
					if ($(cs).parent().find('.cell_value').text() == val) {
						$(cs).find('option:last').attr('selected', 'selected');
					}
				}
			});

			if (currentValue) {
				if (currentValue.includes(';') === true) {
					let currentValues = currentValue.split(";");
					let validatedValues = $(cs).find('option:selected').toArray().map(item => item.text);

					let diff = $(currentValues).not(validatedValues).get();
					if (diff.length > 0) {
						$(cs).closest('tr').addClass('bg-warning has-error');

						diff.forEach(function(wrongItem) {
							addErrorRow(cs, wrongItem);
							//item_cell_value.append('<div class="bg-danger m-1 p-1">- [INVALID VALUE] : ' + wrongItem + '</div>');
						});
					}
				} else if (!$(cs).find('option').is(':selected')) {
					$(cs).closest('tr').addClass('bg-warning has-error');
					addErrorRow(cs, currentValue);
					//item_cell_value.append('<div class="bg-danger m-1 p-1">- [INVALID VALUE] : ' + currentValue + '</div>');
				}
			}
		});
	}

	function displayAllRows() {
		$('#terms-table tr').show();
	}

	function displayOnlyErrors() {
		$('#terms-table tr').hide();
		$('#terms-table tr.has-error').show();
	}

	function confirmImport() {
		$('#confirmModal').modal('show')
	}

	function displayResponse(message) {
		$('#confirmModal').modal('hide');

		$('#processModal .modal-body .modal-body-content').html('<p>' + message + '</p>');
		$('#processModal').modal('show');
	}

	function startImport() {

		showLoading();
		displayResponse('Please wait until import complete...');

		let rows = $('#terms-table tbody tr').not('.has-error');

		let _data = [];

		const rx = /\[.+?\]/g // for finding and removing "[something]"

		$(rows).each((_, r) => {
			let rowItem = {
				'terms': []
			};
			$(r).find('input, select').each((__, i) => {
				if (typeof $(i).attr('name') != 'undefined') {
					let input_name = $(i).attr('name');
					let input_value = $(i).val();

					let _name = input_name.replace(rx, "");

					if (_name === 'terms') {
						input_value.forEach((termid) => {
							rowItem['terms'].push(termid);
						});
					} else {
						rowItem[_name] = input_value;
					}
				}
			});
			_data.push(rowItem);
		});

		$.ajax({
			url: '/admin/services/import/process',
			data: JSON.stringify(_data),
			dataType: 'json',
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
				'Content-Type': 'application/json',
			},
			type: 'POST',
			success: function(response) {
				hideLoading();
				displayResponse(response.message);

				if (response.status == 1) {
					$('#import-content').remove();
					$('.btn_startImport').remove();
					setLoadingInfo('Service import completed.', 'success');
				}
			}
		});
	}

	let loadingWrapper = $('#import-loading');

	function setLoadingInfo(text, type) {
		loadingWrapper.find('.loading-text').text(text);

		loadingWrapper[0].className = loadingWrapper[0].className.replace(/\bbg.*?\b/g, '');
		loadingWrapper.addClass('bg-' + type);
	}

	function hideLoadingInfo() {
		setTimeout(() => {
			loadingWrapper.hide();
		}, 3000);
	}

	init_import().then(function(response) {
		set_input_events();
		setLoadingInfo("Analyzing excel file", "warning");
	});

	function set_input_events() {
		let inputs = $('#terms-table').find('input, select');
		inputs.on('change', function() {
			let input = $(this);
			let _cell_value = input.parent().find('.cell_value');
			let _old_value = input.parent().find('.old_value');

			//console.log(_cell_value.text(), _old_value.text());

			if (input.is('select')) {
				let _selectedText = [];
				input.find(':selected').each((_, iv) => {
					_selectedText.push($(iv).text());
				});
				_cell_value.text(_selectedText.join(';'));
			} else {
				_cell_value.text(input.val());
			}

			if (_cell_value.text() != _old_value.text()) {
				_old_value.show();
			} else {
				_old_value.hide();
			}
		});
	}

	function updateRowInfo(rowID) {
		console.log(rowID);
		//init_import();
	}

	function showLoading() {
		$('#loading').addClass('loading');
		$('#loading-content').addClass('loading-content');
	}

	function hideLoading() {
		$('#loading').removeClass('loading');
		$('#loading-content').removeClass('loading-content');
	}

	$(document).ready(function() {
		console.log('ready');
		setLoadingInfo("Service import is ready", "success");
		$('#import-content').show();
	});

	$('.update-row').click(function() {

		let _btn = $(this);
		let activeClassName = 'btn-success';
		let row = _btn.closest('tr');
		let inputs = row.find('.cell-update-input');

		_btn.toggleClass(activeClassName).promise().done(function() {
			if (_btn.hasClass(activeClassName)) {
				inputs.show();
				inputs.each((_, _input) => {
					if ($(_input).hasClass('js-select'))
						$(_input).next().show();
				})

				_btn.text('Done');

			} else {
				inputs.hide();
				inputs.each((_, _input) => {
					if ($(_input).hasClass('js-select'))
						$(_input).next().hide();
				})
				_btn.text('Update');
				$(_btn).on('click', updateRowInfo(row.data('id')));
			}
		});
	});
</script>

@endsection

@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-12">
		<div class="card index-card">
			<div class="card-header">
				<h4 class="card-title my-1 float-left">Services Import</h4>

			</div>
			<div class="card-body px-0 py-0">
				<div class="row">
					<div class="col-12">
						<div class="content">
							<div class="clearfix"></div>

							@include('flash::message')

							<div class="clearfix"></div>
							<div class="box box-primary bg-info" id="import-loading">
								<div class="loading-text tx-center p-3">{{ __('app.Starting Import Process') }}...</div>
							</div>
							<div class="box box-primary" id="import-content" style="display: none;">
								<div class="box-body">

									<div class="card">
										<div class="card-body">
											<div id="process-info-items">
												<span class="service-count bagde badge-info p-1">{{ __('app.Services On File') }} : <span>{{ count($items) }}</span></span>
												<span class="error-count bagde badge-danger p-1" style="display: none;">{{ __('app.Has Invalid Value') }} : <span>0</span></span>
												<span class="success-count bagde badge-success p-1">{{ __('Will Import') }} : <span>0</span></span>
											</div>
											<hr />
											<div id="process-actions" class="d-flex">
												<button type="button" class="btn btn-info" onclick="displayAllRows()">Show All</button>
												<button type="button" class="btn btn-warning ml-1" onclick="displayOnlyErrors()">Only Errors</button>
												<button type="button" class="btn btn-success ml-auto btn_startImport" onclick="confirmImport()">Start Import</button>
											</div>
										</div>
									</div>

									<div class="table-responsive" style="height: 500px;">
										<form id="services-values">
											<table class="table" id="terms-table">
												<thead>
													<tr>
														<th></th>
														<th>#</th>
														@foreach($headers as $header)
														<th>{!! $header !!}</th>
														@endforeach
													</tr>
												</thead>
												<tbody>
													@foreach($items as $rowIndex=>$item)
													<tr data-id="{!! $rowIndex !!}">
														<td>
															<span class="btn btn-secondary update-row">Update</span>
														</td>
														<td>
															{!! $rowIndex + 1 !!}
														</td>
														@php
														$cellIndex = 0
														@endphp

														@foreach($headers as $headerKey=>$header)
														<td class="import_value_cell">
															<div class="old_value" style="display: none; text-decoration: line-through">{!! $item[$cellIndex] !!}</div>
															<div class="cell_value">{!! $item[$cellIndex] !!}</div>
															<br />
															@include('services.import.input', ['name'=>$headerKey, 'value'=>$item[$cellIndex++], 'index' => $rowIndex])
														</td>
														@endforeach
													</tr>
													@endforeach
												</tbody>
											</table>
										</form>
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
</div>


<div id="import-information">
	@include('services.import.information')
</div>

<div class="modal" id="confirmModal" tabindex="-1" role="dialog" aria- labelledby="confirmModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="importModalLabel">Services Import</h5>
				<button type="button" class="close" data-dismiss="modal" aria- label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div id="importModalBody" class="modal-body">
				<p>Do you want to import services</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Abort</button>
				<button type="button" class="btn btn-primary btn_startImport" onclick="startImport()">Start Import</button>
			</div>
		</div>
	</div>
</div>

<div class="modal" id="processModal" tabindex="-1" role="dialog" aria- labelledby="processModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="importModalLabel">Services Import</h5>
				<button type="button" class="close" data-dismiss="modal" aria- label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body text-center">
				<div class="modal-body-content text-center"></div>
				<section id="loading">
					<div id="loading-content"></div>
				</section>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

@endsection