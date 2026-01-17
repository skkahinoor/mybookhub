@include('user.layout.header')

<div class="container-fluid page-body-wrapper">
    @include('user.layout.sidebar')

    <div class="main-panel">
        <div class="content-wrapper">

            <div class="row mb-4">
                <div class="col-12 d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="font-weight-bold mb-1">Account details</h3>
                        <p class="text-muted mb-0">Manage your personal information and contact details.</p>
                    </div>
                    <span class="badge badge-primary badge-pill px-3 py-2">
                        {{ Auth::user()->email ?? 'User' }}
                    </span>
                </div>
            </div>

            {{-- Alerts --}}
            <div class="row mb-3">
                <div class="col-lg-8">
                    @if (session('success_message'))
                        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                            <i class="mdi mdi-check-circle mr-2"></i>
                            {{ session('success_message') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                            <i class="mdi mdi-alert-circle-outline mr-2"></i>
                            <ul class="mb-0 pl-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <div class="row">
                {{-- Left: profile summary --}}
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                @php
                                    $avatar = optional(Auth::user())->profile_image ?? null;
                                    // Check if avatar exists and handle both new location (asset/user) and old location (storage)
                                    if ($avatar) {
                                        // Works for both asset/user/ and storage/ paths
                                        $avatarSrc = asset($avatar);
                                    } else {
                                        $avatarSrc = asset('user/images/faces/face28.jpg');
                                    }
                                @endphp
                                <img src="{{ $avatarSrc }}" id="user-avatar-preview" alt="profile"
                                    class="rounded-circle" style="width:72px;height:72px;object-fit:cover;">
                                <div class="mt-2">
                                    <button type="button" id="change-avatar-btn"
                                        class="btn btn-sm btn-outline-primary">
                                        Change photo
                                    </button>
                                    <input type="file" id="avatar-input" name="avatar" accept="image/*"
                                        class="d-none">
                                </div>
                            </div>
                            <h5 class="mb-1">{{ Auth::user()->name ?? 'User' }}</h5>
                            <p class="text-muted mb-3 small">{{ Auth::user()->email ?? 'No email' }}</p>

                            <div class="border-top pt-3 mt-3 text-left">
                                <p class="text-muted small mb-2">Profile completion</p>

                                <div class="progress mb-1" style="height: 6px;">
                                    <div id="profileProgressBar" class="progress-bar bg-success" role="progressbar"
                                        style="width: {{ $profileCompletion ?? 0 }}%;"
                                        aria-valuenow="{{ $profileCompletion ?? 0 }}" aria-valuemin="0"
                                        aria-valuemax="100">
                                    </div>
                                </div>

                                <span class="small text-muted">
                                    Approx. <span id="profilePercent">{{ $profileCompletion ?? 0 }}</span>% complete
                                </span>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Right: form --}}
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-0 pb-0">
                            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#tab-basic" role="tab">
                                        <i class="mdi mdi-account-outline mr-1"></i> Basic info
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab-address" role="tab">
                                        <i class="mdi mdi-map-marker-outline mr-1"></i> Address
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab-password" role="tab">
                                        <i class="mdi mdi-lock-outline mr-1"></i> Change password
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body">
                            {{-- Account details + password in a single tab-content --}}
                            <form id="accountForm" action="{{ route('user.account') }}" method="POST">
                                @csrf
                                <div class="tab-content">
                                    {{-- Basic info tab --}}
                                    <div class="tab-pane fade show active" id="tab-basic" role="tabpanel">
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="user-name">Full name <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="user-name" name="name"
                                                    placeholder="Enter your full name"
                                                    value="{{ Auth::user()->name ?? '' }}" required>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label for="user-email">Email address <span
                                                        class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="user-email"
                                                    name="email" placeholder="name@example.com"
                                                    value="{{ Auth::user()->email ?? '' }}" required>
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="user-mobile">Mobile number <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="user-mobile"
                                                    name="mobile" placeholder="Enter mobile number"
                                                    value="{{ Auth::user()->phone ?? '' }}" required>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label for="user-pincode">Pincode <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="user-pincode"
                                                    name="pincode" placeholder="e.g. 110001"
                                                    value="{{ Auth::user()->pincode ?? '' }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Address tab --}}
                                    <div class="tab-pane fade" id="tab-address" role="tabpanel">
                                        <div class="form-group">
                                            <label for="user-address">Address</label>
                                            <input type="text" class="form-control" id="user-address"
                                                name="address" placeholder="House / Street / Locality"
                                                value="{{ Auth::user()->address ?? '' }}">
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="user-country-id">Country</label>
                                                <select class="form-control" id="user-country-id" name="country_id">
                                                    <option value="">Select country</option>
                                                    @foreach ($countries as $country)
                                                        <option value="{{ $country->id }}"
                                                            {{ optional(Auth::user())->country_id == $country->id ? 'selected' : '' }}>
                                                            {{ $country->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label for="user-state-id">State</label>
                                                <select class="form-control" id="user-state-id" name="state_id">
                                                    <option value="">Select state</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="user-district-id">District</label>
                                                <select class="form-control" id="user-district-id"
                                                    name="district_id">
                                                    <option value="">Select district</option>
                                                </select>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label for="user-block-id">Block</label>
                                                <select class="form-control" id="user-block-id" name="block_id">
                                                    <option value="">Select block</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="alert alert-info small mt-3 mb-0">
                                            <i class="mdi mdi-information-outline mr-1"></i>
                                            Address changes help us provide more accurate delivery and service
                                            information.
                                        </div>
                                    </div>
                                    {{-- Change password tab (same form) --}}
                                    <div class="tab-pane fade" id="tab-password" role="tabpanel">
                                        <p class="text-muted small mb-3">
                                            Ensure your new password is at least 8 characters and uses a mix of letters,
                                            numbers and symbols.
                                        </p>

                                        <div class="form-group">
                                            <label for="current-password">Current password <span
                                                    class="text-danger">*</span></label>
                                            <input type="password" class="form-control" id="current-password"
                                                name="current_password" placeholder="Enter current password">
                                        </div>

                                        <div class="form-group">
                                            <label for="new-password">New password <span
                                                    class="text-danger">*</span></label>
                                            <input type="password" class="form-control" id="new-password"
                                                name="new_password" placeholder="Enter new password">
                                        </div>

                                        <div class="form-group">
                                            <label for="confirm-password">Confirm new password <span
                                                    class="text-danger">*</span></label>
                                            <input type="password" class="form-control" id="confirm-password"
                                                name="confirm_password" placeholder="Re‑enter new password">
                                        </div>
                                    </div>
                                </div> {{-- /tab-content (all tabs) --}}

                                <div class="d-flex justify-content-end border-top pt-3 mt-2">
                                    <button type="reset" class="btn btn-light mr-2">Cancel</button>
                                    <button type="button" id="saveProfileBtn" class="btn btn-primary">
                                        Save Changes
                                    </button>
                                </div>
                            </form>
                        </div> {{-- /card-body --}}
                    </div>

                </div> {{-- /col-lg-8 --}}
            </div> {{-- /row --}}
        </div> {{-- /content-wrapper --}}
    </div> {{-- /main-panel --}}
</div>

@include('user.layout.footer')

</div>
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
<!-- endinject -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#saveProfileBtn').on('click', function(e) {
        e.preventDefault();

        var form = $('#accountForm');
        // Serialize all form fields including those in hidden tabs
        var formData = form.serializeArray();
        // Also include any fields that might be in hidden tabs
        form.find('input, select, textarea').each(function() {
            var $field = $(this);
            var name = $field.attr('name');
            if (name && !$field.is(':disabled') && !formData.some(function(item) { return item.name === name; })) {
                formData.push({name: name, value: $field.val()});
            }
        });
        // Convert to URL-encoded string
        var formDataString = $.param(formData);

        var submitButton = $(this);
        var originalButtonText = submitButton.html();

        // Disable submit button and show loading state
        submitButton.prop('disabled', true).html(
            '<i class="mdi mdi-loading mdi-spin mr-1"></i> Saving...');

        // Remove previous alert messages
        $('.alert').remove();

        $.ajax({
            url: form.attr('action'),
            type: "POST",
            data: formDataString,
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
            },
            success: function(response) {
                // Show success message
                var successAlert =
                    '<div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="margin-top: 20px;">' +
                    '<i class="mdi mdi-check-circle mr-2"></i>' +
                    response.message +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                    '<span aria-hidden="true">&times;</span>' +
                    '</button>' +
                    '</div>';

                $('.row.mb-3 .col-lg-8').html(successAlert);

                // Update profile completion percentage if provided
                if (response.profileCompletion !== undefined) {
                    $('#profileProgressBar')
                        .css('width', response.profileCompletion + '%')
                        .attr('aria-valuenow', response.profileCompletion);
                    $('#profilePercent').text(response.profileCompletion);
                }

                // Scroll to top to show message
                $('html, body').animate({
                    scrollTop: 0
                }, 500);

                // Re-enable submit button
                submitButton.prop('disabled', false).html(originalButtonText);

                console.log('Account updated successfully:', response);
            },
            error: function(xhr, status, error) {
                console.error('Error updating account:', error);
                console.error('Response:', xhr.responseText);

                var errorMessage =
                    'An error occurred while updating your account. Please try again.';
                var errorsHtml = '';

                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    // Validation errors
                    var errors = xhr.responseJSON.errors;
                    errorsHtml = '<ul class="mb-0 pl-3">';
                    $.each(errors, function(field, messages) {
                        $.each(messages, function(index, message) {
                            errorsHtml += '<li>' + message + '</li>';
                        });
                    });
                    errorsHtml += '</ul>';
                    errorMessage = 'Please fix the following errors:';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                var errorAlert =
                    '<div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="margin-top: 20px;">' +
                    '<i class="mdi mdi-alert-circle-outline mr-2"></i>' +
                    errorMessage +
                    errorsHtml +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                    '<span aria-hidden="true">&times;</span>' +
                    '</button>' +
                    '</div>';

                $('.row.mb-3 .col-lg-8').html(errorAlert);

                // Scroll to top to show error
                $('html, body').animate({
                    scrollTop: 0
                }, 500);

                // Re-enable submit button
                submitButton.prop('disabled', false).html(originalButtonText);
            }
        });
    });
