<!DOCTYPE html>
<html lang="en">

<head>
    @php
        use App\Models\HeaderLogo;
        $headerLogo = HeaderLogo::first();
    @endphp
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>BookHub - Forgot Password</title>
    <link rel="stylesheet" href="{{ asset('user/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('user/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('user/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('user/css/vertical-layout-light/style.css') }}">
    @if (isset($headerLogo) && filled($headerLogo->favicon))
        <link rel="shortcut icon" href="{{ asset('uploads/logos/' . $headerLogo->favicon) }}" />
    @endif
</head>

<body>
    @php
        $resetPhone = old('phone', session('reset_phone'));
        $showResetForm = session()->has('forgot_success') || $errors->resetPassword->any() || old('otp');
    @endphp
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-5 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                            <div class="brand-logo">
                                @if (isset($headerLogo) && filled($headerLogo->logo))
                                    <img src="{{ asset('uploads/logos/' . $headerLogo->logo) }}" alt="logo">
                                @else
                                    <span class="h4 mb-0 font-weight-bold text-primary">BookHub</span>
                                @endif
                            </div>

                            <h4>Reset Password</h4>
                            <h6 class="font-weight-light mb-3">Enter your mobile number, verify OTP, and set a new password.</h6>

                            @if (session('forgot_success'))
                                <div class="alert alert-success mt-2">
                                    {{ session('forgot_success') }}
                                </div>
                            @endif

                            <form action="{{ route('student.forgot-password') }}" method="post" class="mb-3">
                                @csrf
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-lg" name="phone"
                                        placeholder="Registered mobile number" value="{{ $resetPhone }}">
                                    @if ($errors->forgotPassword->has('phone'))
                                        <small class="text-danger">{{ $errors->forgotPassword->first('phone') }}</small>
                                    @endif
                                </div>
                                <button type="submit" class="btn btn-outline-primary btn-block">Send OTP</button>
                            </form>

                            @if ($showResetForm)
                                <hr class="my-4">
                                <p class="text-muted mb-2">OTP sent to: <strong>{{ $resetPhone }}</strong></p>
                                <form action="{{ route('student.reset-password') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="phone" value="{{ $resetPhone }}">
                                    @if ($errors->resetPassword->has('phone'))
                                        <div class="mb-2">
                                            <small class="text-danger">{{ $errors->resetPassword->first('phone') }}</small>
                                        </div>
                                    @endif
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control-lg" name="otp"
                                            placeholder="Enter OTP" value="{{ old('otp') }}">
                                        @if ($errors->resetPassword->has('otp'))
                                            <small class="text-danger">{{ $errors->resetPassword->first('otp') }}</small>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <input type="password" class="form-control form-control-lg" name="password"
                                            placeholder="New password">
                                        @if ($errors->resetPassword->has('password'))
                                            <small class="text-danger">{{ $errors->resetPassword->first('password') }}</small>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <input type="password" class="form-control form-control-lg" name="password_confirmation"
                                            placeholder="Confirm new password">
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block">Verify OTP & Reset Password</button>
                                </form>
                            @endif

                            <div class="text-center mt-4">
                                <a href="{{ route('student.login') }}" class="text-primary">Back to login</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('user/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('user/js/off-canvas.js') }}"></script>
    <script src="{{ asset('user/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('user/js/template.js') }}"></script>
    <script src="{{ asset('user/js/settings.js') }}"></script>
    <script src="{{ asset('user/js/todolist.js') }}"></script>
</body>

</html>
