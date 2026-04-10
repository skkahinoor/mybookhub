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
                            <h5 class="mb-1">{{ Auth::user()->name ?? 'Student' }}</h5>
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
                                    <a class="nav-link" data-toggle="tab" href="#tab-academic" role="tab">
                                        <i class="mdi mdi-book-outline mr-1"></i> Academic info
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab-manage-addresses" role="tab">
                                        <i class="mdi mdi-home-map-marker mr-1"></i> My Addresses
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab-bank" role="tab">
                                        <i class="mdi mdi-bank-outline mr-1"></i> Bank details
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
                            <form id="accountForm" action="{{ route('student.account') }}" method="POST">
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
                                        </div>
                                    </div>

                                        {{-- Academic info tab --}}
                                        <div class="tab-pane fade" id="tab-academic" role="tabpanel">
                                            <div class="form-group">
                                                <label for="academic-education-level">Education Level</label>
                                                <select id="academic-education-level" name="education_level_id" class="form-control">
                                                    <option value="">Select Education Level</option>
                                                    @foreach ($sections as $sec)
                                                        <option value="{{ $sec->id }}"
                                                            {{ optional($user->academicProfile)->education_level_id == $sec->id ? 'selected' : '' }}>
                                                            {{ $sec->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="academic-board">Board</label>
                                                <select id="academic-board" name="board_id" class="form-control">
                                                    <option value="">Select Board</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="academic-class">Class</label>
                                                <select id="academic-class" name="class_id" class="form-control">
                                                    <option value="">Select Class</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="academic-institution">Institution (Optional)</label>
                                                <select id="academic-institution" name="institution_id"
                                                        class="form-control">
                                                    <option value="">Select Institution</option>
                                                    @foreach ($institutions as $institution)
                                                        <option value="{{ $institution->id }}"
                                                            {{ optional($user)->institution_id == $institution->id ? 'selected' : '' }}>
                                                            {{ $institution->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                    {{-- Manage Addresses tab --}}
                                    <div class="tab-pane fade" id="tab-manage-addresses" role="tabpanel">
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <h6 class="font-weight-bold mb-0">Saved Shipping Addresses</h6>
                                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal"
                                                data-target="#addressModal" onclick="clearAddressForm('{{ Auth::user()->name }}', '{{ Auth::user()->phone }}')">
                                                <i class="mdi mdi-plus mr-1"></i> Add New Address
                                            </button>
                                        </div>

                                        <div class="row" id="addressList">
                                            @foreach ($addresses as $addr)
                                                <div class="col-md-6 mb-3">
                                                    <div class="card border {{ $addr->is_default ? 'border-primary' : '' }}" style="border-radius: 10px; background: {{ $addr->is_default ? '#f8fbfc' : '#fff' }}">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex justify-content-between">
                                                                <h6 class="font-weight-bold mb-2">{{ $addr->name }}</h6>
                                                                @if($addr->is_default)
                                                                    <span class="badge badge-primary small">Default</span>
                                                                @endif
                                                            </div>
                                                            <p class="text-muted small mb-2" style="line-height: 1.4">
                                                                {{ $addr->address }}<br>
                                                                {{ optional($addr->block)->name ? $addr->block->name . ', ' : '' }}
                                                                {{ optional($addr->district)->name ? $addr->district->name . ', ' : '' }}
                                                                {{ optional($addr->state)->name ? $addr->state->name : '' }} - {{ $addr->pincode }}<br>
                                                                {{ optional($addr->country)->name }}
                                                            </p>
                                                            <p class="small mb-3"><strong>Mobile:</strong> {{ $addr->mobile }}</p>
                                                            
                                                            <div class="d-flex border-top pt-2">
                                                                 <a href="javascript:void(0)" class="text-primary mr-3 small font-weight-bold" 
                                                                     onclick="window.editThisAddress(this)"
                                                                     data-id="{{ $addr->id }}"
                                                                     data-name="{{ $addr->name }}"
                                                                     data-mobile="{{ $addr->mobile }}"
                                                                     data-address="{{ $addr->address }}"
                                                                     data-pincode="{{ $addr->pincode }}"
                                                                     data-country="{{ $addr->country_id }}"
                                                                     data-state="{{ $addr->state_id }}"
                                                                     data-district="{{ $addr->district_id }}"
                                                                     data-block="{{ $addr->block_id }}"
                                                                     style="text-decoration: none;">
                                                                     <i class="mdi mdi-pencil mr-1"></i> Edit
                                                                 </a>
                                                                 <a href="javascript:void(0)" class="text-danger mr-3 small font-weight-bold" 
                                                                     onclick="window.deleteAddress({{ $addr->id }})"
                                                                     style="text-decoration: none;">
                                                                     <i class="mdi mdi-delete mr-1"></i> Delete
                                                                 </a>
                                                                 @if(!$addr->is_default)
                                                                     <a href="javascript:void(0)" class="text-secondary ml-auto small font-weight-bold" 
                                                                         onclick="window.setDefaultAddress({{ $addr->id }})"
                                                                         style="text-decoration: none;">
                                                                         Set Default
                                                                     </a>
                                                                 @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        @if($addresses->isEmpty())
                                            <div class="text-center py-5 bg-light rounded" style="border: 1px dashed #ddd;">
                                                <i class="mdi mdi-map-marker-off-outline text-muted" style="font-size: 40px;"></i>
                                                <p class="text-muted mt-2">No addresses saved yet.</p>
                                                <button type="button" class="btn btn-outline-primary btn-sm mt-2" data-toggle="modal" data-target="#addressModal" onclick="clearAddressForm('{{ Auth::user()->name }}', '{{ Auth::user()->phone }}')">
                                                    Add Your First Address
                                                </button>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Bank info tab --}}
                                    <div class="tab-pane fade" id="tab-bank" role="tabpanel">
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="bank-name">Bank Name</label>
                                                <input type="text" class="form-control" id="bank-name"
                                                    name="bank_name" placeholder="e.g. State Bank of India"
                                                    value="{{ Auth::user()->bank_name ?? '' }}">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="account-holder">Account Holder Name</label>
                                                <input type="text" class="form-control" id="account-holder"
                                                    name="account_holder_name" placeholder="Name as per bank records"
                                                    value="{{ Auth::user()->account_holder_name ?? '' }}">
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="account-number">Account Number</label>
                                                <input type="text" class="form-control" id="account-number"
                                                    name="account_number" placeholder="Enter account number"
                                                    value="{{ Auth::user()->account_number ?? '' }}">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="ifsc-code">IFSC Code</label>
                                                <input type="text" class="form-control" id="ifsc-code"
                                                    name="ifsc_code" placeholder="e.g. SBIN0001234"
                                                    value="{{ Auth::user()->ifsc_code ?? '' }}">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="upi-id">UPI ID (VPA)</label>
                                            <input type="text" class="form-control" id="upi-id"
                                                name="upi_id" placeholder="e.g. username@okaxis"
                                                value="{{ Auth::user()->upi_id ?? '' }}">
                                        </div>

                                        <div class="alert alert-info small mt-3 mb-0">
                                            <i class="mdi mdi-information-outline mr-1"></i>
                                            Your bank details are securely stored and used only for wallet withdrawals or refunds.
                                        </div>
                                    </div>
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

    <!-- Address Modal -->
    <div class="modal fade" id="addressModal" tabindex="-1" role="dialog" aria-labelledby="addressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addressModalLabel">Add New Address</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="multiAddressForm">
                    @csrf
                    <input type="hidden" name="address_id" id="multi_address_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Full Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-right-0"><i class="mdi mdi-account text-primary"></i></span>
                                    </div>
                                    <input type="text" name="name" id="addr_name" class="form-control border-left-0" placeholder="Enter full name" required>
                                </div>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Mobile Number <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-right-0"><i class="mdi mdi-phone text-primary"></i></span>
                                    </div>
                                    <input type="text" name="mobile" id="addr_mobile" class="form-control border-left-0" placeholder="Enter 10 digit mobile" required>
                                </div>
                            </div>
                            <div class="col-md-12 form-group">
                                <label>Address <span class="text-danger">*</span></label>
                                <textarea name="address" id="addr_address" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Country <span class="text-danger">*</span></label>
                                <select name="country_id" id="addr_country_id" class="form-control" required>
                                    <option value="">Select Country</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>State <span class="text-danger">*</span></label>
                                <select name="state_id" id="addr_state_id" class="form-control" required>
                                    <option value="">Select State</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>District <span class="text-danger">*</span></label>
                                <select name="district_id" id="addr_district_id" class="form-control" required>
                                    <option value="">Select District</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Block</label>
                                <select name="block_id" id="addr_block_id" class="form-control">
                                    <option value="">Select Block</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Pincode <span class="text-danger">*</span></label>
                                <input type="text" name="pincode" id="addr_pincode" class="form-control" placeholder="Enter 6 digit pincode" required>
                            </div>
                            <div class="col-md-12 mt-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="update_profile" name="update_profile" value="1">
                                    <label class="custom-control-label small text-muted font-weight-bold" for="update_profile">
                                        <i class="mdi mdi-sync mr-1"></i> Also update my account profile name and mobile
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary shadow-sm">Save Address</button>
                    </div>
                </form>
            </div>
        </div>
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
    // Academic info cascading dropdowns (Education Level -> Board -> Class)
    jQuery(document).ready(function($) {
        var educationLevelSelect = $('#academic-education-level');
        var academicBoardSelect = $('#academic-board');
        var academicClassSelect = $('#academic-class');

        var currentEducationLevelId = @json(optional($user->academicProfile)->education_level_id);
        var currentBoardId = @json(optional($user->academicProfile)->board_id);
        var currentClassId = @json(optional($user->academicProfile)->class_id);

        function loadAcademicBoards(sectionId, selectedBoardId) {
            academicBoardSelect.empty().append('<option value="">Select Board</option>');
            academicClassSelect.empty().append('<option value="">Select Class</option>');
            if (!sectionId) return;

            $.ajax({
                url: '{{ route('get.filter.categories') }}',
                type: 'GET',
                data: { section_id: sectionId },
                dataType: 'json',
                success: function(response) {
                    $.each(response, function(key, category) {
                        var option = $('<option></option>')
                            .val(category.id)
                            .text(category.category_name);
                        if (selectedBoardId && parseInt(selectedBoardId) === parseInt(category.id)) {
                            option.attr('selected', 'selected');
                        }
                        academicBoardSelect.append(option);
                    });
                    if (selectedBoardId) {
                        loadAcademicClasses(selectedBoardId, sectionId, currentClassId);
                    }
                }
            });
        }

        function loadAcademicClasses(categoryId, sectionId, selectedClassId) {
            academicClassSelect.empty().append('<option value="">Select Class</option>');
            if (!categoryId || !sectionId) return;

            $.ajax({
                url: '{{ route('get.filter.subcategories') }}',
                type: 'GET',
                data: { category_id: categoryId, section_id: sectionId },
                dataType: 'json',
                success: function(response) {
                    $.each(response, function(key, subcat) {
                        var option = $('<option></option>')
                            .val(subcat.id)
                            .text(subcat.category_name);
                        if (selectedClassId && parseInt(selectedClassId) === parseInt(subcat.id)) {
                            option.attr('selected', 'selected');
                        }
                        academicClassSelect.append(option);
                    });
                }
            });
        }

        educationLevelSelect.on('change', function() {
            loadAcademicBoards($(this).val(), null);
        });

        academicBoardSelect.on('change', function() {
            loadAcademicClasses($(this).val(), educationLevelSelect.val(), null);
        });

        // Initial populate if user already has data
        if (currentEducationLevelId) {
            loadAcademicBoards(currentEducationLevelId, currentBoardId);
        }
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
                url: '{{ route('student.avatar.update') }}',
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
    // Address Management Logic
    window.editThisAddress = function(el) {
        var btn = $(el);
        var addr = {
            id: btn.attr('data-id'),
            name: btn.attr('data-name'),
            mobile: btn.attr('data-mobile'),
            address: btn.attr('data-address'),
            pincode: btn.attr('data-pincode'),
            country_id: btn.attr('data-country'),
            state_id: btn.attr('data-state'),
            district_id: btn.attr('data-district'),
            block_id: btn.attr('data-block')
        };
        
        // Open modal first
        $('#addressModal').modal('show');
        $('#addressModalLabel').text('Edit Address');
        
        // Fill fields
        $('#multi_address_id').val(addr.id);
        $('#addr_name').val(addr.name);
        $('#addr_mobile').val(addr.mobile);
        $('#addr_address').val(addr.address);
        $('#addr_pincode').val(addr.pincode);
        $('#addr_country_id').val(addr.country_id);

        // Load Cascading Dropdowns
        if (addr.country_id) {
            loadStates(addr.country_id, addr.state_id);
            if (addr.state_id) {
                loadDistricts(addr.state_id, addr.district_id);
                if (addr.district_id) {
                    loadBlocks(addr.district_id, addr.block_id);
                }
            }
        }
    };

    window.clearAddressForm = function(name = '', mobile = '') {
        $('#multi_address_id').val('');
        $('#multiAddressForm')[0].reset();
        $('#addr_name').val(name);
        $('#addr_mobile').val(mobile);
        $('#update_profile').prop('checked', false);
        $('#addr_state_id').html('<option value="">Select State</option>');
        $('#addr_district_id').html('<option value="">Select District</option>');
        $('#addr_block_id').html('<option value="">Select Block</option>');
        $('#addressModalLabel').text('Add New Address');
    }

    function loadStates(countryId, stateId = null) {
        if (!countryId) return;
        $.get('{{ route('user_states') }}', { country: countryId }, function(res) {
            let html = '<option value="">Select State</option>';
            $.each(res, function(id, name) {
                html += `<option value="${id}" ${id == stateId ? 'selected' : ''}>${name}</option>`;
            });
            $('#addr_state_id').html(html);
        });
    }

    function loadDistricts(stateId, districtId = null) {
        if (!stateId) return;
        $.get('{{ route('user_districts') }}', { state: stateId }, function(res) {
            let html = '<option value="">Select District</option>';
            $.each(res, function(id, name) {
                html += `<option value="${id}" ${id == districtId ? 'selected' : ''}>${name}</option>`;
            });
            $('#addr_district_id').html(html);
        });
    }

    function loadBlocks(districtId, blockId = null) {
        if (!districtId) return;
        $.get('{{ route('user_blocks') }}', { district: districtId }, function(res) {
            let html = '<option value="">Select Block</option>';
            $.each(res, function(id, name) {
                html += `<option value="${id}" ${id == blockId ? 'selected' : ''}>${name}</option>`;
            });
            $('#addr_block_id').html(html);
        });
    }

    $(document).ready(function() {
        $(document).on('change', '#addr_country_id', function() { loadStates($(this).val()); });
        $(document).on('change', '#addr_state_id', function() { loadDistricts($(this).val()); });
        $(document).on('change', '#addr_district_id', function() { loadBlocks($(this).val()); });

        // Address management form submission
        $('#multiAddressForm').on('submit', function(e) {
            e.preventDefault();
            var btn = $(this).find('button[type="submit"]');
            var originalText = btn.html();
            btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin mr-1"></i> Saving...');
            
            $.ajax({
                url: '{{ route('saveAddress') }}',
                type: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    if (res.type == 'success') {
                        location.reload();
                    } else {
                        alert(res.message || 'Please check required fields.');
                        btn.prop('disabled', false).html(originalText);
                    }
                },
                error: function() {
                    alert('Error saving address. Please try again.');
                    btn.prop('disabled', false).html(originalText);
                }
            });
        });
    });

    window.deleteAddress = function(id) {
        if (confirm('Delete this address?')) {
            $.post('{{ route('deleteAddress') }}', { address_id: id, _token: '{{ csrf_token() }}' }, function(res) {
                if (res.type == 'success') location.reload();
            });
        }
    }

    window.setDefaultAddress = function(id) {
        $.post('{{ route('setDefaultAddress') }}', { address_id: id, _token: '{{ csrf_token() }}' }, function(res) {
            if (res.type == 'success') location.reload();
        });
    }
</script>
</body>

</html>

