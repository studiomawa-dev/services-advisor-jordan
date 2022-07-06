@section('scripts')
<script>
	var serviceLocation = parseJson('<?= json_encode($service->location, JSON_HEX_APOS) ?>');
	var categoriesJson = parseJson('<?= json_encode($category_terms, JSON_HEX_APOS) ?>');
	var locationsJson = parseJson('<?= json_encode($location_terms, JSON_HEX_APOS) ?>');
	var serviceTerms = [<?= implode(',', $service->termIds); ?>];
	var serviceHours = parseJson('<?= json_encode($service->hours, JSON_HEX_APOS) ?>');
	var serviceContacts = parseJson('<?= json_encode($service->contacts, JSON_HEX_APOS) ?>');
	var contacts = parseJson('<?= json_encode($contacts, JSON_HEX_APOS) ?>');

	if (serviceContacts == null) {
		serviceContacts = [];
	}

	var map, marker;

	$(document).ready(function() {
		$('#start_date').datetimepicker({
			format: 'Y-m-d',
			mask: true,
			timepicker: false,
			useCurrent: false
		});

		$('#end_date').datetimepicker({
			format: 'Y-m-d',
			mask: true,
			timepicker: false,
			useCurrent: false
		});

		$('#publish_date').datetimepicker({
			format: 'Y-m-d',
			mask: true,
			timepicker: false,
			useCurrent: false
		});

		$('#main-accordion').closest("form").submit(function(event) {
			event.preventDefault();

			fillServiceHoursInput();
			if (validateForm()) {
				$(this).unbind('submit').submit();
			} else {
				$([document.documentElement, document.body]).animate({
					scrollTop: $('.error').first().offset().top - 180
				}, 1000);
			}
		})

		$('input[name="backendonly"]').click(function() {
			if ($(this).is(':checked')) {
				$('.service-statuses').slideUp()
			} else {
				$('.service-statuses').slideDown()
			}
		});

		$('#location-edit-btn').click(function() {
			var editUrl = $(this).data('edit-url');
			window.open(editUrl, '_blank')
		});

		initMap();
		initCities();
		initCategories();
		initServiceHourDays();
		initTimeSelects();
		initServiceHours();
	});

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

	function initMap() {
		mapboxgl.accessToken = 'pk.eyJ1IjoibG9tYmllIiwiYSI6IlAyVlJfU3MifQ.gMTKJU_NsIvulLTttw4-XA';
		var mapboxClient = mapboxSdk({
			accessToken: mapboxgl.accessToken
		});

		var coordinates = document.getElementById('coordinates');

		map = new mapboxgl.Map({
			container: 'map',
			style: 'mapbox://styles/mapbox/streets-v11',
			center: [0, 0],
			zoom: 5
		});

		marker = new mapboxgl.Marker({
				draggable: false
			})
			.setLngLat([0, 0])
			.addTo(map);

		$('#map-container').addClass('d-none');
	}


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
		citySelect.append($("<option />").val(null).text('<?= __('Select Province') ?>'));
		districtSelect.prepend($("<option />").val(null).text('<?= __('Select District') ?>'));

		citySelect.on('change', onCitySelect);

		$.each(cities, function() {
			citySelect.append($("<option />").val(this[0]).text(this[2]))
		});

		if (serviceLocation != undefined) {
			setMapCenter(serviceLocation.latitude, serviceLocation.longitude);

			citySelect.val(serviceLocation.city_id);
			onCitySelect();
			districtSelect.val(serviceLocation.district_id);
		}
	}

	function initCategories() {
		var selectContainer = $('#category-select-container');
		var servicesSelect = $('#category-select');

		var categories = [];

		for (var i = 0; i < categoriesJson.length; i++) {
			var category = categoriesJson[i];
			category.children = categoriesJson.filter(item => {
				return item.parent_id == category.id;
			});

			if (category.children.length > 0) {
				for (var j = 0; j < category.children.length; j++) {
					var child = category.children[j];
					child.parent = category;
				}
			}

			categories.push(category);
		}

		var onCategorySelect = function() {
			var selectedCategory = null;
			var selectedVal = parseInt($(this).val());
			var selectIndex = parseInt($(this).data('index'));
			var cats = categories.filter(item => {
				return item.id == selectedVal;
			});

			if (cats.length == 1) selectedCategory = cats[0];
			removeOtherSelects(selectIndex);

			if (selectedCategory != null) {
				fillSelect(selectIndex, selectedCategory);
			}
		}

		var fillSelect = function(index, category) {
			if (category.children.length > 0) {
				var newIndex = index + 1;
				selectContainer.append('<select id="category-select-' + newIndex + '" class="category-select form-control mb-2" data-index="' + (index + 1) + '"></select>');
				var select = $('#category-select-' + newIndex);
				select.on('change', onCategorySelect);
				select.append('<option>None</option>');
				for (var i = 0; i < category.children.length; i++) {
					var item = category.children[i];
					select.append('<option value="' + item.id + '">' + item.name + '</option>');

					if (i == 0 && item.children.length > 0) {
						//fillSelect(newIndex, item);
					}
				}
			}
		}

		var removeOtherSelects = function(index) {
			var selects = $('.category-select');
			if (selects.length > 0) {
				for (var i = 0; i < selects.length; i++) {
					var select = selects[i];
					var selectIndex = parseInt($(select).data('index'));
					if (selectIndex > index) {
						$(select).remove();
					}
				}
			}
		}

		var removeCategory = function() {
			var parent = $(this).parents('.selected-category-item').first();
			removeContact(parent.data('id'));
			parent.remove();
		}

		var addCategory = function() {
			var selectedCategoryId;
			var maxIndex = 0;

			var selects = $('.category-select');
			if (selects.length > 0) {
				for (var i = 0; i < selects.length; i++) {
					var select = selects[i];
					var selectIndex = parseInt($(select).data('index'));

					if (selectIndex > maxIndex) {
						selectedCategoryId = parseInt($(select).val());
						maxIndex = selectIndex;
					}
				}
			}

			var selectedCategoryIds = [];
			var selectedCategories = $('#selected-category-container .selected-category-item');
			if (selectedCategories.length > 0) {
				for (var i = 0; i < selectedCategories.length; i++) {
					var selectedCategory = selectedCategories[i];
					var itemId = parseInt($(selectedCategory).data('id'));
					selectedCategoryIds.push(itemId);
				}

			}

			if (selectedCategoryId > 0 && selectedCategoryIds.indexOf(selectedCategoryId) == -1) {
				addCategoryById(selectedCategoryId);
			}
		}

		var addCategoryById = function(selectedCategoryId) {
			var selectedCategories = categoriesJson.filter(x => x.id == selectedCategoryId);
			var selectedCategory = (selectedCategories.length > 0) ? selectedCategories[0] : null;
			var categoriesList = [];
			var categoryNames = [];

			if (selectedCategory) {
				var currentCategory = selectedCategory;
				categoriesList.unshift(currentCategory);

				while (currentCategory.parent != null) {
					currentCategory = currentCategory.parent;
					categoriesList.unshift(currentCategory);
				}
			}

			if (categoriesList.length > 0) {
				for (var i = 0; i < categoriesList.length; i++) {
					var category = categoriesList[i];
					var input = '<input type="hidden" name="terms[]" value="' + category.id + '" />';
					categoryNames.push('<span class="category-item" data-category-id="' + category.id + '">' + category.name + '</span>' + input);
				}

				var selectedCategoryNamesStr = '<div class="selected-category-item-inner">' + categoryNames.join('<span class="category-item-separator">›</span>');
				var selectedCategoryContent = '<div class="selected-category-item" id="selected-category-item-' + selectedCategoryId +
					'" data-id="' + selectedCategoryId + '">' + selectedCategoryNamesStr + '</div>' +
					'<i class="remove mdi mdi-close-circle-outline"></i><div class="clearfix"></div>';
				$('#selected-category-container').append(selectedCategoryContent);
				$('#selected-category-item-' + selectedCategoryId + ' .remove').click(removeCategory);

				addContact(selectedCategoryId, categoriesList.map(x => x.name).join(' › '));
			}
		}

		var rootCategories = categories.filter(item => {
			return item.parent_id == 0;
		});

		servicesSelect.empty();
		servicesSelect.append($("<option />").val(null).text('<?= __('app.Select Category') ?>'))

		servicesSelect.on('change', onCategorySelect);

		$.each(rootCategories, function() {
			servicesSelect.append($("<option />").val(this.id).text(this.name))
		});

		$('#add-category-btn').click(addCategory);

		serviceTerms.forEach(item => {
			var itemCats = categoriesJson.filter(x => x.id == item);

			if (itemCats.length > 0 && (itemCats[0].children == undefined || itemCats[0].children.length == 0)) {
				addCategoryById(item)
			}
		});
	}

	function addContact(categoryId, categoryText) {
		var contactsContainer = $('#contacts-container');
		contactsContainer.hide();

		var contactsWarning = $('#no-category-warning');
		contactsWarning.hide();

		var contactsItemsContainer = $('#contact-items-container');

		var getContactItem = function(selectedCategoryId, selectedCategoryText, selectedContactId) {
			var options = ['<option value="">Select an option</option>'];
			for (var i = 0; i < contacts.length; i++) {
				var contact = contacts[i];
				options.push('<option data-contact="' + JSON.stringify(contact).split('"').join('||') + '" value="' + contact.id + '" ' + ((selectedContactId != null && selectedContactId == contact.id) ? 'selected' : '') + '>' + contact.name + '</option>')
			}
			return `<div class="row service-contact-item" id="service-contact-item-` + selectedCategoryId + `">
                        <div class="col-md-8 col-sm-12">
                            <div class="table-check form-check form-check-flat mt-0">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" id="select-item-` + selectedCategoryId + `" value="` + selectedCategoryId + `" />
                                    ` + selectedCategoryText + `
                                    <i class="input-helper"></i>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <select name="service_contact_` + selectedCategoryId + `" id="contact-input-` + selectedCategoryId + `" class="form-control contact-select">
                                ` + options.join('') + `
                            </select>
                            <div class="contact-clear-btn" style="position: absolute;right: 36px;top: -4px;cursor: pointer;"><i class="mdi mdi-close-outline"></i></div>
                            <div class="contact-info-btn" style="position: absolute;right: 6px;top: -4px;cursor: pointer;"><i class="mdi mdi-comment-account-outline"></i></div>
                        </div>
                    </div>`;
		}

		var serviceContact = serviceContacts.filter(x => x.category_id == categoryId)[0];
		if (serviceContact == null) {
			var serviceContact = {
				'category_id': categoryId,
				'contact_id': null
			};
			serviceContacts.push(serviceContact);
		}
		contactsItemsContainer.append(getContactItem(serviceContact.category_id, categoryText, serviceContact.contact_id));

		if (serviceContacts.length > 0) {
			contactsContainer.show();
		} else {
			contactsWarning.show();
		}

		$('.contact-select').select2({
			placeholder: "<?= __('app.Select an option') ?>",
			allowClear: true
		});

		$('.contact-select').on("change", function(e) {
			updateContactButtons();
		});

		updateContactInfos();
		updateContactButtons();
		checkReferrals();
	}

	function updateContactInfos() {

		$('.contact-select').parent().find('.contact-info-btn').popover({
			placement: 'bottom',
			trigger: 'click',
			html: true,
			content: function() {
				var contactData = $(this).parent().find('.contact-select').children("option:selected").data('contact');
				if (contactData != undefined && contactData.length > 0) {
					var contactJson = contactData.split('||').join('"');
					var contact = JSON.parse(contactJson);

					if (contact != undefined && contact.id != undefined && contact.id > 0) {
						return `
                            <div class="contact-popover-inner">
                            ` +
							(contact.name != undefined ? '<div><strong>' + contact.name + '</strong></div>' : '') +
							(contact.email != undefined ? '<div><i class="mdi mdi-email-outline" style="width: 20px;display: inline-block;"></i>' + contact.email + '</div>' : '') +
							(contact.phone != undefined ? '<div><i class="mdi mdi-phone-outline" style="width: 20px;display: inline-block;"></i>' + contact.phone + '</div>' : '') +
							`
                            </div>
                        `;
					}
				}

				return "";
			}
		})

		$('html').on('click', function(e) {
			if (!($(e.target).parents().is('[aria-describedby^="popover"]') || $(e.target).parents().is('.popover.show'))) {
				$('[data-original-title]').popover('hide');
			}
		});

		$('.contact-clear-btn').click(function() {
			$(this).parent().find('.contact-select').val(null).trigger('change');
			updateContactButtons();
		})
	}

	function updateContactButtons() {
		$('.contact-select').each(function(i, e) {
			var selectedContact = $(e).children("option:selected").val();
			if (selectedContact == undefined || selectedContact == "") {
				$(this).parent().find('.contact-info-btn').hide();
				$(this).parent().find('.contact-clear-btn').hide();
			} else {
				$(this).parent().find('.contact-info-btn').show();
				$(this).parent().find('.contact-clear-btn').show();
			}
		});

	}

	function removeContact(categoryId) {
		$('#service-contact-item-' + categoryId).remove();
		updateContactInfos();
	}

	function assignContacts() {
		var selectedContactId = $('#service-contact-select').val();
		if (!(selectedContactId > 0)) return;

		var checkedItems = $('.service-contact-item input.form-check-input:checked');
		console.log(checkedItems.length);
		checkedItems.each(function(idx, elm) {
			var contactSelect = $(elm).closest('.service-contact-item').find('select');
			contactSelect.val(selectedContactId).trigger('change');
		})
		updateContactInfos();
	}

	function selectAllContactRows() {
		$('.service-contact-item input.form-check-input').prop('checked', $('#select-all-check').is(':checked'));
	}

	function checkReferrals() {
		var canHideContacts = false;
		var notAcceptedTermIds = [1561, 1562];
		var checkboxes = $('input[name="terms[]"]');
		checkboxes.each(function(i, e) {
			var val = parseInt($(e).val());
			var checked = $(e).is(':checked');
			if (notAcceptedTermIds.indexOf(val) > -1 && checked) {
				canHideContacts = true;
			}
		});

		if (canHideContacts) {
			$('.contacts-title, #contacts-container').hide();
			$('#contact-items-container .contact-select').val(null).trigger('change');
			updateContactButtons();
		} else {
			$('.contacts-title, #contacts-container').show();
		}
	}

	function fillServiceHoursInput() {
		var dayContainers = $('.day-container');
		var serviceHours = [];

		for (var i = 0; i < dayContainers.length; i++) {
			var dayContainer = dayContainers[i];
			var isChecked = $(dayContainer).find('.day-switch').prop('checked');

			if (isChecked) {
				var primaryHourParts = {
					day: $(dayContainer).data('day'),
					start_hour: 0,
					end_hour: 0
				};

				var primaryStartHourCombinedElm = $(dayContainer).find('.primary-time .combined-select').first();
				var primaryStartHourCombinedElm1 = $(primaryStartHourCombinedElm).find('.time-select').first();
				primaryHourParts.start_hour = parseInt(primaryStartHourCombinedElm1.val()) * 100;

				var primaryStartHourCombinedElm2 = $(primaryStartHourCombinedElm).find('.time-select').last();
				primaryHourParts.start_hour += parseInt(primaryStartHourCombinedElm2.val());

				var primaryEndHourCombinedElm = $(dayContainer).find('.primary-time .combined-select').last();
				var primaryEndHourCombinedElm1 = $(primaryEndHourCombinedElm).find('.time-select').first();
				primaryHourParts.end_hour = parseInt(primaryEndHourCombinedElm1.val()) * 100;

				var primaryEndHourCombinedElm2 = $(primaryEndHourCombinedElm).find('.time-select').last();
				primaryHourParts.end_hour += parseInt(primaryEndHourCombinedElm2.val());

				serviceHours.push(primaryHourParts);

				var hasAdditional = $(dayContainer).find('.add-btn').hasClass('d-none') === true;

				if (hasAdditional) {

					var additionalHourParts = {
						day: $(dayContainer).data('day'),
						start_hour: 0,
						end_hour: 0
					};
					var additionalStartHour = additionalHourParts.start_hour;
					var additionalEndHour = additionalHourParts.end_hour;

					var additionalStartHourCombinedElm = $(dayContainer).find('.additional-time .combined-select').first();
					var additionalStartHourCombinedElm1 = $(additionalStartHourCombinedElm).find('.time-select').first();
					additionalHourParts.start_hour = parseInt(additionalStartHourCombinedElm1.val()) * 100;

					var additionalStartHourCombinedElm2 = $(additionalStartHourCombinedElm).find('.time-select').last();
					additionalHourParts.start_hour += parseInt(additionalStartHourCombinedElm2.val());

					var additionalEndHourCombinedElm = $(dayContainer).find('.additional-time .combined-select').last();
					var additionalEndHourCombinedElm1 = $(additionalEndHourCombinedElm).find('.time-select').first();
					additionalHourParts.end_hour = parseInt(additionalEndHourCombinedElm1.val()) * 100;

					var additionalEndHourCombinedElm2 = $(additionalEndHourCombinedElm).find('.time-select').last();
					additionalHourParts.end_hour += parseInt(additionalEndHourCombinedElm2.val());

					serviceHours.push(additionalHourParts);
				}
			}
		}

		$('#service_hours').val(JSON.stringify(serviceHours));
	}

	function initServiceHours() {
		if (serviceHours != undefined && serviceHours.length > 0) {

			var serviceDays = {};

			for (var i = 0; i < serviceHours.length; i++) {
				var serviceHour = serviceHours[i];
				var day = serviceHour.day.toString();
				if (serviceDays[day] == undefined) {
					serviceDays[day] = [];
				}

				serviceDays[day].push(serviceHour);

				serviceDays[day].sort((a, b) => (a.start_hour > b.start_hour) ? 1 : ((b.start_hour > a.start_hour) ? -1 : 0));
			}

			var f0 = function(v) {
				return (v < 10 ? '0' : '') + v;
			}

			for (var day in serviceDays) {
				if (serviceDays.hasOwnProperty(day)) {
					var dayContainer = $('.day-container[data-day="' + day + '"]');
					if (dayContainer.length > 0) dayContainer = dayContainer[0];

					var dayItems = serviceDays[day];
					if (dayItems != undefined && dayContainer != undefined) {

						$(dayContainer).find('.day-switch').prop('checked', true).change();

						if (dayItems.length > 0) {
							var primaryHourParts = dayItems[0];
							var primaryStartHour = primaryHourParts.start_hour;
							var primaryEndHour = primaryHourParts.end_hour;

							var primaryStartHourCombinedElm = $(dayContainer).find('.primary-time .combined-select').first();
							var primaryStartHourCombinedElm1 = $(primaryStartHourCombinedElm).find('.time-select').first();
							primaryStartHourCombinedElm1.val(f0(Math.floor((primaryStartHour / 100))));

							var primaryStartHourCombinedElm2 = $(primaryStartHourCombinedElm).find('.time-select').last();
							primaryStartHourCombinedElm2.val(f0(primaryStartHour % 100));

							var primaryEndHourCombinedElm = $(dayContainer).find('.primary-time .combined-select').last();
							var primaryEndHourCombinedElm1 = $(primaryEndHourCombinedElm).find('.time-select').first();
							primaryEndHourCombinedElm1.val(f0(Math.floor((primaryEndHour / 100))));

							var primaryEndHourCombinedElm2 = $(primaryEndHourCombinedElm).find('.time-select').last();
							primaryEndHourCombinedElm2.val(f0(primaryEndHour % 100));
						}

						if (dayItems.length == 2) {

							$(dayContainer).find('.add-btn button').click();

							var additionalHourParts = dayItems[1];
							var additionalStartHour = additionalHourParts.start_hour;
							var additionalEndHour = additionalHourParts.end_hour;

							var additionalStartHourCombinedElm = $(dayContainer).find('.additional-time .combined-select').first();
							var additionalStartHourCombinedElm1 = $(additionalStartHourCombinedElm).find('.time-select').first();
							additionalStartHourCombinedElm1.val(f0(Math.floor((additionalStartHour / 100))));

							var additionalStartHourCombinedElm2 = $(additionalStartHourCombinedElm).find('.time-select').last();
							additionalStartHourCombinedElm2.val(f0(additionalStartHour % 100));

							var additionalEndHourCombinedElm = $(dayContainer).find('.additional-time .combined-select').last();
							var additionalEndHourCombinedElm1 = $(additionalEndHourCombinedElm).find('.time-select').first();
							additionalEndHourCombinedElm1.val(f0(Math.floor((additionalEndHour / 100))));

							var additionalEndHourCombinedElm2 = $(additionalEndHourCombinedElm).find('.time-select').last();
							additionalEndHourCombinedElm2.val(f0(additionalEndHour % 100));
						}
					}
				}
			}
		}
	}

	function initTimeSelects() {
		var hourSelects = $('.service-hours .hour');
		var minuteSelects = $('.service-hours .minute');

		if (hourSelects.length > 0) {
			for (var i = 0; i < hourSelects.length; i++) {
				var hourSelect = $(hourSelects[i]);

				for (var j = 0; j < 24; j++) {
					var hour = (j < 10 ? '0' : '') + j;
					hourSelect.append('<option value="' + hour + '">' + hour + '</option>')
				}
			}
		}

		if (minuteSelects.length > 0) {
			for (var i = 0; i < minuteSelects.length; i++) {
				var minuteSelect = $(minuteSelects[i]);

				for (var j = 0; j < 60; j++) {
					var minute = (j < 10 ? '0' : '') + j;
					minuteSelect.append('<option value="' + minute + '">' + minute + '</option>')
				}
			}
		}
	}

	function initServiceHourDays() {
		var days = [
			'<?= __('app.Sunday') ?>',
			'<?= __('app.Monday') ?>',
			'<?= __('app.Tuesday') ?>',
			'<?= __('app.Wednesday') ?>',
			'<?= __('app.Thursday') ?>',
			'<?= __('app.Friday') ?>',
			'<?= __('app.Saturday') ?>'
		];
		var template = `
            <div class="row day-container mb-sm-3" data-day="|dayIndex|">
                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-12">
                    <div class="day-check">
                        <input class="day-switch" type="checkbox" data-toggle="switchButton" data-size="xs">
                        |Day|
                    </div>
                </div>
                <div class="col-xl-10 col-lg-9 col-md-8 col-sm-12">
                    <div class="row">
                        <div class="col-xl-5 col-lg-6 col-md-12 mb-sm-2">
                            <div class="row">
                                <div class="primary-time col-md-12">
                                    <div class="combined-select">
                                        <select class="time-select form-control hour">
                                        </select>
                                        <span>:</span>
                                        <select class="time-select form-control minute">
                                        </select>
                                    </div>
                                    <div class="service-hours-block"> - </div>
                                    <div class="combined-select">
                                        <select class="time-select form-control hour">
                                        </select>
                                        <span>:</span>
                                        <select class="time-select form-control minute">
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-7 col-lg-6 col-md-12">
                            <div class="row">
                                <div class="col-md-12 add-btn">
                                    <button type="button" class="btn btn-icons btn-inverse-light p-0">
                                        <i class="mdi mdi-plus"></i>
                                    </button>
                                </div>
                                <div class="additional-time col-md-12 d-none">
                                    <div class="combined-select">
                                        <select class="time-select form-control hour">
                                        </select>
                                        <span>:</span>
                                        <select class="time-select form-control minute">
                                        </select>
                                    </div>
                                    <div class="service-hours-block"> - </div>
                                    <div class="combined-select">
                                        <select class="time-select form-control hour">
                                        </select>
                                        <span>:</span>
                                        <select class="time-select form-control minute">
                                        </select>
                                    </div>
                                    <button type="button" class="remove-btn btn btn-icons btn-inverse-light p-0">
                                        <i class="mdi mdi-close"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;

		var container = $('#hours-container');
		for (var i = 0; i < days.length; i++) {
			var day = days[i];
			var content = template.replace('|Day|', day).replace('|dayIndex|', i);
			container.append(content);
		}

		$('.day-switch').bootstrapToggle();

		$('.day-switch').change(function() {
			var isChecked = $(this).prop('checked');
			var dayContainer = $(this).closest('.day-container');
			var dayIndex = parseInt(dayContainer.data('day'));

			dayContainer.find('select,button').prop("disabled", !isChecked);
		})

		$('.day-switch').prop('checked', false).change();

		$('#hours-container .add-btn .btn').click(function() {
			$(this).closest('.add-btn').addClass('d-none').parent().find('.additional-time').removeClass('d-none');
		});

		$('#hours-container .remove-btn').click(function() {
			$(this).closest('.additional-time').addClass('d-none').parent().find('.add-btn').removeClass('d-none');
		});
	}

	function validateForm() {
		var hasError = false;
		$(".error").remove();

		var startDate = $('input[name=start_date]').val();
		if (!isValidDate(startDate)) {
			hasError = true;
			$('input[name=start_date]').after('<span class="error"><?= __('app.This field is required') ?></span>');
		}

		var endDate = $('input[name=end_date]').val();
		if (!isValidDate(endDate)) {
			hasError = true;
			$('input[name=end_date]').after('<span class="error"><?= __('app.This field is required') ?></span>');
		}

		var startDateTime = new Date(startDate).getTime();
		var endDateTime = new Date(endDate).getTime();

		if (endDateTime < startDateTime) {
			hasError = true;
			$('input[name=start_date]').after('<span class="error"><?= __('app.Start date should never be after end date') ?></span>');
		}

		var categories = $('#selected-category-container .selected-category-item');
		if (categories.length == 0) {
			hasError = true;
			$('#selected-category-container').after('<span class="error"><?= __('app.This field is required') ?></span>');
		}

		var locationId = $('input[name=location_id]').val();
		if (isNaN(parseInt(locationId))) {
			hasError = true;
			$('.service-locations').after('<span class="error"><?= __('app.This field is required') ?></span>');
		}

		return !hasError;
	}

	function isValidDate(value) {
		if (value != undefined && value.length > 0) {
			var parts = value.split('-');
			if (parts.length == 3 && parseInt(parts[0]) > 0 && parseInt(parts[1]) > 0 && parseInt(parts[1]) > 0) {
				return true;
			}
		}

		return false;
	}

	window.closeLocationModal = function() {
		$('#locationModal').modal('hide');
	};

	window.selectLocation = function(id) {
		$('#location_id').val(id);
		$('#locationModal').modal('hide');

		$.getJSON("{{url('/admin/services/locations/item')}}/" + id, function(data) {
			var citySelect = $('#city-select');
			var districtSelect = $('#district-select');

			citySelect.val(data.city_id).trigger('change');
			districtSelect.val(data.district_id).trigger('change');

			if (data != undefined && data.latitude != undefined && data.longitude != undefined) {
				setMapCenter(data.latitude, data.longitude);
			}

			$('#location-edit-btn').removeAttr('disabled');
			var editUrl = $('#location-edit-btn').data('edit-base-url');
			editUrl = editUrl.replace('##', id);
			$('#location-edit-btn').data('edit-url', editUrl);
		})



	};

	function openLocationModal() {
		var citySelect = $('#city-select');
		var districtSelect = $('#district-select');
		var url = $('#locationFrame').data('src');
		url += '?city=' + citySelect.val();
		url += '&district=' + districtSelect.val();
		$('#locationFrame').attr('src', url);
		$('#locationModal').modal('show');
	}

	function setMapCenter(lat, lng) {
		if (isNaN(lat) || isNaN(lng)) return;

		var lngLat = [lng, lat];
		if (map != undefined) {
			map.setCenter(lngLat);
			map.setZoom(15);
			marker.setLngLat(lngLat);
		}

		$('#map-container').removeClass('d-none');
	}
</script>
@endsection

<div class="accordion accordion-multiple-filled" id="main-accordion" role="tablist">

	<div class="card horizontal-form">
		<div class="card-header" role="tab" id="heading-1">
			<h5 class="mb-0">
				<a data-toggle="collapse" href="#collapse-1" aria-expanded="true" aria-controls="collapse-1">
					{{ __('app.Service Active Dates') }}
				</a>
			</h5>
		</div>
		<div id="collapse-1" class="collapse show" role="tabpanel" aria-labelledby="heading-1">
			<div class="card-body">
				<div class="row">

					<div class="col-md-6">
						<div class="form-group row mb-0">
							{!! Form::label('start_date', __('app.Start Date:'), ['class' => 'col-sm-3 col-form-label']) !!}
							<div class="col-sm-9">
								{!! Form::text('start_date', null, ['class' => 'form-control','id'=>'start_date', 'autocomplete' => 'off']) !!}
								<p class="hint">{{ __('app.Date this service becomes active') }}.</p>
							</div>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group row mb-0">
							{!! Form::label('end_date', __('app.End Date:'), ['class' => 'col-sm-3 col-form-label']) !!}
							<div class="col-sm-9">
								{!! Form::text('end_date', null, ['class' => 'form-control','id'=>'end_date', 'autocomplete' => 'off']) !!}
								<p class="hint">{{ __('app.Date this service ends') }}.</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="card horizontal-form">
		<div class="card-header" role="tab" id="heading-2">
			<h5 class="mb-0">
				<a data-toggle="collapse" href="#collapse-2" aria-expanded="true" aria-controls="collapse-2">
					{{ __('app.General Information') }}
				</a>
			</h5>
		</div>
		<div id="collapse-2" class="collapse show" role="tabpanel" aria-labelledby="heading-2">
			<div class="card-body">
				<div class="row">
					<div class="col-lg-6 col-md-12">
						<div class="form-group row">
							{!! Form::label('partner_id', __('app.Organization:'), ['class' => 'col-sm-3 col-form-label']) !!}
							<div class="col-sm-9">
								{!! Form::select('partner_id', $partners, null, ['class' => 'form-control js-select', 'placeholder' => '']) !!}
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-md-12 d-none">
						<div class="form-group row">
							<p class="hint">{{ __('app.Associate this content with a group') }}.</p>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">

						<h6 class="card-title">{{ __('app.Categories') }}</h6>
						<div class="row">
							<div class="col-md-12 mb-2">
								<select id="category-select" class="form-control js-select" data-index="0"></select>
							</div>
							<div id="category-select-container" class="col-md-12"></div>
							<div class="col-md-12">
								<button id="add-category-btn" type="button" class="btn btn-primary">{{ __('app.Add') }}</button>
							</div>

							<div id="selected-category-container" class="col-md-12 mt-4"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="card horizontal-form">
		<div class="card-header" role="tab" id="heading-3">
			<h5 class="mb-0">
				<a data-toggle="collapse" href="#collapse-3" aria-expanded="true" aria-controls="collapse-3">
					{{ __('app.Availability') }}
				</a>
			</h5>
		</div>
		<div id="collapse-3" class="collapse show" role="tabpanel" aria-labelledby="heading-3">
			<div class="card-body">
				<h4 class="card-title">{{ __('app.Hours') }}</h4>

				<div class="row service-hours">

					<div id="hours-container" class="col-md-12">

					</div>

				</div>

				<div class="service-card-wrapper">

					<h4 class="card-title mt-3">{{ __('app.Accessibility') }}</h4>

					<div class="row">
						@foreach($accessibility_terms as $accessibility_term)
						<div class="col-xl-4 col-md-6 col-sm-12">
							<div class="form-check form-check-flat mt-0">
								<label class="form-check-label">
									<input type="checkbox" class="form-check-input" name="terms[]" value="{{ $accessibility_term->id }}" <?php if (in_array($accessibility_term->id, $service->termIds)) echo 'checked="checked"'; ?>>
									<?php if (count($accessibility_term->langs) == 0) ?>

									{{ count($accessibility_term->langs) == 0 ? "-" : $accessibility_term->langs[0]->name }}
								</label>
							</div>
						</div>
						@endforeach
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="card horizontal-form">
		<div class="card-header" role="tab" id="heading-4">
			<h5 class="mb-0">
				<a data-toggle="collapse" href="#collapse-4" aria-expanded="true" aria-controls="collapse-4">
					{{ __('app.Location') }}
				</a>
			</h5>
		</div>
		<div id="collapse-4" class="collapse show" role="tabpanel" aria-labelledby="heading-4">
			<div class="card-body">
				<h4 class="card-title">{{ __('app.Service Location') }}</h4>

				<div class="row service-locations">

					<div class="col-lg-4 col-md-4 mb-md-3">
						<select id="city-select" name="location_city_id" class="form-control js-select" data-placeholder="{{ __('app.Select Province') }}"></select>
					</div>

					<div class="col-lg-4 col-md-4">
						<select id="district-select" name="location_district_id" class="form-control js-select" data-placeholder="{{ __('app.Select District') }}"></select>
					</div>

					<div class="col-lg-4 col-md-4">
						<div class="row">

							<div class="col-md-6">
								{!! Form::hidden('location_id', null, ['id'=>'location_id', 'class' => 'form-control', 'readonly'=>'readonly', 'style'=>'line-height: 16px;']) !!}
								<button type="button" class="btn btn-outline-primary btn-block" onclick="openLocationModal()">{{ __('app.Select Location') }}</button>
							</div>
							<div class="col-md-6">
								<button id="location-edit-btn" {{ isset($service) && isset($service->location_id) && $service->location_id > 0 ? '': 'disabled' }} type=" button" class="btn btn-outline-primary btn-block" data-edit-base-url="{{ route('services.locations.edit', '##') }}" data-edit-url="{{ route('services.locations.edit', isset($service->location_id)?$service->location_id:0) }}">Edit Location</button>
							</div>
						</div>
					</div>


				</div>

				<div id="map-container">
					<h4 class="card-title mt-3">{{ __('app.GPS coordinates') }}</h4>

					<div class="row">
						<div class="col-md-12">

							<div style="height: 400px">
								<div id='map' style="height: 400px"></div>
								<pre id='coordinates' class='coordinates' style="background: rgba(0,0,0,0.5);color: #fff;position: absolute;bottom: 40px;left: 18px;padding:5px 10px;margin: 0;font-size: 11px;line-height: 18px;border-radius: 3px;display: none;"></pre>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="card horizontal-form">
		<div class="card-header" role="tab" id="heading-5">
			<h5 class="mb-0">
				<a data-toggle="collapse" href="#collapse-5" aria-expanded="true" aria-controls="collapse-5">
					{{ __('app.Documentations and Specific Needs') }}
				</a>
			</h5>
		</div>
		<div id="collapse-5" class="collapse show" role="tabpanel" aria-labelledby="heading-5">
			<div class="card-body">

				<div class="service-card-wrapper">

					<h4 class="card-title mt-3">{{ __('app.Legal Documents Required') }}</h4>

					<div class="row legal-documents-items">
						@foreach($registration_type_terms as $registration_type_term)
						<div class="col-md-6 col-sm-12">
							<div class="form-check form-check-flat mt-0">
								<label class="form-check-label">
									<input type="checkbox" class="form-check-input" name="terms[]" value="{{ $registration_type_term->id }}" <?php if (in_array($registration_type_term->id, $service->termIds)) echo 'checked="checked"'; ?>>
									{{ $registration_type_term->langs[0]->name }}
								</label>
							</div>
						</div>
						@endforeach
					</div>
				</div>

				<div class="service-card-wrapper">

					<h4 class="card-title mt-3">{{ __('app.Nationality') }}</h4>
					<div class="row">
						@foreach($available_nationality_terms as $available_nationality_term)
						<div class="col-xl-4 col-md-6 col-sm-12">
							<div class="form-check form-check-flat mt-0">
								<label class="form-check-label">
									<input type="checkbox" class="form-check-input" name="terms[]" value="{{ $available_nationality_term->id }}" <?php if (in_array($available_nationality_term->id, $service->termIds)) echo 'checked="checked"'; ?>>
									{{ $available_nationality_term->langs[0]->name }}
								</label>
							</div>
						</div>
						@endforeach
					</div>
				</div>


				<?php
				$genderAndAge = [];
				$otherIntakeCriteria = [];
				foreach ($intake_criteria_terms as $intake_criteria_term) {
					if (
						strpos(strtolower($intake_criteria_term->langs[0]->name), 'only') !== false ||
						strpos(strtolower($intake_criteria_term->langs[0]->name), 'sadece') !== false
					) {
						$genderAndAge[] = $intake_criteria_term;
					} else {
						$otherIntakeCriteria[] = $intake_criteria_term;
					}
				}
				?>

				<?php if (!empty($genderAndAge)) : ?>
					<div class="service-card-wrapper">
						<h4 class="card-title mt-3">{{ __('app.Gender & Age') }}</h4>
						<div class="row">
							@foreach($genderAndAge as $intake_criteria_term)

							<div class="col-xl-4 col-md-6 col-sm-12">
								<div class="form-check form-check-flat mt-0">
									<label class="form-check-label">
										<input type="checkbox" class="form-check-input" name="terms[]" value="{{ $intake_criteria_term->id }}" <?php if (in_array($intake_criteria_term->id, $service->termIds)) echo 'checked="checked"'; ?>>
										{{ trim(ucwords(str_replace(array("only", "sadece", "Sadece", "Only"),'',$intake_criteria_term->langs[0]->name))) }}
									</label>

								</div>
							</div>
							@endforeach
						</div>
					</div>
				<?php endif; ?>

				<div class="service-card-wrapper">

					<h4 class="card-title mt-3">{{ __('app.Intake Criteria') }}</h4>
					<div class="row">
						@foreach($otherIntakeCriteria as $intake_criteria_term)
						<div class="col-xl-4 col-md-6 col-sm-12">
							<div class="form-check form-check-flat mt-0">
								<label class="form-check-label">
									<input type="checkbox" class="form-check-input" name="terms[]" value="{{ $intake_criteria_term->id }}" <?php if (in_array($intake_criteria_term->id, $service->termIds)) echo 'checked="checked"'; ?>>
									{{ $intake_criteria_term->langs[0]->name }}
								</label>
							</div>
						</div>
						@endforeach
					</div>
				</div>



				<div class="service-card-wrapper">

					<h4 class="card-title mt-3">{{ __('app.Coverage') }}</h4>
					<div class="row">
						@foreach($coverage_terms as $coverage_term)
						<div class="col-xl-4 col-md-6 col-sm-12">
							<div class="form-check form-check-flat mt-0">
								<label class="form-check-label">
									<input type="checkbox" class="form-check-input" name="terms[]" value="{{ $coverage_term->id }}" <?php if (in_array($coverage_term->id, $service->termIds)) echo 'checked="checked"'; ?>>
									{{ $coverage_term->langs[0]->name }}
								</label>
							</div>
						</div>
						@endforeach
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="card horizontal-form">
		<div class="card-header" role="tab" id="heading-6">
			<h5 class="mb-0">
				<a data-toggle="collapse" href="#collapse-6" aria-expanded="true" aria-controls="collapse-6">
					{{ __('app.Referral and Feedback') }}
				</a>
			</h5>
		</div>
		<div id="collapse-6" class="collapse show" role="tabpanel" aria-labelledby="heading-6">
			<div class="card-body">

				<div class="service-card-wrapper">

					<h4 class="card-title mt-3">{{ __('app.Referral Method') }}</h4>

					<div class="row">
						@foreach($referral_method_terms as $referral_method_term)
						<div class="col-xl-3 col-md-6 col-sm-12">
							<div class="form-check form-check-flat mt-0">
								<label class="form-check-label">
									<input type="checkbox" class="form-check-input" name="terms[]" onclick="checkReferrals()" value="{{ $referral_method_term->id }}" <?php if (in_array($referral_method_term->id, $service->termIds)) echo 'checked="checked"'; ?>>
									{{ $referral_method_term->langs[0]->name }}
								</label>
							</div>
						</div>
						@endforeach
					</div>

				</div>

				<h4 class="contacts-title card-title mt-3">{{ __('app.Contacts') }}</h4>
				<div class="row mt-2">
					<div id="contacts-container" class="col-12">
						<div class="row contacts-inner">
							<div class="col-12">
								<div class="row head">
									<div class="col-8">
										<div class="table-check form-check form-check-flat mt-0 mb-0">
											<label class="form-check-label">
												<input type="checkbox" class="form-check-input" id="select-all-check" onclick="selectAllContactRows()" />
												{{ __('app.Category') }}
												<i class="input-helper"></i>
											</label>
										</div>
									</div>
									<div class="col-4">{{ __('app.Contact') }}</div>
								</div>
								<div id="contact-items-container">

								</div>

								<div class="row mt-2">
									<div class="col-md-4">
										<div class="form-group mb-2">
											<select id="service-contact-select" class="form-control js-select">
												<option value="">{{ __('app.Select Contact') }}</option>
												@foreach($contacts as $contact)
												<option value="{{ $contact->id }}">{{ $contact->name }}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="col-md-2">
										<button class="btn btn-primary" type="button" onclick="assignContacts()">{{ __('app.Assign To Selection') }}</button>
									</div>
								</div>

								<div>


								</div>
							</div>
						</div>
					</div>

					<div id="no-category-warning" class="col-md-12">
						<div class="form-group">
							<p class="hint">{{ __('app.To assign a contact, first set a service category') }}.</p>
						</div>
					</div>
				</div>

				<div class="service-card-wrapper">

					<h4 class="card-title mt-3">{{ __('app.Immediate Next Step After Referral') }}</h4>
					<div class="row">
						@foreach($referral_next_step_terms as $referral_next_step_term)
						<div class="col-xl-4 col-md-6 col-sm-12">
							<div class="form-check form-check-flat mt-0">
								<label class="form-check-label">
									<input type="checkbox" class="form-check-input" name="terms[]" value="{{ $referral_next_step_term->id }}" <?php if (in_array($referral_next_step_term->id, $service->termIds)) echo 'checked="checked"'; ?>>
									{{ $referral_next_step_term->langs[0]->name }}
								</label>
							</div>
						</div>
						@endforeach
					</div>

				</div>

				<div class="service-card-wrapper">

					<h4 class="card-title mt-3">{{ __('app.Response Delay after Referral') }}</h4>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<select class="form-control" name="terms[]">
									<option>{{ __('app.None') }}</option>
									@foreach($response_delay_terms as $response_delay_term)
									<option value="{{ $response_delay_term->id }}" <?php if (in_array($response_delay_term->id, $service->termIds)) echo 'selected="selected"'; ?>>{{ $response_delay_term->langs[0]->name }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>

				</div>

				<div class="service-card-wrapper">

					<h4 class="card-title mt-3">{{ __('app.Feedback Mechanism') }}</h4>
					<div class="row">
						@foreach($feedback_mechanism_terms as $feedback_mechanism_term)
						<div class="col-xl-4 col-md-6 col-sm-12">
							<div class="form-check form-check-flat mt-0">
								<label class="form-check-label">
									<input type="checkbox" class="form-check-input" name="terms[]" value="{{ $feedback_mechanism_term->id }}" <?php if (in_array($feedback_mechanism_term->id, $service->termIds)) echo 'checked="checked"'; ?>>
									{{ $feedback_mechanism_term->langs[0]->name }}
								</label>
							</div>
						</div>
						@endforeach
					</div>

				</div>

				<div class="service-card-wrapper">

					<h4 class="card-title mt-3">{{ __('app.Feedback Delay') }}</h4>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<select class="form-control" name="terms[]">
									<option>{{ __('app.None') }}</option>
									@foreach($feedback_delay_terms as $feedback_delay_term)
									<option value="{{ $feedback_delay_term->id }}" <?php if (in_array($feedback_delay_term->id, $service->termIds)) echo 'selected="selected"'; ?>>{{ $feedback_delay_term->langs[0]->name }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>

				</div>

				<div class="service-card-wrapper">

					<h4 class="card-title mt-3">{{ __('app.Complaints Mechanism') }}</h4>
					<div class="row">
						@foreach($complaints_mechanism_terms as $complaints_mechanism_term)
						<div class="col-xl-4 col-md-6 col-sm-12">
							<div class="form-check form-check-flat mt-0">
								<label class="form-check-label">
									<input type="checkbox" class="form-check-input" name="terms[]" value="{{ $complaints_mechanism_term->id }}" <?php if (in_array($complaints_mechanism_term->id, $service->termIds)) echo 'checked="checked"'; ?>>
									{{ $complaints_mechanism_term->langs[0]->name }}
								</label>
							</div>
						</div>
						@endforeach
					</div>

				</div>

			</div>
		</div>
	</div>
	<div class="card horizontal-form">
		<div class="card-header" role="tab" id="heading-7">
			<h5 class="mb-0">
				<a data-toggle="collapse" href="#collapse-7" aria-expanded="true" aria-controls="collapse-7">
					{{ __('app.Additional Information') }}
				</a>
			</h5>
		</div>
		<div id="collapse-7" class="collapse show" role="tabpanel" aria-labelledby="heading-7">
			<div class="card-body">
				<div class="row">

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
							<div class="tab-pane fade {{ $loop->index == 0 ? 'show active' : '' }}" id="lang-{{ $lang->id }}" role="tabpanel" aria-labelledby="lang-{{ $lang->id }}-tab">
								<div class="row mt-3">
									<div class="form-group col-sm-12">
										{!! Form::label('langs[l'.$lang->id.'][additional]', __('app.Additional Details:')) !!}
										{!! Form::textarea('langs[l'.$lang->id.'][additional]', null, ['class' => 'form-control']) !!}
										<p class="hint">{{ __('app.If needed, you can add additional details') }}.</p>
									</div>
									<div class="form-group col-sm-12">
										{!! Form::label('langs[l'.$lang->id.'][comments]', __('app.Comments:')) !!}
										{!! Form::textarea('langs[l'.$lang->id.'][comments]', null, ['class' => 'form-control']) !!}
										<p class="hint">{{ __('app.Comments will only be visible in the back-end to the other service providers') }}.</p>
									</div>
									<div class="form-group col-sm-12">
										{!! Form::label('langs[l'.$lang->id.'][phone]', __('app.Hotline / Public Phone:')) !!}
										{!! Form::text('langs[l'.$lang->id.'][phone]', null, ['class' => 'form-control']) !!}
									</div>
									<div class="form-group col-sm-12">
										{!! Form::label('langs[l'.$lang->id.'][link]', __('app.More Info Link:')) !!}
										{!! Form::text('langs[l'.$lang->id.'][link]', null, ['class' => 'form-control']) !!}
									</div>
									{!! Form::hidden('langs[l'.$lang->id.'][name]', null, ['class' => 'form-control']) !!}
									{!! Form::hidden('langs[l'.$lang->id.'][slug]', null, ['class' => 'form-control']) !!}
								</div>
							</div>
							@endforeach
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>



	<div class="card horizontal-form">
		<div class="card-header" role="tab" id="heading-8">
			<h5 class="mb-0">
				<a data-toggle="collapse" href="#collapse-8" aria-expanded="false" aria-controls="collapse-8">
					{{ __('app.Service Status') }}
				</a>
			</h5>
		</div>
		<div id="collapse-8" class="collapse show" role="tabpanel" aria-labelledby="heading-8" style="">
			<div class="card-body">
				<div class="row">

					<div class="col-sm-12 mt-3">

						<div class="form-group col-sm-6">
							<label class="checkbox-inline">
								<div class="form-check form-check-flat mt-0">
									<label class="form-check-label">
										<input type="checkbox" name="backendonly" class="form-check-input" <?php if ($service->backendonly) echo 'checked="checked"'; ?>>
										{{ __('app.Backend only') }}
									</label>
								</div>

							</label>
						</div>

						<div class="service-statuses" <?php echo isset($service->backendonly) && $service->backendonly ? 'style="display:none"' : '' ?>>

							<!-- Published Field -->
							<div class="form-group col-sm-6">
								<label class="checkbox-inline">
									<div class="form-check form-check-flat mt-0">
										<label class="form-check-label">
											<input type="checkbox" name="published" class="form-check-input" <?php if ($service->published) echo 'checked="checked"'; ?>>
											{{ __('app.Published') }}
										</label>
									</div>

								</label>
							</div>

							@if(Auth::user()->isInRole('sysadmin') || Auth::user()->isInRole('admin'))
							<div class="form-group row col-md-6">
								{!! Form::label('publish_date', __('app.Publish Date:'), ['class' => 'col-sm-3 col-form-label']) !!}
								<div class="col-sm-9">
									{!! Form::text('publish_date', null, ['class' => 'form-control','id'=>'publish_date']) !!}
								</div>
							</div>
							@else
							{!! Form::hidden('publish_date', null, []) !!}
							@endif

						</div>

						@if($service != null && isset($service->id) && $service->id > 0)

						<div class="form-group row col-md-6">
							{!! Form::label('created_by', __('app.Created By:'), ['class' => 'col-sm-3 col-form-label']) !!}
							<div class="col-sm-9">
								<p class="form-text-value">
									@if($service->creator != null)
									<a href="{{ URL::to('admin/profile/'.$service->creator->username) }}">{{ $service->creator->name }}</a>
									@else
									{{ $service->created_by }}
									@endif
								</p>
							</div>
						</div>

						<div class="form-group row col-md-6">
							{!! Form::label('created_at', __('app.Created At:'), ['class' => 'col-sm-3 col-form-label']) !!}
							<div class="col-sm-9">
								<p class="form-text-value">{{ $service->created_at->format('d.m.Y H:i:s') }}</p>
							</div>
						</div>

						<div class="form-group row col-md-6">
							{!! Form::label('updated_by', __('app.Updated By:'), ['class' => 'col-sm-3 col-form-label']) !!}
							<div class="col-sm-9">
								<p class="form-text-value">
									@if($service->editor != null)
									<a href="{{ URL::to('admin/profile/'.$service->editor->username) }}">{{ $service->editor->name }}</a>
									@else
									{{ $service->updated_by }}
									@endif
								</p>
							</div>
						</div>

						<div class="form-group row col-md-6">
							{!! Form::label('updated_at', __('app.Updated At:'), ['class' => 'col-sm-3 col-form-label']) !!}
							<div class="col-sm-9">
								<p class="form-text-value">{{ $service->updated_at->format('d.m.Y H:i:s') }}</p>
							</div>
						</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<!-- Submit Field -->
<div class="footer-buttons form-group col-sm-12">
	<input type="hidden" id="service_hours" name="service_hours" value="{{ json_encode($service->hours) }}">
	{!! Form::submit(__('app.Save'), ['class' => 'btn btn-primary']) !!}
	<a href="{!! route('services.services.index') !!}" class="btn btn-default">{{ __('app.Cancel') }}</a>
</div>

<div class="modal fade" id="locationModal" tabindex="-1" role="dialog" aria-labelledby="locationModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-body px-0 py-0">
				<iframe id="locationFrame" data-src="{{ url('/admin/services/locations/list') }}" frameborder="0" style="width: 100%;height: 900px;" scrolling="no"></iframe>
			</div>
		</div>
	</div>
</div>