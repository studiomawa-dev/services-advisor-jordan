(function ($) {
	"use strict";
	$(function () {
		var body = $("body");
		var footer = $(".footer");

		//var current = location.pathname.split("?")[0]; //.split("/").slice(-1)[0].replace(/^\/|\/$/g, '');
		var current = location.href.split("&")[0]; //.split("/").slice(-1)[0].replace(/^\/|\/$/g, '');

		$(
			".navbar.horizontal-layout .nav-bottom .page-navigation .nav-item"
		).each(function () {
			var $this = $(this);
			if (current === "") {
				//for root url
				if (
					$this
						.find(".nav-link")
						.attr("href")
						.indexOf("index.html") !== -1
				) {
					$(this)
						.find(".nav-link")
						.parents(".nav-item")
						.last()
						.addClass("active");
					$(this).addClass("active");
				}
			} else {
				//for other url
				if ($this.find(".nav-link").attr("href") == current) {
					//$this.find(".nav-link").attr('href').indexOf(current) !== -1
					$(this)
						.find(".nav-link")
						.parents(".nav-item")
						.last()
						.addClass("active");
					$(this).addClass("active");
				}
			}
		});

		$(window).scroll(function () {
			var headerBottom = ".navbar.horizontal-layout .nav-bottom";
			if ($(window).scrollTop() >= 70) {
				$(headerBottom).addClass("fixed-top");
			} else {
				$(headerBottom).removeClass("fixed-top");
			}
		});

		$(".navbar.horizontal-layout .navbar-menu-wrapper .navbar-toggler").on(
			"click",
			function () {
				$(".navbar.horizontal-layout .nav-bottom").toggleClass(
					"header-toggled"
				);
			}
		);

		//checkbox and radios
		$(".form-check .form-check-label,.form-radio .form-check-label")
			.not(".todo-form-check .form-check-label")
			.append('<i class="input-helper"></i>');

		$(".js-select").each(function (_, sinput) {
			$(sinput).select2({
				placeholder: "Select an option",
				allowClear: true,
			});
			if ($(sinput).is(":hidden")) {
				$(sinput).next().hide();
			}
		});

		/* $(".js-select").select2({
			placeholder: "Select an option",
			allowClear: true
		}); */

		$('#filter-form button[type="submit"]').click(function () {
			for (var key in localStorage) {
				if (key.indexOf("DataTables_dataTableBuilder") > -1) {
					localStorage.removeItem(key);
				}
			}
		});
	});
})(jQuery);

(function ($) {
	"use strict";
	$(function () {
		if ($("#product-area-chart").length) {
			var lineChartCanvas = $("#product-area-chart")
				.get(0)
				.getContext("2d");
			var data = {
				labels: [
					"2013",
					"2014",
					"2014",
					"2015",
					"2016",
					"2017",
					"2018",
				],
				datasets: [
					{
						label: "Visitor",
						data: [150, 200, 150, 200, 350, 320, 400],
						backgroundColor: "rgba(70, 77, 228, 0.3)",
						borderColor: ["rgba(70, 77, 228, 1)"],
						borderWidth: 1,
						fill: true,
					},
					{
						label: "Page View",
						data: [300, 400, 300, 440, 700, 550, 730],
						backgroundColor: "rgba(217, 225 ,253, 1)",
						borderColor: ["rgba(70, 77, 228, 1)"],
						borderWidth: 1,
						fill: true,
					},
				],
			};
			var options = {
				scales: {
					yAxes: [
						{
							display: false,
						},
					],
					xAxes: [
						{
							display: false,
						},
					],
				},
				legend: {
					display: false,
				},
				elements: {
					point: {
						radius: 3,
					},
				},
				stepsize: 1,
			};
			var lineChart = new Chart(lineChartCanvas, {
				type: "line",
				data: data,
				options: options,
			});
		}
	});
})(jQuery);

(function ($) {
	"use strict";
	$(function () {
		if ($(".collapse-item").length) {
			$(".collapse-item .collapse-icon").each(function () {
				let _id = $(this).closest(".collapse-item").data("id");
				if ($('[data-parent="' + _id + '"]').length) {
					$(this).html(
						'<i class="level-icon mdi mdi-chevron-right"></i>'
					);
				}
			});
			//

			$(".collapse-item").click(function () {
				let _id = $(this).data("id");

				$(this).toggleClass("shown");

				if ($(this).hasClass("shown")) {
					$(this)
						.find(".collapse-icon")
						.html(
							'<i class="level-icon mdi mdi-chevron-down"></i>'
						);
				} else {
					$(this)
						.find(".collapse-icon")
						.html(
							'<i class="level-icon mdi mdi-chevron-right"></i>'
						);

					$('[data-parent="' + _id + '"].shown').each(function () {
						$(this).click();
					});
				}

				$('[data-parent="' + _id + '"]').toggleClass("collapse");

				$('[data-parent="' + _id + '"]')
					.not("collapse")
					.css("background-color", "#ccc");
			});
		}
	});
})(jQuery);
