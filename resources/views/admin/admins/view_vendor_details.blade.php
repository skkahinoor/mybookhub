@extends('admin.layout.layout')


@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-bold">Vendor Details</h3>
                            <h6 class="font-weight-normal mb-0"><a href="{{ url('admin/admins/vendor') }}">Back to Vendors</a>
                            </h6>
                        </div>
                        <div class="col-12 col-xl-4">
                            <div class="justify-content-end d-flex">
                                <div class="dropdown flex-md-grow-1 flex-xl-grow-0">
                                    <button class="btn btn-sm btn-light bg-white dropdown-toggle" type="button"
                                        id="dropdownMenuDate2" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="true">
                                        <i class="mdi mdi-calendar"></i> Today (10 Jan 2021)
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuDate2">
                                        <a class="dropdown-item" href="#">January - March</a>
                                        <a class="dropdown-item" href="#">March - June</a>
                                        <a class="dropdown-item" href="#">June - August</a>
                                        <a class="dropdown-item" href="#">August - November</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Personal Information</h4>
                            <div class="form-group">
                                <label>Email</label>
                                <input class="form-control" value="{{ $vendorDetails['vendor_personal']['email'] ?? '' }}"
                                    readonly> <!-- Check updateAdminPassword() method in AdminController.php -->
                            </div>
                            <div class="form-group">
                                <label for="vendor_name">Name</label>
                                <input type="text" class="form-control"
                                    value="{{ $vendorDetails['vendor_personal']['name'] ?? '' }}" readonly>
                                {{-- $vendorDetails was passed from AdminController --}}
                            </div>
                            <div class="form-group">
                                <label for="vendor_address">Address</label>
                                <input type="text" class="form-control"
                                    value="{{ $vendorDetails['vendor_personal']['address'] ?? '' }}" readonly>
                                {{-- $vendorDetails was passed from AdminController --}}
                            </div>
                            <div class="form-group">
                                <label for="vendor_country">Country</label>
                                <select class="form-control" id="vendor_country_id" style="color: #495057" disabled>
                                    <option value="">Select Country</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country['id'] }}" @if (isset($currentCountryId) && $country['id'] == $currentCountryId) selected @endif>{{ $country['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="vendor_state">State</label>
                                <select class="form-control" id="vendor_state_id" style="color: #495057" disabled>
                                    <option value="">Select State</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="vendor_district">District</label>
                                <select class="form-control" id="vendor_district_id" style="color: #495057" disabled>
                                    <option value="">Select District</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="vendor_block">Block</label>
                                <select class="form-control" id="vendor_block_id" style="color: #495057" disabled>
                                    <option value="">Select Block</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="vendor_pincode">Pincode</label>
                                <input type="text" class="form-control"
                                    value="{{ $vendorDetails['vendor_personal']['pincode'] ?? '' }}" readonly>
                                {{-- $vendorDetails was passed from AdminController --}}
                            </div>
                            <div class="form-group">
                                <label for="vendor_mobile">Mobile</label>
                                <input type="text" class="form-control"
                                    value="{{ $vendorDetails['vendor_personal']['mobile'] ?? '' }}" readonly>
                            </div>
                            @if (!empty($vendorDetails['image']))
                                <div class="form-group">
                                    <label for="vendor_image">Vendor Photo</label>
                                    <br>
                                    <img style="width: 200px"
                                        src="{{ url('admin/images/photos/' . $vendorDetails['image']) }}">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Business Information</h4>
                            <div class="form-group">
                                <label for="vendor_name">Shop Name</label>
                                <input type="text" class="form-control"
                                    value="{{ $vendorDetails['vendor_business']['shop_name'] ?? '' }}" readonly>
                                {{-- $vendorDetails was passed from AdminController --}}
                            </div>
                            <div class="form-group">
                                <label for="vendor_address">Shop Address</label>
                                <input type="text" class="form-control"
                                    value="{{ $vendorDetails['vendor_business']['shop_address'] ?? '' }}" readonly>
                                {{-- $vendorDetails was passed from AdminController --}}
                            </div>
                            <div class="form-group">
                                <label for="vendor_pincode">Shop Pincode</label>
                                <input type="text" class="form-control"
                                    value="{{ $vendorDetails['vendor_business']['shop_pincode'] ?? '' }}" readonly>
                                {{-- $vendorDetails was passed from AdminController --}}
                            </div>
                            <div class="form-group">
                                <label for="vendor_mobile">Shop Mobile</label>
                                <input type="text" class="form-control"
                                    value="{{ $vendorDetails['vendor_business']['shop_mobile'] ?? '' }}" readonly>
                            </div>
                            <div class="form-group">
                                <label for="vendor_mobile">Shop Website</label>
                                <input type="text" class="form-control"
                                    value="{{ $vendorDetails['vendor_business']['shop_website'] ?? '' }}" readonly>
                            </div>
                            {{-- <div class="form-group">
                                <label>Shop Email</label>
                                <input class="form-control"
                                    value="{{ $vendorDetails['vendor_business']['shop_email'] ?? '' }}" readonly>
                                
                            </div> --}}
                            <div class="form-group">
                                <label>Business License Number</label>
                                <input class="form-control"
                                    value="{{ $vendorDetails['vendor_business']['business_license_number'] ?? '' }}"
                                    readonly> <!-- Check updateAdminPassword() method in AdminController.php -->
                            </div>
                            <div class="form-group">
                                <label>GST Number</label>
                                <input class="form-control"
                                    value="{{ $vendorDetails['vendor_business']['gst_number'] ?? '' }}" readonly>
                                <!-- Check updateAdminPassword() method in AdminController.php -->
                            </div>
                            <div class="form-group">
                                <label>PAN Number</label>
                                <input class="form-control"
                                    value="{{ $vendorDetails['vendor_business']['pan_number'] ?? '' }}" readonly>
                                <!-- Check updateAdminPassword() method in AdminController.php -->
                            </div>
                            <div class="form-group">
                                <label>Address Proof</label>
                                <input class="form-control"
                                    value="{{ $vendorDetails['vendor_business']['address_proof'] ?? '' }}" readonly>
                                <!-- Check updateAdminPassword() method in AdminController.php -->
                            </div>
                            @if (!empty($vendorDetails['vendor_business']['address_proof_image'] ?? null))
                                <div class="form-group">
                                    <label for="vendor_image">Address Proof Image</label>
                                    <br>
                                    <img style="width: 200px"
                                        src="{{ url('admin/images/proofs/' . $vendorDetails['vendor_business']['address_proof_image']) }}">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Bank Information</h4>
                            <div class="form-group">
                                <label for="vendor_name">Account Holder Name</label>
                                <input type="text" class="form-control"
                                    value="{{ $vendorDetails['vendor_bank']['account_holder_name'] ?? '' }}" readonly>
                                {{-- $vendorDetails was passed from AdminController --}}
                            </div>
                            <div class="form-group">
                                <label for="vendor_name">Bank Name</label>
                                <input type="text" class="form-control"
                                    value="{{ $vendorDetails['vendor_bank']['bank_name'] ?? '' }}" readonly>
                                {{-- $vendorDetails was passed from AdminController --}}
                            </div>
                            <div class="form-group">
                                <label for="vendor_address">Account Number</label>
                                <input type="text" class="form-control"
                                    value="{{ $vendorDetails['vendor_bank']['account_number'] ?? '' }}" readonly>
                                {{-- $vendorDetails was passed from AdminController --}}
                            </div>
                            <div class="form-group">
                                <label for="vendor_city">Bank IFSC Code</label>
                                <input type="text" class="form-control"
                                    value="{{ $vendorDetails['vendor_bank']['bank_ifsc_code'] ?? '' }}" readonly>
                                {{-- $vendorDetails was passed from AdminController --}}
                            </div>
                        </div>
                    </div>
                </div>


                {{-- Commissions module: Every vendor must pay a certain commission (that may vary from a vendor to another) for the website owner (admin) on every item sold, and it's defined by the website owner (admin) --}}
                {{-- <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Commission Information</h4>

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
                                <!-- Check AdminController.php, updateAdminPassword() method -->
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success:</strong> {{ Session::get('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="form-group">
                                <label for="vendor_name">Commission per order item (%)</label>
                                <form method="post" action="{{ url('admin/update-vendor-commission') }}">
                                    @csrf
                                    <input type="hidden" name="vendor_id"
                                        value="{{ $vendorDetails['vendor_personal']['id'] ?? '' }}">
                                    <input class="form-control" type="text" name="commission"
                                        value="{{ $vendorDetails['vendor_personal']['commission'] ?? '' }}" required>
                                    <br>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div> --}}


            </div>
        </div>
        <!-- content-wrapper ends -->
        @include('admin.layout.footer')
        <!-- partial -->
    </div>

    @if (isset($vendorDetails['vendor_personal']) && !empty($vendorDetails['vendor_personal']))
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Store current values
            var currentCountryId = {{ $currentCountryId ?? 'null' }};
            var currentStateId = {{ $currentStateId ?? 'null' }};
            var currentDistrictId = {{ $currentDistrictId ?? 'null' }};
            var currentBlockId = {{ $currentBlockId ?? 'null' }};

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