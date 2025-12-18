<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Vendor Registration</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ url('admin/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ url('admin/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ url('admin/vendors/css/vendor.bundle.base.css') }}">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ url('admin/css/vertical-layout-light/style.css') }}">
    <!-- endinject -->
    <link rel="shortcut icon" href="{{ asset('admin/images/favicon.png') }}" />
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                            <h4>Vendor Registration</h4>
                            <h6 class="font-weight-light">Sign up to become a vendor.</h6>


                            {{-- Our Bootstrap error code in case of wrong credentials when logging in: --}}
                            {{-- Determining If An Item Exists In The Session (using has() method): https://laravel.com/docs/9.x/session#determining-if-an-item-exists-in-the-session --}}
                            {{-- Alerts --}}
                            @if (session('success_message'))
                                <div class="alert alert-success">{{ session('success_message') }}</div>
                            @endif

                            @if (session('error_message'))
                                <div class="alert alert-danger">{{ session('error_message') }}</div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    @foreach ($errors->all() as $error)
                                        <div>{{ $error }}</div>
                                    @endforeach
                                </div>
                            @endif


                            {{-- My code --}}
                            <form method="POST" action="{{ route('vendor.register.submit') }}">
                                @csrf
        
                                <div class="form-group mb-3">
                                    <input type="text" name="name" class="form-control" placeholder="Vendor Name" required>
                                </div>
        
                                <div class="form-group mb-3">
                                    <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                                </div>
        
                                <div class="form-group mb-3">
                                    <input type="number" name="mobile" class="form-control" placeholder="Mobile Number" required>
                                </div>
        
                                <div class="form-group mb-3">
                                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                                </div>
        
                                <div class="form-group mb-3">
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    Register
                                </button>
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
   <script src="{{ url('admin/vendors/js/vendor.bundle.base.js') }}"></script> 
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{ url('admin/js/off-canvas.js') }}"></script>
    <script src="{{ url('admin/js/hoverable-collapse.js') }}"></script>
    <script src="{{ url('admin/js/template.js') }}"></script>
    <script src="{{ url('admin/js/settings.js') }}"></script>
    <script src="{{ url('admin/js/todolist.js') }}"></script>
    <!-- endinject --> 
</body>

</html>
