<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Delivery Agent Registration | MyBookHub</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('admin/images/favicon.png') }}" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #435ebe;
            --secondary-color: #6c757d;
            --accent-color: #ffc107;
            --bg-gradient: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            --card-bg: #ffffff;
            --text-main: #2c3e50;
            --text-muted: #6c757d;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            color: var(--text-main);
        }

        .registration-card {
            background: var(--card-bg);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            transition: transform 0.3s ease;
        }

        .card-header-section {
            background: #fff;
            padding: 40px 40px 20px;
            text-align: center;
            border-bottom: 1px solid #f1f1f1;
        }

        .logo-img {
            max-width: 180px;
            margin-bottom: 25px;
        }

        .form-section {
            padding: 40px;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 25px;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            font-size: 1rem;
            background: rgba(67, 94, 190, 0.1);
            padding: 8px;
            border-radius: 8px;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 8px;
            color: #4a5568;
        }

        .input-group {
            background: #f8fafc;
            border-radius: 12px;
            padding: 2px 5px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .input-group:focus-within {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(67, 94, 190, 0.1);
            background: #fff;
        }

        .input-group-text {
            background: transparent;
            border: none;
            color: var(--primary-color);
            font-size: 1rem;
        }

        .form-control, .form-select {
            border: none;
            background: transparent;
            padding: 12px 10px;
            font-size: 0.95rem;
            color: #2d3748;
        }

        .form-control:focus, .form-select:focus {
            box-shadow: none;
            background: transparent;
        }

        .btn-register {
            background: var(--primary-color);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 700;
            font-size: 1.1rem;
            width: 100%;
            margin-top: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(67, 94, 190, 0.4);
        }

        .btn-register:hover {
            background: #364b9a;
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(67, 94, 190, 0.3);
        }

        .footer-link {
            text-align: center;
            margin-top: 30px;
            font-size: 0.95rem;
        }

        .footer-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 700;
        }

        .footer-link a:hover {
            text-decoration: underline;
        }

        .alert {
            border-radius: 16px;
            border: none;
            font-weight: 500;
        }

        /* Loading Spinner */
        .spinner-border-sm {
            display: none;
            margin-left: 5px;
        }

        @media (max-width: 768px) {
            .form-section {
                padding: 25px;
            }
            .registration-card {
                border-radius: 16px;
            }
        }
    </style>
</head>
<body>

<div class="registration-card">
    <div class="card-header-section">
        <a href="{{ url('/') }}">
            <img src="{{ asset('front/images/logos/logo.png') }}" alt="MyBookHub Logo" class="logo-img">
        </a>
        <h2 class="fw-bold h3 mb-2">Delivery Partner Registration</h2>
        <p class="text-muted">Fill out the form below to join our delivery network.</p>
    </div>

    <div class="form-section">
        @if (Session::has('success_message'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ Session::get('success_message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (Session::has('error_message'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ Session::get('error_message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('delivery_agent.register.submit') }}" method="POST">
            @csrf
            
            <!-- Personal Information -->
            <div class="section-title">
                <i class="fas fa-user"></i> Personal Details
            </div>
            <div class="row g-4 mb-5">
                <div class="col-md-12">
                    <label class="form-label">Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user-edit"></i></span>
                        <input type="text" name="name" class="form-control" placeholder="Enter your full name" value="{{ old('name') }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="example@mail.com" value="{{ old('email') }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Mobile Number</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input type="text" name="phone" class="form-control" placeholder="10-digit mobile number" value="{{ old('phone') }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Create a strong password" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-shield-alt"></i></span>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat your password" required>
                    </div>
                </div>
            </div>

            <!-- Location Information (AJAX) -->
            <div class="section-title">
                <i class="fas fa-map-marker-alt"></i> Location Details
            </div>
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <label class="form-label">Country</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-globe"></i></span>
                        <select name="country_id" id="country_id" class="form-select" required>
                            <option value="">Select Country</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">State <span class="spinner-border spinner-border-sm text-primary" id="state_loader"></span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-map"></i></span>
                        <select name="state_id" id="state_id" class="form-select" required disabled>
                            <option value="">Select State</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">District <span class="spinner-border spinner-border-sm text-primary" id="district_loader"></span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-city"></i></span>
                        <select name="district_id" id="district_id" class="form-select" required disabled>
                            <option value="">Select District</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Block (Optional) <span class="spinner-border spinner-border-sm text-primary" id="block_loader"></span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-th-large"></i></span>
                        <select name="block_id" id="block_id" class="form-select" disabled>
                            <option value="">Select Block</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Vehicle Details -->
            <div class="section-title">
                <i class="fas fa-motorcycle"></i> Vehicle & Identification
            </div>
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Vehicle Type</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-truck-pickup"></i></span>
                        <input type="text" name="vehicle_type" class="form-control" placeholder="e.g. Bike, Electric Scooter" value="{{ old('vehicle_type') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Driving License Number</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                        <input type="text" name="license_number" class="form-control" placeholder="Enter DL Number" value="{{ old('license_number') }}">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-register">
                Register as Delivery Partner <i class="fas fa-arrow-right ms-2"></i>
            </button>

            <div class="footer-link">
                Already registered? <a href="{{ url('admin/login') }}">Login to Portal</a>
            </div>
        </form>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Country Change
        $('#country_id').change(function() {
            let countryId = $(this).val();
            $('#state_id').html('<option value="">Select State</option>').prop('disabled', true);
            $('#district_id').html('<option value="">Select District</option>').prop('disabled', true);
            $('#block_id').html('<option value="">Select Block</option>').prop('disabled', true);

            if (countryId) {
                $('#state_loader').show();
                $.ajax({
                    url: "{{ route('delivery_agent.get_states') }}",
                    type: "GET",
                    data: { country_id: countryId },
                    success: function(data) {
                        $('#state_loader').hide();
                        $('#state_id').prop('disabled', false);
                        $.each(data, function(key, state) {
                            $('#state_id').append('<option value="' + state.id + '">' + state.name + '</option>');
                        });
                    }
                });
            }
        });

        // State Change
        $('#state_id').change(function() {
            let stateId = $(this).val();
            $('#district_id').html('<option value="">Select District</option>').prop('disabled', true);
            $('#block_id').html('<option value="">Select Block</option>').prop('disabled', true);

            if (stateId) {
                $('#district_loader').show();
                $.ajax({
                    url: "{{ route('delivery_agent.get_districts') }}",
                    type: "GET",
                    data: { state_id: stateId },
                    success: function(data) {
                        $('#district_loader').hide();
                        $('#district_id').prop('disabled', false);
                        $.each(data, function(key, district) {
                            $('#district_id').append('<option value="' + district.id + '">' + district.name + '</option>');
                        });
                    }
                });
            }
        });

        // District Change
        $('#district_id').change(function() {
            let districtId = $(this).val();
            $('#block_id').html('<option value="">Select Block</option>').prop('disabled', true);

            if (districtId) {
                $('#block_loader').show();
                $.ajax({
                    url: "{{ route('delivery_agent.get_blocks') }}",
                    type: "GET",
                    data: { district_id: districtId },
                    success: function(data) {
                        $('#block_loader').hide();
                        $('#block_id').prop('disabled', false);
                        $.each(data, function(key, block) {
                            $('#block_id').append('<option value="' + block.id + '">' + block.name + '</option>');
                        });
                    }
                });
            }
        });
    });
</script>

</body>
</html>
