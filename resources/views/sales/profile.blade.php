@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="card shadow-lg border-0">
                <div class="row g-0">
                    <!-- Navigation Sidebar -->
                    <div class="col-lg-3 border-end">
                        <nav class="p-4">
                            <ul class="nav flex-column nav-pills" id="sales-account-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a href="#dashboard" class="nav-link active" id="dashboard-tab"
                                       data-bs-toggle="tab" role="tab" aria-controls="dashboard"
                                       aria-selected="true">Dashboard</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a href="#profile-details" class="nav-link" id="profile-details-tab"
                                       data-bs-toggle="tab" role="tab" aria-controls="profile-details"
                                       aria-selected="false">Profile Details</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a href="#bank-details" class="nav-link" id="bank-details-tab"
                                       data-bs-toggle="tab" role="tab" aria-controls="bank-details"
                                       aria-selected="false">Bank Details</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a href="#change-password" class="nav-link" id="change-password-tab"
                                       data-bs-toggle="tab" role="tab" aria-controls="change-password"
                                       aria-selected="false">Change Password</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a href="{{ route('sales.logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('sales-logout-form').submit();"
                                       class="nav-link">Logout</a>
                                    <form id="sales-logout-form" action="{{ route('sales.logout') }}" method="POST" style="display:none;">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </nav>
                    </div>

                    <!-- Content Area -->
                    <div class="col-lg-9">
                        <div class="p-4 p-md-5">
                            <div class="tab-content" id="sales-account-tabs-content">
                                <!-- Dashboard Tab -->
                                <div class="tab-pane fade show active" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
                                    <div class="mb-4 text-center">
                                        <h2 class="text-primary fw-bold mb-1">My Profile</h2>
                                        <p class="text-muted mb-0 fs-6">Welcome back, <strong>{{ $sales->name }}</strong>.</p>
                                    </div>

                                    @if(session('success_message'))
                                        <div class="alert alert-success text-center py-2 mb-4">{{ session('success_message') }}</div>
                                    @endif

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="p-3 border rounded bg-light h-100">
                                                <div class="small text-muted">Email</div>
                                                <div class="fw-semibold">{{ $sales->email }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="p-3 border rounded bg-light h-100">
                                                <div class="small text-muted">Phone</div>
                                                <div class="fw-semibold">{{ $sales->phone ?: 'Not provided' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="p-3 border rounded bg-light h-100">
                                                <div class="small text-muted">Address</div>
                                                <div class="fw-semibold">{{ $sales->address ?: 'Not provided' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Profile Details Tab -->
                                <div class="tab-pane fade" id="profile-details" role="tabpanel" aria-labelledby="profile-details-tab">
                                    @if ($errors->any())
                                        <div class="alert alert-danger py-2 mb-4">
                                            <ul class="mb-0 ps-3">
                                                @foreach ($errors->all() as $error)
                                                    <li class="small">{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <form class="" method="POST" action="{{ route('sales.profile.update') }}" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row g-3 mb-2">
                                            <h5 class="text-primary mb-3 fw-bold">Profile Details</h5>
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Profile Picture</label>
                                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                                    <div class="rounded-circle border overflow-hidden" style="width: 72px; height: 72px;">
                                                        @php
                                                            $profilePicturePath = $sales->profile_picture ? asset($sales->profile_picture) : asset('assets/images/avatar.png');
                                                        @endphp
                                                        <img
                                                            id="profile-picture-preview"
                                                            src="{{ $profilePicturePath }}"
                                                            data-default-src="{{ $profilePicturePath }}"
                                                            alt="Profile picture preview"
                                                            class="w-100 h-100 object-fit-cover">
                                                    </div>
                                                    <div class="flex-grow-1" style="min-width: 220px;">
                                                        <input type="file" name="profile_picture" id="profile-picture-input" class="form-control" accept="image/*">
                                                        <small class="text-muted">Supported formats: JPG, PNG, GIF up to 2MB.</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Name</label>
                                                <input type="text" name="name" class="form-control" value="{{ old('name', $sales->name) }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Email</label>
                                                <input type="email" name="email" class="form-control" value="{{ old('email', $sales->email) }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Phone</label>
                                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $sales->phone) }}">
                                            </div>

                                        </div>

                                        <div class="row g-3 mb-2">
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Address</label>
                                                <input type="text" name="address" class="form-control" value="{{ old('address', $sales->address) }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Country</label>
                                                <select class="form-control" id="sales_country_id" name="country_id" style="color: #495057">
                                                    <option value="">Select Country</option>
                                                    @foreach ($countries as $country)
                                                        <option value="{{ $country['id'] }}" @if (isset($currentCountryId) && $country['id'] == $currentCountryId) selected @endif>{{ $country['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">State</label>
                                                <select class="form-control" id="sales_state_id" name="state_id" style="color: #495057">
                                                    <option value="">Select State</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">District</label>
                                                <select class="form-control" id="sales_district_id" name="district_id" style="color: #495057">
                                                    <option value="">Select District</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Block</label>
                                                <select class="form-control" id="sales_block_id" name="block_id" style="color: #495057">
                                                    <option value="">Select Block</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Pincode</label>
                                                <input type="text" name="pincode" class="form-control" value="{{ old('pincode', $sales->pincode) }}">
                                            </div>
                                        </div>

                                        <div class="mt-4 text-end">
                                            <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Bank Details Tab -->
                                <div class="tab-pane fade" id="bank-details" role="tabpanel" aria-labelledby="bank-details-tab">
                                    <form class="" method="POST" action="{{ route('sales.profile.update') }}" style="max-width: 520px;">
                                        @csrf
                                        <div class="row g-3 mb-2">
                                            <h5 class="text-primary mb-3 fw-bold">Bank Details</h5>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Bank Name</label>
                                                        <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $sales->bank_name) }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Branch</label>
                                                        <input type="text" name="bank_branch" class="form-control" value="{{ old('bank_branch', $sales->bank_branch) }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Account Number</label>
                                                        <input type="text" name="account_number" class="form-control" value="{{ old('account_number', $sales->account_number) }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">IFSC Code</label>
                                                        <input type="text" name="ifsc_code" class="form-control" value="{{ old('ifsc_code', $sales->ifsc_code) }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">UPI ID</label>
                                                        <input type="text" name="upi_id" class="form-control" value="{{ old('upi_id', $sales->upi_id) }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-4 text-end">
                                                <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                                            </div>
                                    </form>
                                </div>

                                <!-- Change Password Tab -->
                                <div class="tab-pane fade" id="change-password" role="tabpanel" aria-labelledby="change-password-tab">
                                    <form class="" method="POST" action="{{ route('sales.profile.update') }}" style="max-width: 520px;">
                                        @csrf
                                        <!-- Preserve required fields for validation -->
                                        <input type="hidden" name="name" value="{{ $sales->name }}">
                                        <input type="hidden" name="email" value="{{ $sales->email }}">

                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">New Password</label>
                                                <input type="password" name="password" class="form-control" autocomplete="new-password" required>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Confirm Password</label>
                                                <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password" required>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <button type="submit" class="btn btn-primary">Update Password</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            $('#sales-account-tabs .nav-link').removeClass('active');
            $(e.target).addClass('active');
        });

        const profileInput = document.getElementById('profile-picture-input');
        const profilePreview = document.getElementById('profile-picture-preview');
        if (profileInput && profilePreview) {
            profileInput.addEventListener('change', function(event) {
                const [file] = event.target.files || [];
                if (!file) {
                    profilePreview.src = profilePreview.dataset.defaultSrc || profilePreview.src;
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePreview.src = e.target?.result || profilePreview.src;
                };
                reader.readAsDataURL(file);
            });
        }

        // Cascading location dropdowns
        var currentCountryId = {{ $currentCountryId ?? 'null' }};
        var currentStateId = {{ $currentStateId ?? 'null' }};
        var currentDistrictId = {{ $currentDistrictId ?? 'null' }};
        var currentBlockId = {{ $currentBlockId ?? 'null' }};

        // Load states based on country
        function loadSalesStates(countryId) {
            if (!countryId) {
                $('#sales_state_id').empty().append('<option value="">Select State</option>');
                $('#sales_district_id').empty().append('<option value="">Select District</option>');
                $('#sales_block_id').empty().append('<option value="">Select Block</option>');
                return Promise.resolve();
            }

            return new Promise(function(resolve, reject) {
                $.ajax({
                    url: '{{ route('sales.profile.states') }}',
                    type: 'GET',
                    data: { country: countryId },
                    dataType: 'json',
                    success: function(response) {
                        var stateSelect = $('#sales_state_id');
                        stateSelect.empty();
                        stateSelect.append('<option value="">Select State</option>');

                        $.each(response, function(key, value) {
                            stateSelect.append('<option value="' + key + '">' + value + '</option>');
                        });

                        // Clear dependent dropdowns
                        $('#sales_district_id').empty().append('<option value="">Select District</option>');
                        $('#sales_block_id').empty().append('<option value="">Select Block</option>');

                        // Set current value if exists
                        if (currentStateId && currentStateId !== null) {
                            stateSelect.val(currentStateId);
                            loadSalesDistricts(currentStateId).then(resolve);
                        } else {
                            resolve();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('Error loading states:', error);
                        reject(error);
                    }
                });
            });
        }

        // Load districts based on state
        function loadSalesDistricts(stateId) {
            if (!stateId) {
                $('#sales_district_id').empty().append('<option value="">Select District</option>');
                $('#sales_block_id').empty().append('<option value="">Select Block</option>');
                return Promise.resolve();
            }

            return new Promise(function(resolve, reject) {
                $.ajax({
                    url: '{{ route('sales.profile.districts') }}',
                    type: 'GET',
                    data: { state: stateId },
                    dataType: 'json',
                    success: function(response) {
                        var districtSelect = $('#sales_district_id');
                        districtSelect.empty();
                        districtSelect.append('<option value="">Select District</option>');

                        $.each(response, function(key, value) {
                            districtSelect.append('<option value="' + key + '">' + value + '</option>');
                        });

                        // Clear dependent dropdowns
                        $('#sales_block_id').empty().append('<option value="">Select Block</option>');

                        // Set current value if exists
                        if (currentDistrictId && currentDistrictId !== null) {
                            districtSelect.val(currentDistrictId);
                            loadSalesBlocks(currentDistrictId).then(resolve);
                        } else {
                            resolve();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('Error loading districts:', error);
                        reject(error);
                    }
                });
            });
        }

        // Load blocks based on district
        function loadSalesBlocks(districtId) {
            if (!districtId) {
                $('#sales_block_id').empty().append('<option value="">Select Block</option>');
                return Promise.resolve();
            }

            return new Promise(function(resolve, reject) {
                $.ajax({
                    url: '{{ route('sales.profile.blocks') }}',
                    type: 'GET',
                    data: { district: districtId },
                    dataType: 'json',
                    success: function(response) {
                        var blockSelect = $('#sales_block_id');
                        blockSelect.empty();
                        blockSelect.append('<option value="">Select Block</option>');

                        $.each(response, function(key, value) {
                            blockSelect.append('<option value="' + key + '">' + value + '</option>');
                        });

                        // Set current value if exists
                        if (currentBlockId && currentBlockId !== null) {
                            blockSelect.val(currentBlockId);
                        }

                        resolve();
                    },
                    error: function(xhr, status, error) {
                        console.log('Error loading blocks:', error);
                        reject(error);
                    }
                });
            });
        }

        // Event handlers for cascading dropdowns
        $('#sales_country_id').on('change', function() {
            var countryId = $(this).val();
            currentStateId = null;
            currentDistrictId = null;
            currentBlockId = null;
            loadSalesStates(countryId);
        });

        $('#sales_state_id').on('change', function() {
            var stateId = $(this).val();
            currentDistrictId = null;
            currentBlockId = null;
            loadSalesDistricts(stateId);
        });

        $('#sales_district_id').on('change', function() {
            var districtId = $(this).val();
            currentBlockId = null;
            loadSalesBlocks(districtId);
        });

        // Initialize form with current values
        if (currentCountryId && currentCountryId !== null) {
            $('#sales_country_id').val(currentCountryId);
            loadSalesStates(currentCountryId);
        }
    });
    </script>
@endsection
