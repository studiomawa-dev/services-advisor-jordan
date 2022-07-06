<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
	<title>Services Advisor</title>
	<link rel="shortcut icon" href="img/favicon.png" />
	<link rel="stylesheet" href="//cdn.materialdesignicons.com/3.6.95/css/materialdesignicons.min.css">
	<link rel="stylesheet" href="css/vendor.css">
	<link rel="stylesheet" href="css/custom.css">
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
								<a href="{{ url('/admin') }}"><img src="img/logo.svg" /></a>
							</div>

							<form method="post" action="{{ url('/login') }}">
								{!! csrf_field() !!}

								<div class="form-group has-feedback {{ $errors->has('login') ? ' has-error' : '' }}">
									<label class="label">E-mail or Username</label>
									<div class="input-group">
										<input type="login" class="form-control" name="login" value="{{ old('login') }}">
										<div class="input-group-append">
											<span class="input-group-text"><i class="mdi mdi-account-outline"></i></span>
										</div>
									</div>
									<span class="glyphicon glyphicon-envelope form-control-feedback"></span>
									@if ($errors->has('login'))
									<span class="help-block">
										<strong>{{ $errors->first('login') }}</strong>
									</span>
									@endif
								</div>

								<div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
									<label class="label">Password</label>
									<div class="input-group">
										<input type="password" class="form-control" name="password">
										<div class="input-group-append">
											<span class="input-group-text"><i class="mdi mdi-lock-outline"></i></span>
										</div>
									</div>
									<span class="glyphicon glyphicon-lock form-control-feedback"></span>
									@if ($errors->has('password'))
									<span class="help-block">
										<strong>{{ $errors->first('password') }}</strong>
									</span>
									@endif

								</div>

								<div class="form-group">
									<button type="submit" class="btn btn-primary submit-btn btn-block">Login</button>
								</div>

								<div class="form-group d-flex justify-content-between">
									<div class="form-check form-check-flat mt-0">
										<label class="form-check-label">
											<input type="checkbox" class="form-check-input" checked="" name="remember">
											Keep me signed in
											<i class="input-helper"></i></label>
									</div>
									<a href="{{ url('/password/reset') }}" class="text-small forgot-password text-black">Forgot Password</a>
								</div>
								<!--
                                <div class="text-block text-center my-3">
                                    <span class="text-small font-weight-semibold">Not a member ?</span>
                                    <a href="{{ url('/register') }}" class="text-black text-small">Create new account</a>
								</div>
								-->
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

	<script src="js/vendor.js"></script>
	<script src="js/custom.js"></script>

</body>

</html>
