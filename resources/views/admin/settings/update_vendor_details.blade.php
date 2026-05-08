@extends('admin.layout.layout')


@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-bold">Update Vendor Details</h3>

                        </div>
                        <div class="col-12 col-xl-4">
                            <div class="justify-content-end d-flex">
                                <div class="dropdown flex-md-grow-1 flex-xl-grow-0">
                                    <button class="btn btn-sm btn-light bg-white dropdown-toggle" type="button" id="dropdownMenuDate2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
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



            @if ($slug == 'personal') {{-- $slug was passed from AdminController to view (using compact() method) --}}
                <div class="row">
                    <div class="col-md-6 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Update Personal Information</h4>


                                {{-- Our Bootstrap error code in case of wrong current password or the new password and confirm password are not matching: --}}
                                {{-- Determining If An Item Exists In The Session (using has() method): https://laravel.com/docs/9.x/session#determining-if-an-item-exists-in-the-session --}}
                                @if (Session::has('error_message')) <!-- Check AdminController.php, updateAdminPassword() method -->
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Error:</strong> {{ Session::get('error_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                @endif



                                {{-- Displaying Laravel Validation Errors: https://laravel.com/docs/9.x/validation#quick-displaying-the-validation-errors --}}
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



                                {{-- Our Bootstrap success message in case of updating admin password is successful: --}}
                                {{-- Determining If An Item Exists In The Session (using has() method): https://laravel.com/docs/9.x/session#determining-if-an-item-exists-in-the-session --}}
                                @if (Session::has('success_message')) <!-- Check AdminController.php, updateAdminPassword() method -->
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <strong>Success:</strong> {{ Session::get('success_message') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif



                                <form class="forms-sample" action="{{ url('admin/update-vendor-details/personal') }}" method="post" enctype="multipart/form-data"> @csrf <!-- Using the enctype="multipart/form-data" to allow uploading files (images) -->
                                    <div class="form-group">
                                        <label>Vendor Username/Email</label>
                                        <input class="form-control" value="{{ Auth::guard('admin')->user()->email }}" readonly> <!-- Check updateAdminPassword() method in AdminController.php --> {{-- Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances --}}
                                    </div>
                                    <div class="form-group">
                                        <label for="vendor_name">Name</label>
                                        <input type="text" class="form-control" id="vendor_name" placeholder="Enter Name" name="vendor_name" value="{{ Auth::guard('admin')->user()->name }}"> {{-- $vendorDetails was passed from AdminController --}} {{-- Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances --}}
                                    </div>
                                    <div class="form-group">
                                        <label for="vendor_address">Address</label>
                                        <input type="text" class="form-control" id="vendor_address" placeholder="Enter Address" name="vendor_address" value="{{ Auth::guard('admin')->user()->address }}"> {{-- Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances --}}
                                    </div>

                                    <div class="form-group">
                                        <label for="vendor_country">Country</label>
                                        <select class="form-control" id="vendor_country_id" name="country_id" style="color: #495057">
                                            <option value="">Select Country</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country['id'] }}" @if (isset(Auth::guard('admin')->user()->country_id) && $country['id'] == Auth::guard('admin')->user()->country_id) selected @endif>{{ $country['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="vendor_state">State</label>
                                        <select class="form-control" id="vendor_state_id" name="state_id" style="color: #495057">
                                            <option value="">Select State</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="vendor_district">District</label>
                                        <select class="form-control" id="vendor_district_id" name="district_id" style="color: #495057">
                                            <option value="">Select District</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="vendor_block">Block</label>
                                        <select class="form-control" id="vendor_block_id" name="block_id" style="color: #495057">
                                            <option value="">Select Block</option>
                                        </select>
                                    </div>


                                    <div class="form-group">
                                        <label for="vendor_pincode">Pincode</label>
                                        <input type="text" class="form-control" id="vendor_pincode" placeholder="Enter Pincode" name="vendor_pincode" value="{{ Auth::guard('admin')->user()->pincode }}"> {{-- Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances --}}
                                    </div>
                                    <div class="form-group">
                                        <label for="vendor_mobile">Mobile</label>
                                        <input type="text" class="form-control" id="vendor_mobile" placeholder="Enter 10 Digit Mobile Number" name="vendor_mobile" value="{{ Auth::guard('admin')->user()->mobile }}" maxlength="10" minlength="10"> {{-- Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances --}}
                                    </div>
                                    <div class="form-group">
                                        <label for="vendor_image">Vendor Photo</label>
                                        <input type="file" class="form-control" id="vendor_image" name="vendor_image">
                                        {{-- Show the admin image if exists --}}
                                        @if (!empty(Auth::guard('admin')->user()->image)) {{-- Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances --}}
                                            <a target="_blank" href="{{ url('admin/images/photos/' . Auth::guard('admin')->user()->image) }}">View Image</a> <!-- We used    target="_blank"    to open the image in another separate page --> {{-- Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances --}}
                                            <input type="hidden" name="current_vendor_image" value="{{ Auth::guard('admin')->user()->image }}"> <!-- to send the current admin image url all the time with all the requests --> {{-- Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances --}}
                                        @endif
                                    </div>
                                    <button type="submit" class="btn btn-primary mr-2">Submit</button>
                                    <button type="reset"  class="btn btn-light">Cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif ($slug == 'business')
                <div class="row">
                    <div class="col-md-6 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Update Vendor Business Information</h4>


                                {{-- Our Bootstrap error code in case of wrong current password or the new password and confirm password are not matching: --}}
                                {{-- Determining If An Item Exists In The Session (using has() method): https://laravel.com/docs/9.x/session#determining-if-an-item-exists-in-the-session --}}
                                @if (Session::has('error_message')) <!-- Check AdminController.php, updateAdminPassword() method -->
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Error:</strong> {{ Session::get('error_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                @endif



                                {{-- Displaying Laravel Validation Errors: https://laravel.com/docs/9.x/validation#quick-displaying-the-validation-errors --}}
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



                                {{-- Our Bootstrap success message in case of updating admin password is successful: --}}

                                {{-- Determining If An Item Exists In The Session (using has() method): https://laravel.com/docs/9.x/session#determining-if-an-item-exists-in-the-session --}}
                                @if (Session::has('success_message')) <!-- Check AdminController.php, updateAdminPassword() method -->
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <strong>Success:</strong> {{ Session::get('success_message') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif



                                <form class="forms-sample" action="{{ url('admin/update-vendor-details/business') }}" method="post" enctype="multipart/form-data"> @csrf <!-- Using the enctype="multipart/form-data" to allow uploading files (images) -->
                                    <div class="form-group">
                                        <label>Vendor Username/Email</label>
                                        <input class="form-control" value="{{ Auth::guard('admin')->user()->email }}" readonly> <!-- Check updateAdminPassword() method in AdminController.php --> {{-- Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances --}}
                                    </div>
                                    <div class="form-group">
                                        <label for="shop_name">Shop Name</label>
                                        <input type="text" class="form-control" id="shop_name" placeholder="Enter Shop Name" name="shop_name"  @if (isset($vendorDetails['shop_name'])) value="{{ $vendorDetails['shop_name'] }}" @endif> {{-- $vendorDetails was passed from AdminController --}}
                                    </div>
                                    <div class="form-group">
                                        <label for="shop_address">Shop Address</label>
                                        <input type="text" class="form-control" id="shop_address" placeholder="Enter Shop Address" name="shop_address"  @if (isset($vendorDetails['shop_address'])) value="{{ $vendorDetails['shop_address'] }}" @endif> 
                                    </div>

                                    <div class="form-group">
                                        <label><strong>Search Business Location on Map</strong></label>
                                        <input type="text" id="location_search" class="form-control mb-3" placeholder="Search location (Type name, street, city...)">
                                        <div id="map" style="height: 300px; width: 100%; border-radius: 10px; margin-bottom: 10px; border: 1px solid #ddd;"></div>
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <label class="text-muted small">Latitude</label>
                                                <input type="text" class="form-control" id="latitude" name="latitude" placeholder="Latitude" value="{{ $vendorDetails['latitude'] ?? '' }}" readonly>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <label class="text-muted small">Longitude</label>
                                                <input type="text" class="form-control" id="longitude" name="longitude" placeholder="Longitude" value="{{ $vendorDetails['longitude'] ?? '' }}" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="shop_country">Shop Country</label>
                                        <select class="form-control" id="business_country_id" name="country_id" style="color: #495057">
                                            <option value="">Select Country</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country['id'] }}" @if (isset($vendorDetails['country_id']) && $vendorDetails['country_id'] == $country['id']) selected @endif>{{ $country['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="shop_state">Shop State</label>
                                        <select class="form-control" id="business_state_id" name="state_id" style="color: #495057">
                                            <option value="">Select State</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="shop_district">Shop District</label>
                                        <select class="form-control" id="business_district_id" name="district_id" style="color: #495057">
                                            <option value="">Select District</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="shop_block">Shop Block</label>
                                        <select class="form-control" id="business_block_id" name="block_id" style="color: #495057">
                                            <option value="">Select Block</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="shop_pincode">Shop Pincode</label>
                                        <input type="text" class="form-control" id="shop_pincode" placeholder="Enter Shop Pincode" name="shop_pincode"  @if (isset($vendorDetails['shop_pincode'])) value="{{ $vendorDetails['shop_pincode'] }}" @endif> 
                                    </div>
                                    <div class="form-group">
                                        <label for="shop_mobile">Shop Mobile</label>
                                        <input type="text" class="form-control" id="shop_mobile" placeholder="Enter 10 Digit Shop Mobile Number" name="shop_mobile"  @if (isset($vendorDetails['shop_mobile'])) value="{{ $vendorDetails['shop_mobile'] }}" @endif maxlength="10" minlength="10">
                                    </div>
                                    <div class="form-group">
                                        <label for="shop_mobile">Shop Website</label>
                                        <input type="text" class="form-control" id="shop_website" placeholder="Enter Shop Website" name="shop_website"  @if (isset($vendorDetails['shop_website'])) value="{{ $vendorDetails['shop_website'] }}" @endif>
                                    </div>
                                    <div class="form-group">
                                        <label for="business_license_number">Business License Number</label>
                                        <input type="text" class="form-control" id="business_license_number" placeholder="Enter Business License Number" name="business_license_number"  @if (isset($vendorDetails['business_license_number'])) value="{{ $vendorDetails['business_license_number'] }}" @endif> {{-- $vendorDetails was passed from AdminController --}}
                                    </div>
                                    <div class="form-group">
                                        <label for="gst_number">GST Number</label>
                                        <input type="text" class="form-control" id="gst_number" placeholder="Enter GST Number" name="gst_number"  @if (isset($vendorDetails['gst_number'])) value="{{ $vendorDetails['gst_number'] }}" @endif> {{-- $vendorDetails was passed from AdminController --}}
                                    </div>
                                    <div class="form-group">
                                        <label for="pan_number">PAN Number</label>
                                        <input type="text" class="form-control" id="pan_number" placeholder="Enter PAN Number" name="pan_number"  @if (isset($vendorDetails['pan_number'])) value="{{ $vendorDetails['pan_number'] }}" @endif> {{-- $vendorDetails was passed from AdminController --}}
                                    </div>
                                    <div class="form-group">
                                        <label for="address_proof">Shop Address Proof</label>
                                        <select class="form-control" name="address_proof" id="address_proof">
                                            <option value="Passport"        @if(isset($vendorDetails['address_proof']) && $vendorDetails['address_proof'] == 'Passport')        selected @endif>Passport</option>
                                            <option value="Voting Card"     @if(isset($vendorDetails['address_proof']) && $vendorDetails['address_proof'] == 'Voting Card')     selected @endif>Voting Card</option>
                                            <option value="PAN"             @if(isset($vendorDetails['address_proof']) && $vendorDetails['address_proof'] == 'PAN')             selected @endif>PAN</option>
                                            <option value="Driving License" @if(isset($vendorDetails['address_proof']) && $vendorDetails['address_proof'] == 'Driving License') selected @endif>Driving License</option>
                                            <option value="Aadhar card"     @if(isset($vendorDetails['address_proof']) && $vendorDetails['address_proof'] == 'Aadhar card')     selected @endif>Aadhar Card</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="address_proof_image">Address Proof Image</label>
                                        <input type="file" class="form-control" id="address_proof_image" name="address_proof_image">
                                        {{-- Show the admin image if exists --}}
                                        @if (!empty($vendorDetails['address_proof_image']))
                                            <a target="_blank" href="{{ url('admin/images/proofs/' . $vendorDetails['address_proof_image']) }}">View Image</a> <!-- We used    target="_blank"    to open the image in another separate page -->
                                            <input type="hidden" name="current_address_proof" value="{{ $vendorDetails['address_proof_image'] }}"> <!-- to send the current admin image url all the time with all the requests -->
                                        @endif
                                    </div>
                                    <button type="submit" class="btn btn-primary mr-2">Submit</button>
                                    <button type="reset"  class="btn btn-light">Cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif ($slug == 'bank')
                <div class="row">
                    <div class="col-md-6 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Update Vendor Bank Information</h4>


                                {{-- Our Bootstrap error code in case of wrong current password or the new password and confirm password are not matching: --}}
                                {{-- Determining If An Item Exists In The Session (using has() method): https://laravel.com/docs/9.x/session#determining-if-an-item-exists-in-the-session --}}
                                @if (Session::has('error_message')) <!-- Check AdminController.php, updateAdminPassword() method -->
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <strong>Error:</strong> {{ Session::get('error_message') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif



                                {{-- Displaying Laravel Validation Errors: https://laravel.com/docs/9.x/validation#quick-displaying-the-validation-errors --}}
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



                                {{-- Our Bootstrap success message in case of updating admin password is successful: --}}
                                {{-- Determining If An Item Exists In The Session (using has() method): https://laravel.com/docs/9.x/session#determining-if-an-item-exists-in-the-session --}}
                                @if (Session::has('success_message')) <!-- Check AdminController.php, updateAdminPassword() method -->
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <strong>Success:</strong> {{ Session::get('success_message') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif



                                <form class="forms-sample" action="{{ url('admin/update-vendor-details/bank') }}" method="post" enctype="multipart/form-data"> @csrf <!-- Using the enctype="multipart/form-data" to allow uploading files (images) -->
                                    <div class="form-group">
                                        <label>Vendor Username/Email</label>
                                        <input class="form-control" value="{{ Auth::guard('admin')->user()->email }}" readonly> <!-- Check updateAdminPassword() method in AdminController.php --> {{-- Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances --}}
                                    </div>
                                    <div class="form-group">
                                        <label for="account_holder_name">Account Holder Name</label>
                                        <input type="text" class="form-control" id="account_holder_name" placeholder="Enter Account Holder Name" name="account_holder_name"  @if (isset($vendorDetails['account_holder_name'])) value="{{ $vendorDetails['account_holder_name'] }}" @endif> {{-- $vendorDetails was passed from AdminController --}}
                                    </div>
                                    <div class="form-group">
                                        <label for="bank_name">Bank Name</label>
                                        <input type="text" class="form-control" id="bank_name" placeholder="Enter Bank Name" name="bank_name"  @if (isset($vendorDetails['bank_name'])) value="{{ $vendorDetails['bank_name'] }}" @endif> {{-- $vendorDetails was passed from AdminController --}}
                                    </div>
                                    <div class="form-group">
                                        <label for="account_number">Account Number</label>
                                        <input type="text" class="form-control" id="account_number" placeholder="Enter Account Number" name="account_number"  @if (isset($vendorDetails['account_number'])) value="{{ $vendorDetails['account_number'] }}" @endif> {{-- $vendorDetails was passed from AdminController --}}
                                    </div>
                                    <div class="form-group">
                                        <label for="bank_ifsc_code">Bank IFSC Code</label>
                                        <input type="text" class="form-control" id="bank_ifsc_code" placeholder="Enter Bank IFSC Code" name="bank_ifsc_code"  @if (isset($vendorDetails['bank_ifsc_code'])) value="{{ $vendorDetails['bank_ifsc_code'] }}" @endif> {{-- $vendorDetails was passed from AdminController --}}
                                    </div>
                                    <button type="submit" class="btn btn-primary mr-2">Submit</button>
                                    <button type="reset"  class="btn btn-light">Cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif



        </div>
        <!-- content-wrapper ends -->
        @include('admin.layout.footer')
        <!-- partial -->
    </div>

    @if ($slug == 'personal' || $slug == 'business')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&libraries=places"></script>
    <script>
        $(document).ready(function() {
            var slug = '{{ $slug }}';
            var prefix = slug === 'personal' ? 'vendor_' : 'business_';
            
            // Current values
            var currentCountryId, currentStateId, currentDistrictId, currentBlockId;
            
            if (slug === 'personal') {
                currentCountryId = @json(Auth::guard('admin')->user()->country_id ?? null);
                currentStateId = @json(Auth::guard('admin')->user()->state_id ?? null);
                currentDistrictId = @json(Auth::guard('admin')->user()->district_id ?? null);
                currentBlockId = @json(Auth::guard('admin')->user()->block_id ?? null);
            } else {
                currentCountryId = @json($vendorDetails['country_id'] ?? null);
                currentStateId = @json($vendorDetails['state_id'] ?? null);
                currentDistrictId = @json($vendorDetails['district_id'] ?? null);
                currentBlockId = @json($vendorDetails['block_id'] ?? null);
            }

            // Google Maps Implementation for Business
            if (slug === 'business') {
                var initialLat = parseFloat($('#latitude').val()) || 20.2961;
                var initialLng = parseFloat($('#longitude').val()) || 85.8245;
                var zoomLevel = $('#latitude').val() ? 15 : 5;

                var map = new google.maps.Map(document.getElementById('map'), {
                    center: {lat: initialLat, lng: initialLng},
                    zoom: zoomLevel
                });

                var marker = new google.maps.Marker({
                    position: {lat: initialLat, lng: initialLng},
                    map: map,
                    draggable: true,
                    title: "Drag to shop location"
                });

                // 🔍 Search autocomplete
                var autocomplete = new google.maps.places.Autocomplete(
                    document.getElementById("location_search")
                );
                autocomplete.bindTo("bounds", map);

                autocomplete.addListener("place_changed", function() {
                    var place = autocomplete.getPlace();
                    if (!place.geometry) return;

                    map.setCenter(place.geometry.location);
                    marker.setPosition(place.geometry.location);

                    $('#latitude').val(place.geometry.location.lat());
                    $('#longitude').val(place.geometry.location.lng());
                });

                // Update lat/lng on marker drag
                google.maps.event.addListener(marker, 'dragend', function(event) {
                    $('#latitude').val(event.latLng.lat());
                    $('#longitude').val(event.latLng.lng());
                });

                // If latitude/longitude is not set, try to auto-detect current location
                if (!$('#latitude').val() && navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            var lat = position.coords.latitude;
                            var lng = position.coords.longitude;
                            var currentPos = new google.maps.LatLng(lat, lng);

                            map.setCenter(currentPos);
                            marker.setPosition(currentPos);
                            map.setZoom(15);

                            $('#latitude').val(lat);
                            $('#longitude').val(lng);
                        },
                        function() {
                            console.warn("User denied location access");
                        }
                    );
                }
            }

            // AJAX Dropdowns
            function loadStates(countryId) {
                if (!countryId) return Promise.resolve();
                return new Promise(function(resolve) {
                    $.ajax({
                        url: '{{ route('vendor_states') }}',
                        type: 'GET',
                        data: { country: countryId },
                        success: function(response) {
                            var select = $('#' + prefix + 'state_id');
                            select.empty().append('<option value="">Select State</option>');
                            $.each(response, function(key, value) {
                                select.append('<option value="' + key + '">' + value + '</option>');
                            });
                            if (currentStateId) {
                                select.val(currentStateId);
                                loadDistricts(currentStateId).then(resolve);
                            } else resolve();
                        }
                    });
                });
            }

            function loadDistricts(stateId) {
                if (!stateId) return Promise.resolve();
                return new Promise(function(resolve) {
                    $.ajax({
                        url: '{{ route('vendor_districts') }}',
                        type: 'GET',
                        data: { state: stateId },
                        success: function(response) {
                            var select = $('#' + prefix + 'district_id');
                            select.empty().append('<option value="">Select District</option>');
                            $.each(response, function(key, value) {
                                select.append('<option value="' + key + '">' + value + '</option>');
                            });
                            if (currentDistrictId) {
                                select.val(currentDistrictId);
                                loadBlocks(currentDistrictId).then(resolve);
                            } else resolve();
                        }
                    });
                });
            }

            function loadBlocks(districtId) {
                if (!districtId) return Promise.resolve();
                return new Promise(function(resolve) {
                    $.ajax({
                        url: '{{ route('vendor_blocks') }}',
                        type: 'GET',
                        data: { district: districtId },
                        success: function(response) {
                            var select = $('#' + prefix + 'block_id');
                            select.empty().append('<option value="">Select Block</option>');
                            $.each(response, function(key, value) {
                                select.append('<option value="' + key + '">' + value + '</option>');
                            });
                            if (currentBlockId) select.val(currentBlockId);
                            resolve();
                        }
                    });
                });
            }

            // Event handlers
            $('#' + prefix + 'country_id').on('change', function() {
                currentStateId = null; currentDistrictId = null; currentBlockId = null;
                loadStates($(this).val());
            });

            $('#' + prefix + 'state_id').on('change', function() {
                currentDistrictId = null; currentBlockId = null;
                loadDistricts($(this).val());
            });

            $('#' + prefix + 'district_id').on('change', function() {
                currentBlockId = null;
                loadBlocks($(this).val());
            });

            // Initialize
            if (currentCountryId) {
                loadStates(currentCountryId);
            }
        });
    </script>
    @endif
@endsection
