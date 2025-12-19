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


                            {{-- Vendor Registration with OTP (similar to Sales) --}}
                            <form method="POST" action="{{ route('vendor.register.submit') }}" id="vendorRegisterForm">
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

                                {{-- OTP Input - Hidden initially --}}
                                <div class="form-group mb-3 d-none" id="otpSection">
                                    <input type="text" name="otp" class="form-control" placeholder="Enter OTP">
                                </div>

                                {{-- Password - Hidden until OTP sent --}}
                                <div class="form-group mb-3 d-none" id="passwordSection">
                                    <input type="password" name="password" class="form-control" placeholder="Password">
                                </div>

                                {{-- Confirm Password - Hidden until OTP sent --}}
                                <div class="form-group mb-3 d-none" id="confirmPasswordSection">
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password">
                                </div>

                                {{-- Send OTP Button --}}
                                <button type="button" class="btn btn-primary w-100 mb-2" id="sendOtpBtn">
                                    Send OTP
                                </button>

                                {{-- Verify + Register Button --}}
                                <button type="submit" class="btn btn-success w-100 d-none" id="verifyBtn">
                                    Verify OTP & Register
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

    {{-- jQuery for OTP (if not already loaded) --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $('#sendOtpBtn').click(function(e) {
            e.preventDefault();

            let formData = {
                name: $("input[name='name']").val(),
                email: $("input[name='email']").val(),
                mobile: $("input[name='mobile']").val(),
                _token: "{{ csrf_token() }}"
            };

            $.ajax({
                url: "{{ route('vendor.otp.send') }}",
                type: "POST",
                data: formData,
                success: function(response) {
                    if (response.status === true) {
                        alert("OTP Sent Successfully");

                        $("input[name='email']").prop('readonly', true);
                        $("input[name='mobile']").prop('readonly', true);

                        // Show OTP + Password fields
                        $('#otpSection').removeClass('d-none');
                        $('#passwordSection').removeClass('d-none');
                        $('#confirmPasswordSection').removeClass('d-none');
                        $('#verifyBtn').removeClass('d-none');
                        $('#sendOtpBtn').addClass('d-none');
                    } else {
                        alert(response.message || 'Something went wrong while sending OTP.');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;

                        // Remove old error messages
                        $('.text-danger').remove();

                        // Email error
                        if (errors.email) {
                            $("input[name='email']")
                                .closest('.form-group')
                                .append('<span class="text-danger small">' + errors.email[0] + '</span>');
                        }

                        // Mobile error
                        if (errors.mobile) {
                            $("input[name='mobile']")
                                .closest('.form-group')
                                .append('<span class="text-danger small">' + errors.mobile[0] + '</span>');
                        }
                    } else {
                        alert('Failed to send OTP. Please try again.');
                    }
                }
            });
        });
    </script>
</body>

</html>
