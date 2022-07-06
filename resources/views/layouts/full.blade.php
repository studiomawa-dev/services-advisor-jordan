<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
	<title>Services Advisor</title>
	<link rel="shortcut icon" href="img/favicon.png" />
	<link rel="stylesheet" href="//cdn.materialdesignicons.com/3.6.95/css/materialdesignicons.min.css">
	<script src="https://unpkg.com/@mapbox/mapbox-sdk/umd/mapbox-sdk.min.js"></script>
	<script src='https://api.tiles.mapbox.com/mapbox-gl-js/v1.0.0/mapbox-gl.js'></script>
	<link href='https://api.tiles.mapbox.com/mapbox-gl-js/v1.0.0/mapbox-gl.css' rel='stylesheet' />
	<script src='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.2.0/mapbox-gl-geocoder.min.js'></script>
	<link rel='stylesheet' href='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.2.0/mapbox-gl-geocoder.css' type='text/css' />

	<link rel="stylesheet" href="{{ url('/css/vendor.css') }}">
	<link rel="stylesheet" href="{{ url('/css/custom.css') }}">
	@yield('css')
</head>

<body>
	<div class="container-scroller">

		<div id="page-content-wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-lg-12">
						@yield('content')
					</div>
				</div>
			</div>
		</div>
	</div>

	<script src="{{ url('/js/vendor.js') }}"></script>
	<script src="{{ url('/js/custom.js') }}"></script>

	@yield('scripts')
	@yield('pagescripts')
</body>

</html>
