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
                                <a href="{{ url('/admin') }}"><img src="img/logo.svg"/></a>
                            </div>
                            
                            <form method="post" action="{{ url('/register') }}">

                                {!! csrf_field() !!}

                                <div class="form-group has-feedback{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="Full Name">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="mdi mdi-account-outline"></i></span>
                                        </div>
                                    </div>

                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group has-feedback{{ $errors->has('email') ? ' has-error' : '' }}">
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

                                <div class="form-group d-flex justify-content-between">
                                    <div class="form-check form-check-flat mt-0">
                                        <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" checked="" name="terms">
                                        I agree to the 
                                        <i class="input-helper"></i><a href="#">terms</a></label>
                                        
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary submit-btn btn-block">Register</button>
                                </div>

                                <div class="text-block text-center my-3">
                                    <span class="text-small font-weight-semibold">Already have and account ?</span>
                                    <a href="{{ url('/login') }}" class="text-black text-small">Login</a>
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