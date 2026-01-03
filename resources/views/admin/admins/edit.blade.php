@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                             <h3 class="font-weight-bold">{{ isset($admin['type']) && $admin['type'] == 'vendor' ? 'Edit Vendor Details' : 'Edit Admin' }}</h3>
                            <h6 class="font-weight-normal mb-0"><a href="{{ url('admin/admins') }}">Back to Admins</a></h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                             <h4 class="card-title">{{ isset($admin['type']) && $admin['type'] == 'vendor' ? 'Personal Information' : 'Admin Information' }}</h4>

                            {{-- Error Messages --}}
                            @if (Session::has('error_message'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Error:</strong> {{ Session::get('error_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if (Session::has('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success:</strong> {{ Session::get('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <form method="POST" action="{{ url('admin/add-edit-admin') }}" enctype="multipart/form-data" id="admin-edit-form">
                                @csrf
                                 <input type="hidden" name="admin_id" value="{{ $admin['id'] ?? '' }}">

                                <div class="form-group">
                                    <label>Name <span class="text-danger">*</span></label>
                                     <input type="text" name="name" class="form-control" value="{{ $admin['name'] ?? '' }}" required>
                                </div>

                                <div class="form-group">
                                    <label>Email <span class="text-danger">*</span></label>
                                     <input type="email" name="email" class="form-control" value="{{ $admin['email'] ?? '' }}" required>
                                </div>

                                <div class="form-group">
                                    <label>Mobile <span class="text-danger">*</span></label>
                                     <input type="text" name="mobile" class="form-control" value="{{ $admin['mobile'] ?? '' }}" required>
                                </div>

                                {{-- <div class="form-group">
                                    <label>Type <span class="text-danger">*</span></label>
                                    <input type="text" name="type" class="form-control" value="{{ $admin['type'] }}" readonly>
                                </div> --}}

                                <div class="form-group">
                                    <label>Current Image</label>
                                    <br>
                                    @if (!empty($admin['image']))
                                        <img style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;"
                                             src="{{ url('admin/images/photos/' . $admin['image']) }}" alt="Admin Photo">
                                    @else
                                        <img style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;"
                                             src="{{ url('admin/images/photos/no-image.gif') }}" alt="No Image">
                                    @endif
                                     <input type="hidden" name="current_admin_image" value="{{ $admin['image'] ?? '' }}">
                                </div>

                                <div class="form-group">
                                    <label>New Photo</label>
                                    <input type="file" name="admin_image" class="form-control">
                                    <small class="text-muted">Leave blank if you don't want to change the photo</small>
                                </div>

                                @if (isset($vendorPersonal) && !empty($vendorPersonal) && isset($admin['type']) && $admin['type'] == 'vendor')
                                    {{-- Vendor Location Fields --}}
                                    <div class="form-group">
                                        <label>Address</label>
                                        <input type="text" name="vendor_address" class="form-control" value="{{ $vendorPersonal['address'] ?? '' }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Country</label>
                                        <select class="form-control" id="vendor_country_id" name="vendor_country_id" style="color: #495057">
                                            <option value="">Select Country</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country['id'] }}" @if (isset($vendorPersonal['country_id']) && $country['id'] == $vendorPersonal['country_id']) selected @endif>{{ $country['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>State</label>
                                        <select class="form-control" id="vendor_state_id" name="vendor_state_id" style="color: #495057">
                                            <option value="">Select State</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>District</label>
                                        <select class="form-control" id="vendor_district_id" name="vendor_district_id" style="color: #495057">
                                            <option value="">Select District</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Block</label>
                                        <select class="form-control" id="vendor_block_id" name="vendor_block_id" style="color: #495057">
                                            <option value="">Select Block</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Pincode</label>
                                        <input type="text" name="vendor_pincode" class="form-control" value="{{ $vendorPersonal['pincode'] ?? '' }}">
                                    </div>
                                @endif

                                <button type="submit" class="btn btn-primary">Update Admin</button>
                            </form>
                        </div>
                    </div>
                </div>

                @if (isset($vendorPersonal) || isset($vendorBusiness) || isset($vendorBank))

                    {{-- Vendor Business Information --}}
                    @if (isset($vendorBusiness) && !empty($vendorBusiness))
                    <div class="col-md-6 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Business Information (Vendor)</h4>
                                <div class="form-group">
                                    <label>Shop Name</label>
                                    <input class="form-control" value="{{ $vendorBusiness['shop_name'] ?? '' }}" >
                                </div>
                                <div class="form-group">
                                    <label>Shop Address</label>
                                    <input class="form-control" value="{{ $vendorBusiness['shop_address'] ?? '' }}" >
                                </div>
                                <div class="form-group">
                                    <label>Shop Mobile</label>
                                    <input class="form-control" value="{{ $vendorBusiness['shop_mobile'] ?? '' }}" >
                                </div>
                                <div class="form-group">
                                    <label>Business License Number</label>
                                    <input class="form-control" value="{{ $vendorBusiness['business_license_number'] ?? '' }}" >
                                </div>
                                <div class="form-group">
                                    <label>GST Number</label>
                                    <input class="form-control" value="{{ $vendorBusiness['gst_number'] ?? '' }}" >
                                </div>
                                <div class="form-group">
                                    <label>PAN Number</label>
                                    <input class="form-control" value="{{ $vendorBusiness['pan_number'] ?? '' }}" >
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Vendor Bank Information --}}
                    @if (isset($vendorBank) && !empty($vendorBank))
                    <div class="col-md-6 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Bank Information (Vendor)</h4>
                                <div class="form-group">
                                    <label>Account Holder Name</label>
                                    <input class="form-control" value="{{ $vendorBank['account_holder_name'] ?? '' }}" >
                                </div>
                                <div class="form-group">
                                    <label>Bank Name</label>
                                    <input class="form-control" value="{{ $vendorBank['bank_name'] ?? '' }}" >
                                </div>
                                <div class="form-group">
                                    <label>Account Number</label>
                                    <input class="form-control" value="{{ $vendorBank['account_number'] ?? '' }}" >
                                </div>
                                <div class="form-group">
                                    <label>Bank IFSC Code</label>
                                    <input class="form-control" value="{{ $vendorBank['bank_ifsc_code'] ?? '' }}" >
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                @endif
            </div>
        </div>
        <!-- content-wrapper ends -->
        @include('admin.layout.footer')
        <!-- partial -->
    </div>

    @if (isset($vendorPersonal) && !empty($vendorPersonal))
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Store current values
            var currentCountryId = {{ $vendorPersonal['country_id'] ?? 'null' }};
            var currentStateId = {{ $vendorPersonal['state_id'] ?? 'null' }};
            var currentDistrictId = {{ $vendorPersonal['district_id'] ?? 'null' }};
            var currentBlockId = {{ $vendorPersonal['block_id'] ?? 'null' }};

            // Load states based on country
            function loadVendorStates(countryId) {
                if (!countryId) {
                    $('#vendor_state_id').empty().append('<option value="">Select State</option>');
                    $('#vendor_district_id').empty().append('<option value="">Select District</option>');
                    $('#vendor_block_id').empty().append('<option value="">Select Block</option>');
                    return Promise.resolve();
                }

                return new Promise(function(resolve, reject) {
                    $.ajax({
                        url: '{{ route('vendor_states') }}',
                        type: 'GET',
                        data: { country: countryId },
                        dataType: 'json',
                        success: function(response) {
                            var stateSelect = $('#vendor_state_id');
                            stateSelect.empty();
                            stateSelect.append('<option value="">Select State</option>');

                            $.each(response, function(key, value) {
                                stateSelect.append('<option value="' + key + '">' + value + '</option>');
                            });

                            // Clear dependent dropdowns
                            $('#vendor_district_id').empty().append('<option value="">Select District</option>');
                            $('#vendor_block_id').empty().append('<option value="">Select Block</option>');

                            // Set current value if exists
                            if (currentStateId && currentStateId !== null) {
                                stateSelect.val(currentStateId);
                                loadVendorDistricts(currentStateId).then(resolve);
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
            function loadVendorDistricts(stateId) {
                if (!stateId) {
                    $('#vendor_district_id').empty().append('<option value="">Select District</option>');
                    $('#vendor_block_id').empty().append('<option value="">Select Block</option>');
                    return Promise.resolve();
                }

                return new Promise(function(resolve, reject) {
                    $.ajax({
                        url: '{{ route('vendor_districts') }}',
                        type: 'GET',
                        data: { state: stateId },
                        dataType: 'json',
                        success: function(response) {
                            var districtSelect = $('#vendor_district_id');
                            districtSelect.empty();
                            districtSelect.append('<option value="">Select District</option>');

                            $.each(response, function(key, value) {
                                districtSelect.append('<option value="' + key + '">' + value + '</option>');
                            });

                            // Clear dependent dropdowns
                            $('#vendor_block_id').empty().append('<option value="">Select Block</option>');

                            // Set current value if exists
                            if (currentDistrictId && currentDistrictId !== null) {
                                districtSelect.val(currentDistrictId);
                                loadVendorBlocks(currentDistrictId).then(resolve);
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
            function loadVendorBlocks(districtId) {
                if (!districtId) {
                    $('#vendor_block_id').empty().append('<option value="">Select Block</option>');
                    return Promise.resolve();
                }

                return new Promise(function(resolve, reject) {
                    $.ajax({
                        url: '{{ route('vendor_blocks') }}',
                        type: 'GET',
                        data: { district: districtId },
                        dataType: 'json',
                        success: function(response) {
                            var blockSelect = $('#vendor_block_id');
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
            $('#vendor_country_id').on('change', function() {
                var countryId = $(this).val();
                currentStateId = null;
                currentDistrictId = null;
                currentBlockId = null;
                loadVendorStates(countryId);
            });

            $('#vendor_state_id').on('change', function() {
                var stateId = $(this).val();
                currentDistrictId = null;
                currentBlockId = null;
                loadVendorDistricts(stateId);
            });

            $('#vendor_district_id').on('change', function() {
                var districtId = $(this).val();
                currentBlockId = null;
                loadVendorBlocks(districtId);
            });

            // Initialize form with current values on page load
            if (currentCountryId && currentCountryId !== null) {
                $('#vendor_country_id').val(currentCountryId);
                loadVendorStates(currentCountryId);
            } else {
                // If country dropdown has a value, load states
                var selectedCountry = $('#vendor_country_id').val();
                if (selectedCountry) {
                    loadVendorStates(selectedCountry);
                }
            }
        });
    </script>
    @endif
@endsection

