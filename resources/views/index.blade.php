<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<meta charset="utf-8">
	<title>Services Advisor</title>
	<base href="/">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<link rel="icon" type="image/x-icon" href="favicon.ico">
	<link href="https://fonts.googleapis.com/css?family=Nunito:400,700&display=swap&subset=latin-ext" rel="stylesheet">

	<link rel="stylesheet" href="{{ url('styles.380e09c1e0a19adc51fe.css') }}">

	<script>
		window.defaults = {
			api: '<?php echo env("APP_URL") ?>',
			country: '<?php echo env("APP_COUNTRY") ?>',
			lang: 'en',
			langs: [{
					id: 1,
					code: "en",
					name: "English"
				},
				{
					id: 4,
					code: "ar",
					name: "‏العربية‏"
				}
			],
			current_location: [32.2650893, 35.878883],
			zoom: 7,
		}

		window.dataLayer = window.dataLayer || [];

		function gtag() {
			dataLayer.push(arguments);
		}
		gtag('js', new Date());

		var isKiosk = localStorage.getItem('is_kiosk') == 'true';
		var kioskName = localStorage.getItem('kiosk_name');

		var resetInterval = null;
		var needRefresh = false;

		function resetTimer() {
			if (resetInterval != null) {
				clearInterval(resetInterval);
			}
			resetInterval = setTimeout(function(params) {
				var isKiosk = localStorage.getItem('is_kiosk') == 'true';
				if (needRefresh && isKiosk) {
					window.location.href = '/';
				}
			}, 120000);
		}

		function addListener(el, s, fn) {
			s.split(' ').forEach(e => el.addEventListener(e, fn, false));
		}

		function init() {
			addListener(window, 'click mousedown mouseup touchstart touchend', function(e) {
				needRefresh = true;
				resetTimer();
			});

			resetTimer();
		}
	</script>
</head>

<body>
	<app-root></app-root>
	<script type="text/javascript" src="{{ url('runtime.26209474bfa8dc87a77c.js') }}"></script>
	<script type="text/javascript" src="{{ url('es2015-polyfills.8324bb31dd8aa5f2460c.js') }}" nomodule></script>
	<script type="text/javascript" src="{{ url('polyfills.8bbb231b43165d65d357.js') }}"></script>
	<script type="text/javascript" src="{{ url('scripts.7468b983f4a5acdf2c5e.js') }}"></script>
	<script type="text/javascript" src="{{ url('main.5589257a7f2ec6305810.js') }}"></script>
</body>

</html>