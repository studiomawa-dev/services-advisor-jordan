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

							<form method="post" action="{{ url('/password/reset') }}">
								{!! csrf_field() !!}

								<input type="hidden" name="token" value="{{ $token }}">

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

								<div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
									<div class="input-group">
										<input type="password" class="form-control" name="password" placeholder="Password">
										<div class="input-group-append">
											<span class="input-group-text"><i class="mdi mdi-lock-outline"></i></span>
										</div>
									</div>
									@if ($errors->has('password'))
									<span class="help-block">
										<strong>{{ $errors->first('password') }}</strong>
									</span>
									@endif
								</div>

								<div class="form-group has-feedback{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
									<div class="input-group">
										<input type="password" name="password_confirmation" class="form-control" placeholder="Confirm password">
										<div class="input-group-append">
											<span class="input-group-text"><i class="mdi mdi-lock-outline"></i></span>
										</div>
									</div>

									@if ($errors->has('password_confirmation'))
									<span class="help-block">
										<strong>{{ $errors->first('password_confirmation') }}</strong>
									</span>
									@endif
								</div>

								<div class="form-group">
									<button type="submit" class="btn btn-primary submit-btn btn-block"> <i class="mdi mdi-refresh"></i>Reset Password</button>
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
