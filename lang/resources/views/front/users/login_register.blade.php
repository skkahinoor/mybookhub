@extends('front.layout.layout3')

@section('content')
    <!-- Page Introduction Wrapper -->
    <div class="page-style-a">
        <div class="container">
            <div class="page-intro mb-5">
                <div class="text-center">
                    <h1 class="display-4 mb-4">Welcome to BookHub</h1>
                    <div class="bread-crumb-wrapper bg-white d-inline-block py-2 px-4 rounded shadow-sm">
                        <ul class="bread-crumb d-inline-block">
                            <li class="has-separator d-inline-block mx-2">
                                <a href="{{ url('/') }}" class="btn btn-light text-decoration-none font-weight-bold"
                                    style="color: #cf8938; border: none;">Home</a>
                            </li>
                            <li class="is-marked d-inline-block mx-2">
                                <a href="" class="btn btn-light text-decoration-none font-weight-bold"
                                    style="color: #cf8938; border: none;">Account</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Introduction Wrapper /- -->

    <!-- Account-Page -->
    <div class="page-account">
        <div class="container">
            <div class="alert-wrapper mb-4">
                @if (Session::has('success_message'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                        <i class="fas fa-check-circle mr-2"></i>
                        <strong>Success:</strong> {{ Session::get('success_message') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                @if (Session::has('error_message'))
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <strong>Error:</strong> {{ Session::get('error_message') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <strong>Error:</strong> @php echo implode('', $errors->all('<div>:message</div>')); @endphp
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
            </div>

            <div class="row justify-content-center">
                <!-- Login -->
                <div class="col-lg-5">
                    <div class="login-wrapper bg-white p-4 rounded shadow-sm">
                        <div class="text-center mb-4">
                            <i class="fas fa-user-circle mb-3" style="font-size: 48px; color:#cf8938"></i>
                            <h2 class="account-h2">Login</h2>
                            <h6 class="account-h6 text-muted">Welcome back! Sign in to your account.</h6>
                        </div>

                        <p id="login-error" class="text-danger"></p>
                        <form action="{{ route('user.login') }}" method="post">
                            @csrf

                            <div class="form-group mb-4">
                                <label for="user-email" class="font-weight-bold">
                                    <i class="fas fa-envelope mr-2 text-muted"></i>Email
                                </label>
                                <input type="email" name="email" id="users-email" class="form-control"
                                    placeholder="Enter your email">
                                <p id="login-email" class="text-danger small mt-1"></p>
                            </div>
                            <div class="form-group mb-4">
                                <label for="user-password" class="font-weight-bold">
                                    <i class="fas fa-lock mr-2 text-muted"></i>Password
                                </label>
                                <input type="password" name="password" id="users-password" class="form-control"
                                    placeholder="Enter your password">
                                <p id="login-password" class="text-danger small mt-1"></p>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="page-anchor">
                                    <a href="{{ url('user/forgot-password') }}" class="text-decoration-none text-muted">
                                        <i class="fas fa-key mr-1"></i>Forgot Password?
                                    </a>
                                </div>
                            </div>

                            <button class="btn btn-primary w-100 rounded" style="background-color: #cf8938; border: none;">
                                <i class="fas fa-walking mr-2"></i>Login
                            </button>

                        </form>
                    </div>
                </div>
                <!-- Login /- -->

                <!-- Register -->
                <div class="col-lg-5">
                    <div class="reg-wrapper bg-white p-4 rounded shadow-sm">
                        <div class="text-center mb-4">
                            <i class="fas fa-user-plus mb-3" style="font-size: 48px; color:#cf8938"></i>
                            <h2 class="account-h2">Register</h2>
                            <h6 class="account-h6 text-muted">Create an account to access your order history.</h6>
                        </div>

                        <p id="register-success" class="text-success"></p>

                        <form id="registerForm" action="{{ route('user.register') }}" method="post">
                            @csrf

                            <div class="form-group mb-3">
                                <label for="username" class="font-weight-bold">
                                    <i class="fas fa-user mr-2 text-muted"></i>Name
                                </label>
                                <input type="text" id="user-name" class="form-control" placeholder="Enter your name"
                                    name="name">
                                <p id="register-name" class="text-danger small mt-1"></p>
                            </div>
                            <div class="form-group mb-3">
                                <label for="usermobile" class="font-weight-bold">
                                    <i class="fas fa-mobile-alt mr-2 text-muted"></i>Mobile
                                </label>
                                <input type="number" id="user-mobile" class="form-control"
                                    placeholder="Enter your mobile number" name="mobile">
                                <p id="register-mobile" class="text-danger small mt-1"></p>
                            </div>
                            <div class="form-group mb-3">
                                <label for="useremail" class="font-weight-bold">
                                    <i class="fas fa-envelope mr-2 text-muted"></i>Email
                                </label>
                                <input type="email" id="user-email" class="form-control"
                                    placeholder="Enter your email" name="email">
                                <p id="register-email" class="text-danger small mt-1"></p>
                            </div>
                            <div class="form-group mb-4">
                                <label for="userpassword" class="font-weight-bold">
                                    <i class="fas fa-lock mr-2 text-muted"></i>Password
                                </label>
                                <input type="password" id="user-password" class="form-control"
                                    placeholder="Choose a password" name="password">
                                <p id="register-password" class="text-danger small mt-1"></p>
                            </div>
                            <div class="form-group mb-4">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="accept" name="accept">
                                    <label class="custom-control-label" for="accept">I've read and accept the
                                        <a href="terms-and-conditions.html"
                                            class="text-primary text-decoration-none">terms & conditions</a>
                                    </label>
                                </div>
                                <p id="register-accept" class="text-danger small mt-1"></p>
                            </div>

                            <button class="btn btn-primary w-100  rounded"
                                style="background-color: #cf8938; border: none;">
                                <i class="fas fa-user mr-2"></i>Register
                            </button>
                        </form>
                    </div>
                </div>
                <!-- Register /- -->
            </div>
        </div>
    </div>
    <!-- Account-Page /- -->

    <style>
        .page-intro {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 3rem 0;
            margin-bottom: 2rem;
        }

        .bread-crumb-wrapper {
            transition: all 0.3s ease;
        }

        .bread-crumb-wrapper:hover {
            transform: translateY(-2px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .1) !important;
        }

        .login-wrapper,
        .reg-wrapper {
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, .05);
        }

        .login-wrapper:hover,
        .reg-wrapper:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, .1) !important;
        }

        .form-control {
            height: 45px;
            border-radius: 4px;
            border: 1px solid #ced4da;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .15);
            transform: translateY(-1px);
        }

        .button-primary {
            font-size: 16px;
            font-weight: 600;
            border-radius: 4px;
            transition: all 0.3s ease;
            text-transform: none;
            letter-spacing: 0.5px;
        }

        .button-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, .1);
        }

        .button-primary:active {
            transform: translateY(0);
        }

        .custom-control-input:checked~.custom-control-label::before {
            border-color: #007bff;
            background-color: #007bff;
        }

        .alert {
            border-radius: 8px;
            box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075);
        }

        .text-decoration-none:hover {
            opacity: 0.8;
        }

        @media (max-width: 768px) {
            .page-intro {
                padding: 2rem 0;
            }

            .display-4 {
                font-size: 2rem;
            }
        }
    </style>
@endsection
