<!DOCTYPE html>
<html lang="{!! $language->code !!}" dir="{!! $language->is_rtl ? 'rtl' : 'ltr' !!}">

<head>
	<meta charset="utf-8">
	<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
	<meta name="csrf-token" content="{{ csrf_token() }}" />
	<title>Services Advisor</title>
	<link rel="shortcut icon" href="{{ url('/img/favicon.png') }}" />

	<link rel="stylesheet" href="//cdn.materialdesignicons.com/3.6.95/css/materialdesignicons.min.css">
	<script src="https://unpkg.com/@mapbox/mapbox-sdk/umd/mapbox-sdk.min.js"></script>
	<script src='https://api.tiles.mapbox.com/mapbox-gl-js/v1.0.0/mapbox-gl.js'></script>
	<link href='https://api.tiles.mapbox.com/mapbox-gl-js/v1.0.0/mapbox-gl.css' rel='stylesheet' />
	<script src='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.2.0/mapbox-gl-geocoder.min.js'></script>
	<link rel='stylesheet' href='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.2.0/mapbox-gl-geocoder.css' type='text/css' />
	<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.3/css/bootstrap-colorpicker.min.css" rel="stylesheet">

	<link rel="stylesheet" href="{{ url('/css/vendor.css') }}">
	<link rel="stylesheet" href="{{ url('/css/custom.css?v=1.2') }}">
	@yield('css')

	<script>
		var conversations = <?= isset($conversations) ? json_encode($conversations) : '[]' ?>;
	</script>

</head>

<body>
	<div class="container-scroller">
		@if (!Auth::guest())
		<div class="wrapper">

			@include('layouts.navbar')

			<div class="container-fluid page-body-wrapper">
				<div class="main-panel">
					<div class="content-wrapper">
						@yield('content')
					</div>

					<footer class="footer">
						<div class="container-fluid clearfix">
							<span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © {{ date('Y') }} <a href="http://www.servicesadvisor.org/" target="_blank">Services Advisor</a>. All rights reserved.</span>
							<span class="text-muted float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Made with <i class="mdi mdi-heart text-danger"></i> by <a href="https://www.studiomawa.com/" target="_blank">Studio Mawa</a>.</span>
						</div>
					</footer>
				</div>
			</div>

		</div>
		@else
		<nav class="navbar navbar-default navbar-static-top">
			<div class="container">
				<div class="navbar-header">

					<!-- Collapsed Hamburger -->
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
						<span class="sr-only">Toggle Navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>

					<!-- Branding Image -->
					<a class="navbar-brand" href="{!! url('/') !!}">
						Services Advisor
					</a>
				</div>

				<div class="collapse navbar-collapse" id="app-navbar-collapse">
					<!-- Left Side Of Navbar -->
					<ul class="nav navbar-nav">
						<li><a href="{!! url('/home') !!}">Home</a></li>
					</ul>

					<!-- Right Side Of Navbar -->
					<ul class="nav navbar-nav navbar-right">
						<!-- Authentication Links -->
						<li><a href="{!! url('/login') !!}">Login</a></li>
						<li><a href="{!! url('/register') !!}">Register</a></li>
					</ul>
				</div>
			</div>
		</nav>

		<div id="page-content-wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-lg-12">
						@yield('content')
					</div>
				</div>
			</div>
		</div>
		@endif
	</div>

	<script src="{{ url('/js/vendor.js') }}"></script>
	<script src="{{ url('/js/custom.js') }}"></script>



	@yield('scripts')
	@yield('additionalscripts')
	@yield('pagescripts')
</body>

</html>