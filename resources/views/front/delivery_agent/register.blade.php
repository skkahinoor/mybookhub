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
            --primary-color: #00a86b; /* Emerald Green matching App */
            --secondary-color: #6c757d;
            --accent-color: #ffc107;
            --bg-gradient: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            --card-bg: #ffffff;
            --text-main: #1a1a1a;
            --text-muted: #666666;
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
            border-radius: 32px; /* Smoother corners */
            box-shadow: 0 25px 50px -12px rgba(0, 168, 107, 0.15);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            border: 1px solid rgba(0, 168, 107, 0.1);
        }

        .card-header-section {
            background: #fff;
            padding: 50px 40px 30px;
            text-align: center;
        }

        .logo-img {
            max-width: 220px;
            margin-bottom: 30px;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.05));
        }

        .form-section {
            padding: 20px 50px 50px;
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
            border-radius: 16px;
            padding: 16px;
            font-weight: 700;
            font-size: 1.1rem;
            width: 100%;
            margin-top: 20px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 20px -5px rgba(0, 168, 107, 0.4);
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

        /* Multi-step styles */
        .step-content {
            display: none;
            animation: fadeIn 0.5s ease;
        }
        .step-content.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            padding: 0 40px;
            position: relative;
        }
        .step-indicator::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 50px;
            right: 50px;
            height: 2px;
            background: #e2e8f0;
            z-index: 0;
        }
        .step {
            position: relative;
            z-index: 1;
            text-align: center;
        }
        .step-icon {
            width: 42px;
            height: 42px;
            background: #fff;
            border: 2px solid #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            color: var(--secondary-color);
            transition: all 0.3s ease;
            font-weight: bold;
        }
        .step.active .step-icon {
            border-color: var(--primary-color);
            background: var(--primary-color);
            color: #fff;
            box-shadow: 0 0 0 4px rgba(0, 168, 107, 0.15);
        }
        .step.completed .step-icon {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: #fff;
        }
        .step-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-muted);
        }
        .step.active .step-label {
            color: var(--primary-color);
        }

        .step-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        .btn-prev {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: var(--text-main);
            padding: 14px 25px;
            border-radius: 12px;
            font-weight: 600;
        }
        .btn-next {
            flex: 1;
            background: var(--primary-color);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 700;
            box-shadow: 0 10px 15px -3px rgba(0, 168, 107, 0.2);
        }

        /* Loading Spinner */
        .spinner-border-sm {
            display: none;
            margin-left: 5px;
        }

        /* Responsiveness Fixes */
        @media (max-width: 768px) {
            body {
                padding: 15px 10px;
            }
            .registration-card {
                border-radius: 20px;
            }
            .card-header-section {
                padding: 30px 20px 20px;
            }
            .logo-img {
                max-width: 160px;
                margin-bottom: 20px;
            }
            .form-section {
                padding: 10px 20px 30px;
            }
            .step-indicator {
                padding: 0 10px;
                margin-bottom: 30px;
            }
            .step-indicator::before {
                left: 30px;
                right: 30px;
            }
            .step-icon {
                width: 35px;
                height: 35px;
                font-size: 0.9rem;
            }
            .step-label {
                font-size: 0.7rem;
            }
            .step-actions {
                flex-direction: column-reverse; /* Stack buttons on mobile */
                gap: 10px;
            }
            .btn-prev, .btn-next, .btn-register {
                width: 100%;
                padding: 12px;
                font-size: 1rem;
            }
            .section-title {
                font-size: 1.1rem;
                margin-bottom: 20px;
            }
        }

        /* Extra small devices */
        @media (max-width: 480px) {
            .step-label {
                display: none; /* Hide labels on very small screens to save space */
            }
            .step-indicator {
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>

<div class="registration-card">
    <div class="card-header-section">
        <a href="{{ url('/') }}">
            @if(!empty($logos->logo))
                <img src="{{ asset('uploads/logos/'.$logos->logo) }}" alt="MyBookHub Logo" class="logo-img">
            @else
                <img src="{{ asset('front/images/logos/logo.png') }}" alt="MyBookHub Logo" class="logo-img">
            @endif
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

        <div class="step-indicator">
            <div class="step active" id="step1-indicator">
                <div class="step-icon">1</div>
                <div class="step-label">Account</div>
            </div>
            <div class="step" id="step2-indicator">
                <div class="step-icon">2</div>
                <div class="step-label">Location</div>
            </div>
            <div class="step" id="step3-indicator">
                <div class="step-icon">3</div>
                <div class="step-label">Verify</div>
            </div>
        </div>

        <form action="{{ route('delivery_agent.register.submit') }}" method="POST" enctype="multipart/form-data" id="multi-step-form">
            @csrf
            
            <!-- Step 1: Personal Information -->
            <div class="step-content active" id="step-1">
                <div class="section-title">
                    <i class="fas fa-user"></i> Personal Details
                </div>
                <div class="row g-4">
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
                <div class="step-actions">
                    <button type="button" class="btn-next" onclick="nextStep(2)">Continue to Location <i class="fas fa-arrow-right ms-2"></i></button>
                </div>
            </div>

            <!-- Step 2: Location Information (AJAX) -->
            <div class="step-content" id="step-2">
                <div class="section-title">
                    <i class="fas fa-map-marker-alt"></i> Location Details
                </div>
                <div class="row g-4">
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
                <div class="step-actions">
                    <button type="button" class="btn-prev" onclick="nextStep(1)">Previous</button>
                    <button type="button" class="btn-next" onclick="nextStep(3)">Verification Proofs <i class="fas fa-arrow-right ms-2"></i></button>
                </div>
            </div>

            <!-- Step 3: Vehicle Details -->
            <div class="step-content" id="step-3">
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
                    <div class="col-md-6">
                        <label class="form-label">Upload ID Proof <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-file-upload"></i></span>
                            <input type="file" name="id_proof" class="form-control" accept="image/*" required>
                        </div>
                        <small class="text-muted" style="font-size: 0.75rem;">Aadhar/Voter ID (Max: 2MB)</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Upload License (Optional)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-image"></i></span>
                            <input type="file" name="license_image" class="form-control" accept="image/*">
                        </div>
                        <small class="text-muted" style="font-size: 0.75rem;">Front Copy (Max: 2MB)</small>
                    </div>
                </div>
                <div class="step-actions">
                    <button type="button" class="btn-prev" onclick="nextStep(2)">Previous</button>
                    <button type="submit" class="btn-register">
                        Complete Registration <i class="fas fa-check-circle ms-2"></i>
                    </button>
                </div>
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

    function nextStep(step) {
        // Simple validation for current step
        let currentStep = step === 2 ? 1 : (step === 3 ? 2 : 3);
        let isValid = true;
        
        // Basic required field check for current step
        $('#step-' + currentStep + ' [required]').each(function() {
            if (!$(this).val()) {
                $(this).closest('.input-group').css('border-color', '#dc3545');
                isValid = false;
            } else {
                $(this).closest('.input-group').css('border-color', '#e2e8f0');
            }
        });

        if (!isValid && step > currentStep) {
            alert('Please fill all required fields before continuing.');
            return;
        }

        // Hide all steps
        $('.step-content').removeClass('active');
        // Show target step
        $('#step-' + step).addClass('active');

        // Update indicators
        $('.step').removeClass('active completed');
        for (let i = 1; i <= step; i++) {
            if (i < step) {
                $('#step' + i + '-indicator').addClass('completed');
            }
            if (i === step) {
                $('#step' + i + '-indicator').addClass('active');
            }
        }
    }
</script>

</body>
</html>
