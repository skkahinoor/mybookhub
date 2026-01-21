<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Bookhub - Register</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('user/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('user/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('user/vendors/css/vendor.bundle.base.css') }}">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('user/css/vertical-layout-light/style.css') }}">
    <!-- endinject -->
    <link rel="shortcut icon" href="{{ asset('uploads/logos/' . $headerLogo->favicon) }}" />
    @php
        use App\Models\HeaderLogo;
        $headerLogo = HeaderLogo::first();
    @endphp
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                            <div class="brand-logo">
                                <img src="{{ asset('uploads/logos/' . $headerLogo->logo) }}" alt="logo">
                            </div>
                            <h4>New here?</h4>
                            <h6 class="font-weight-light">Signing up is easy. It only takes a few steps</h6>
                            <form class="pt-3" action="{{ route('user.registerstore') }}" method="post">
                                @csrf
                                <input type="hidden" name="user_type" value="user">
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-lg"
                                        id="exampleInputUsername1" name="name" placeholder="Username">
                                </div>
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-lg" id="exampleInputEmail1" name="email"
                                        placeholder="Email">
                                </div>
                                <div class="form-group">
                                    <input type="number" class="form-control form-control-lg" id="exampleInputPhone1" name="phone"
                                        placeholder="Mobile Number">
                                </div>

                                <div class="form-group">
                                    <input type="password" class="form-control form-control-lg"
                                        id="exampleInputPassword1" name="password" placeholder="Password">
                                </div>
                                <div class="mb-4">
                                    <div class="form-check">
                                        <label class="form-check-label text-muted">
                                            <input type="checkbox" class="form-check-input">
                                            I agree to all Terms & Conditions
                                        </label>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button type="submit"
                                        class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">SIGN
                                        UP</button>
                                </div>
                                <div class="text-center mt-4 font-weight-light">
                                    Already have an account? <a href="{{ route('user.login') }}"
                                        class="text-primary">Login</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- content-wrapper ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="{{ asset('user/vendors/js/vendor.bundle.base.js') }}"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{ asset('user/js/off-canvas.js') }}"></script>
    <script src="{{ asset('user/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('user/js/template.js') }}"></script>
    <script src="{{ asset('user/js/settings.js') }}"></script>
    <script src="{{ asset('user/js/todolist.js') }}"></script>
    <!-- endinject -->
</body>

</html>