</script>

<script>
    // Wait for jQuery and DOM to be ready
    jQuery(document).ready(function($) {
        console.log('Account details page loaded');

        // Current user location values
        var currentCountryId = @json(Auth::user()->country_id ?? null);
        var currentStateId = @json(Auth::user()->state_id ?? null);
        var currentDistrictId = @json(Auth::user()->district_id ?? null);
        var currentBlockId = @json(Auth::user()->block_id ?? null);

        console.log('Current values:', {
            country: currentCountryId,
            state: currentStateId,
            district: currentDistrictId,
            block: currentBlockId
        });

        // Load states based on country
        function loadUserStates(countryId, preserveStateId) {
            if (!countryId) {
                $('#user-state-id').empty().append('<option value="">Select state</option>');
                $('#user-district-id').empty().append('<option value="">Select district</option>');
                $('#user-block-id').empty().append('<option value="">Select block</option>');
                return Promise.resolve();
            }

            return new Promise(function(resolve, reject) {
                $.ajax({
                    url: '{{ route('user_states') }}',
                    type: 'GET',
                    data: {
                        country: countryId
                    },
                    dataType: 'json',
                    xhrFields: {
                        withCredentials: true
                    },
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        console.log('States loaded:', response);
                        var stateSelect = $('#user-state-id');
                        stateSelect.empty();
                        stateSelect.append('<option value="">Select state</option>');

                        $.each(response, function(key, value) {
                            stateSelect.append('<option value="' + key + '">' +
                                value + '</option>');
                        });

                        // Clear dependent dropdowns only if not preserving state
                        if (!preserveStateId) {
                            $('#user-district-id').empty().append(
                                '<option value="">Select district</option>');
                            $('#user-block-id').empty().append(
                                '<option value="">Select block</option>');
                        }

                        // Set current value if exists
                        var stateIdToSet = preserveStateId ? preserveStateId :
                            currentStateId;
                        if (stateIdToSet && stateIdToSet !== null) {
                            stateSelect.val(stateIdToSet);
                            loadUserDistricts(stateIdToSet, preserveStateId ?
                                currentDistrictId : null).then(resolve);
                        } else {
                            resolve();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading states:', error);
                        console.error('Status:', status);
                        console.error('Status Code:', xhr.status);
                        console.error('Response:', xhr.responseText);
                        console.error('URL:', '{{ route('user_states') }}');
                        console.error('Request Data:', {
                            country: countryId
                        });

                        if (xhr.status === 404) {
                            alert(
                                'Route not found. Please check if the route is properly configured.');
                        } else if (xhr.status === 401 || xhr.status === 403) {
                            alert(
                                'Authentication required. Please refresh the page and try again.');
                        } else {
                            alert('Error loading states. Status: ' + xhr.status +
                                '. Please check console for details.');
                        }
                        reject(error);
                    }
                });
            });
        }

        // Load districts based on state
        function loadUserDistricts(stateId, preserveDistrictId) {
            if (!stateId) {
                $('#user-district-id').empty().append('<option value="">Select district</option>');
                $('#user-block-id').empty().append('<option value="">Select block</option>');
                return Promise.resolve();
            }

            return new Promise(function(resolve, reject) {
                $.ajax({
                    url: '{{ route('user_districts') }}',
                    type: 'GET',
                    data: {
                        state: stateId
                    },
                    dataType: 'json',
                    xhrFields: {
                        withCredentials: true
                    },
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        console.log('Districts loaded:', response);
                        var districtSelect = $('#user-district-id');
                        districtSelect.empty();
                        districtSelect.append('<option value="">Select district</option>');

                        $.each(response, function(key, value) {
                            districtSelect.append('<option value="' + key + '">' +
                                value + '</option>');
                        });

                        // Clear dependent dropdowns only if not preserving district
                        if (!preserveDistrictId) {
                            $('#user-block-id').empty().append(
                                '<option value="">Select block</option>');
                        }

                        // Set current value if exists
                        var districtIdToSet = preserveDistrictId ? preserveDistrictId :
                            currentDistrictId;
                        if (districtIdToSet && districtIdToSet !== null) {
                            districtSelect.val(districtIdToSet);
                            loadUserBlocks(districtIdToSet, preserveDistrictId ?
                                currentBlockId : null).then(resolve);
                        } else {
                            resolve();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading districts:', error);
                        console.error('Status:', status);
                        console.error('Status Code:', xhr.status);
                        console.error('Response:', xhr.responseText);
                        console.error('URL:', '{{ route('user_districts') }}');

                        if (xhr.status === 404) {
                            alert(
                                'Route not found. Please check if the route is properly configured.');
                        } else if (xhr.status === 401 || xhr.status === 403) {
                            alert(
                                'Authentication required. Please refresh the page and try again.');
                        } else {
                            alert('Error loading districts. Status: ' + xhr.status +
                                '. Please check console for details.');
                        }
                        reject(error);
                    }
                });
            });
        }

        // Load blocks based on district
        function loadUserBlocks(districtId, preserveBlockId) {
            if (!districtId) {
                $('#user-block-id').empty().append('<option value="">Select block</option>');
                return Promise.resolve();
            }

            return new Promise(function(resolve, reject) {
                $.ajax({
                    url: '{{ route('user_blocks') }}',
                    type: 'GET',
                    data: {
                        district: districtId
                    },
                    dataType: 'json',
                    xhrFields: {
                        withCredentials: true
                    },
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        console.log('Blocks loaded:', response);
                        var blockSelect = $('#user-block-id');
                        blockSelect.empty();
                        blockSelect.append('<option value="">Select block</option>');

                        $.each(response, function(key, value) {
                            blockSelect.append('<option value="' + key + '">' +
                                value + '</option>');
                        });

                        // Set current value if exists
                        var blockIdToSet = preserveBlockId ? preserveBlockId :
                            currentBlockId;
                        if (blockIdToSet && blockIdToSet !== null) {
                            blockSelect.val(blockIdToSet);
                        }

                        resolve();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading blocks:', error);
                        console.error('Status:', status);
                        console.error('Status Code:', xhr.status);
                        console.error('Response:', xhr.responseText);
                        console.error('URL:', '{{ route('user_blocks') }}');

                        if (xhr.status === 404) {
                            alert(
                                'Route not found. Please check if the route is properly configured.');
                        } else if (xhr.status === 401 || xhr.status === 403) {
                            alert(
                                'Authentication required. Please refresh the page and try again.');
                        } else {
                            alert('Error loading blocks. Status: ' + xhr.status +
                                '. Please check console for details.');
                        }
                        reject(error);
                    }
                });
            });
        }

        // Event handlers for cascading dropdowns
        $('#user-country-id').on('change', function() {
            var countryId = $(this).val();
            console.log('Country changed to:', countryId);
            currentStateId = null;
            currentDistrictId = null;
            currentBlockId = null;
            loadUserStates(countryId);
        });

        $('#user-state-id').on('change', function() {
            var stateId = $(this).val();
            console.log('State changed to:', stateId);
            currentDistrictId = null;
            currentBlockId = null;
            loadUserDistricts(stateId);
        });

        $('#user-district-id').on('change', function() {
            var districtId = $(this).val();
            console.log('District changed to:', districtId);
            currentBlockId = null;
            loadUserBlocks(districtId);
        });

        // Initialize form with current values on page load
        if (currentCountryId && currentCountryId !== null) {
            console.log('Initializing with country:', currentCountryId);
            $('#user-country-id').val(currentCountryId);
            // Load states and preserve current state/district/block IDs
            loadUserStates(currentCountryId, currentStateId);
        } else {
            console.log('No country selected, dropdowns will be empty');
        }

        // Handle form submission via AJAX
        $('#accountForm').on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            var form = $(this);
            // Serialize all form fields including those in hidden tabs
            var formData = form.serializeArray();
            // Also include any fields that might be disabled or in hidden tabs
            form.find('input, select, textarea').each(function() {
                var $field = $(this);
                var name = $field.attr('name');
                if (name && !$field.is(':disabled') && !formData.some(function(item) { return item.name === name; })) {
                    formData.push({name: name, value: $field.val()});
                }
            });
            // Convert to URL-encoded string
            var formDataString = $.param(formData);

            var submitButton = form.find('button[type="submit"], #saveProfileBtn');
            var originalButtonText = submitButton.html();

            // Disable submit button and show loading state
            submitButton.prop('disabled', true).html(
                '<i class="mdi mdi-loading mdi-spin mr-1"></i> Saving...');

            // Remove previous alert messages
            $('.alert').remove();

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formDataString,
                dataType: 'json',
                xhrFields: {
                    withCredentials: true
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $(
                        'input[name="_token"]').val()
                },
                success: function(response) {
                    // Show success message
                    var successAlert =
                        '<div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="margin-top: 20px;">' +
                        '<i class="mdi mdi-check-circle mr-2"></i>' +
                        response.message +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span>' +
                        '</button>' +
                        '</div>';

                    $('.row.mb-3 .col-lg-8').html(successAlert);

                    // Update profile completion percentage if provided
                    if (response.profileCompletion !== undefined) {
                        $('#profileProgressBar').css('width', response.profileCompletion + '%')
                            .attr('aria-valuenow', response.profileCompletion);
                        $('#profilePercent').text(response.profileCompletion);
                    }

                    // Scroll to top to show message
                    $('html, body').animate({
                        scrollTop: 0
                    }, 500);

                    // Re-enable submit button
                    submitButton.prop('disabled', false).html(originalButtonText);

                    console.log('Account updated successfully:', response);
                },
                error: function(xhr, status, error) {
                    console.error('Error updating account:', error);
                    console.error('Response:', xhr.responseText);

                    var errorMessage =
                        'An error occurred while updating your account. Please try again.';
                    var errorsHtml = '';

                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        // Validation errors
                        var errors = xhr.responseJSON.errors;
                        errorsHtml = '<ul class="mb-0 pl-3">';
                        $.each(errors, function(field, messages) {
                            $.each(messages, function(index, message) {
                                errorsHtml += '<li>' + message + '</li>';
                            });
                        });
                        errorsHtml += '</ul>';
                        errorMessage = 'Please fix the following errors:';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    var errorAlert =
                        '<div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="margin-top: 20px;">' +
                        '<i class="mdi mdi-alert-circle-outline mr-2"></i>' +
                        errorMessage +
                        errorsHtml +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span>' +
                        '</button>' +
                        '</div>';

                    $('.row.mb-3 .col-lg-8').html(errorAlert);

                    // Scroll to top to show error
                    $('html, body').animate({
                        scrollTop: 0
                    }, 500);

                    // Re-enable submit button
                    submitButton.prop('disabled', false).html(originalButtonText);
                }
            });
        });

        // Handle avatar change (upload via AJAX, update preview without reload)
        $('#change-avatar-btn').on('click', function() {
            $('#avatar-input').click();
        });

        $('#avatar-input').on('change', function() {
            var fileInput = this;
            if (!fileInput.files || !fileInput.files[0]) {
                return;
            }

            var formData = new FormData();
            formData.append('avatar', fileInput.files[0]);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content') || $(
                'input[name="_token"]').val());

            // Optional: simple loading state on button
            var btn = $('#change-avatar-btn');
            var originalText = btn.text();
            btn.prop('disabled', true).text('Uploading...');

            $.ajax({
                url: '{{ route('user.avatar.update') }}',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                xhrFields: {
                    withCredentials: true
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (response.avatar_url) {
                        // Bust cache by appending timestamp
                        var newSrc = response.avatar_url + '?v=' + Date.now();
                        $('#user-avatar-preview').attr('src', newSrc);
                        $('#nav-avatar').attr('src', newSrc);
                        // Also update front navbar avatar if it exists
                        $('#front-nav-avatar').attr('src', newSrc);
                    }

                    // Show success alert
                    var successAlert =
                        '<div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="margin-top: 10px;">' +
                        '<i class="mdi mdi-check-circle mr-2"></i>' +
                        (response.message || 'Profile photo updated successfully.') +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span>' +
                        '</button>' +
                        '</div>';
                    $('.row.mb-3 .col-lg-8').prepend(successAlert);
                },
                error: function(xhr, status, error) {
                    console.error('Error updating avatar:', error);
                    console.error('Response:', xhr.responseText);

                    var errorMessage =
                        'An error occurred while updating your photo. Please try again.';
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors &&
                        xhr.responseJSON.errors.avatar) {
                        errorMessage = xhr.responseJSON.errors.avatar.join('<br>');
                    }

                    var errorAlert =
                        '<div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="margin-top: 10px;">' +
                        '<i class="mdi mdi-alert-circle-outline mr-2"></i>' +
                        errorMessage +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span>' +
                        '</button>' +
                        '</div>';
                    $('.row.mb-3 .col-lg-8').prepend(errorAlert);
                },
                complete: function() {
                    btn.prop('disabled', false).text(originalText);
                    // Clear file input so same file can be re-selected if needed
                    $(fileInput).val('');
                }
            });
        });

        // Handle password change via AJAX
        $('#passwordForm').on('submit', function(e) {
            e.preventDefault();

            var form = $(this);
            var formData = form.serialize();
            var submitButton = form.find('button[type="submit"]');
            var originalButtonText = submitButton.html();

            submitButton.prop('disabled', true).html(
                '<i class="mdi mdi-loading mdi-spin mr-1"></i> Updating...');
            $('.alert').remove();

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                dataType: 'json',
                xhrFields: {
                    withCredentials: true
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $(
                        'input[name="_token"]').val()
                },
                success: function(response) {
                    var successAlert =
                        '<div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="margin-top: 20px;">' +
                        '<i class="mdi mdi-check-circle mr-2"></i>' +
                        (response.message || 'Password updated successfully!') +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span>' +
                        '</button>' +
                        '</div>';

                    $('.row.mb-3 .col-lg-8').html(successAlert);
                    $('html, body').animate({
                        scrollTop: 0
                    }, 500);
                    submitButton.prop('disabled', false).html(originalButtonText);
                    form[0].reset();
                },
                error: function(xhr, status, error) {
                    console.error('Error updating password:', error);
                    console.error('Response:', xhr.responseText);

                    var errorMessage =
                        'An error occurred while updating your password. Please try again.';
                    var errorsHtml = '';

                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;
                        errorsHtml = '<ul class="mb-0 pl-3">';
                        $.each(errors, function(field, messages) {
                            $.each(messages, function(index, message) {
                                errorsHtml += '<li>' + message + '</li>';
                            });
                        });
                        errorsHtml += '</ul>';
                        errorMessage = 'Please fix the following errors:';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    var errorAlert =
                        '<div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="margin-top: 20px;">' +
                        '<i class="mdi mdi-alert-circle-outline mr-2"></i>' +
                        errorMessage +
                        errorsHtml +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span>' +
                        '</button>' +
                        '</div>';

                    $('.row.mb-3 .col-lg-8').html(errorAlert);
                    $('html, body').animate({
                        scrollTop: 0
                    }, 500);
                    submitButton.prop('disabled', false).html(originalButtonText);
                }
            });
        });
    });
</script>
</body>

</html>
