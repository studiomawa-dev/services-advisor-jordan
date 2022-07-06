<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
	<title>Services Advisor</title>
	<link rel="shortcut icon" href="img/favicon.png" />
	<link rel="stylesheet" href="//cdn.materialdesignicons.com/3.6.95/css/materialdesignicons.min.css">
	<link rel="stylesheet" href="{{ url('/css/vendor.css') }}">
	<link rel="stylesheet" href="{{ url('/css/custom.css') }}">
	@yield('css')
</head>

<body>
	<div class="container-scroller">
		<div class="container-fluid page-body-wrapper full-page-wrapper">
			<div class="content-wrapper d-flex align-items-center auth auth-bg-1 theme-one">
				<div class="row w-100 mx-auto">
					<div class="col-lg-4 mx-auto">
						<div class="auto-form-wrapper">
							<div class="register-logo mb-5">
								<a href="{{ url('/admin') }}"><img src="{{ url('/img/logo.svg') }}" /></a>
							</div>

							@if (session('status'))
							<div class="alert alert-success">
								{{ session('status') }}
							</div>
							@endif

							@if (session('warning'))
							<div class="alert alert-warning">
								{{ session('warning') }}
							</div>
							@endif

							<form method="post" action="{{ url('/password/email') }}">
								{!! csrf_field() !!}

								<div class="form-group has-feedback {{ $errors->has('email') ? ' has-error' : '' }}">
									<div class="input-group">
										<input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email">
										<div class="input-group-append">
											<span class="input-group-text"><i class="mdi mdi-email-outline"></i></span>
										</div>
									</div>

									@if ($errors->has('email'))
									<span class="help-block">
										<strong>{{ $errors->first('email') }}</strong>
									</span>
									@endif
								</div>

								<div class="form-group">
									<button type="submit" class="btn btn-primary submit-btn btn-block"> <i class="mdi mdi-email"></i>Send Password Reset Link</button>
								</div>
							</form>
						</div>
						<ul class="auth-footer">
							<li><a href="#">Conditions</a></li>
							<li><a href="#">Help</a></li>
							<li><a href="#">Terms</a></li>
						</ul>
						<p class="footer-text text-center">Copyright © {{ date('Y') }} Services Advisor. All rights reserved.</p>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script src="{{ url('/js/vendor.js') }}"></script>
	<script src="{{ url('/js/custom.js') }}"></script>

</body>

</html>
