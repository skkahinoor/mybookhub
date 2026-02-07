@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-12 grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">{{ isset($salesExecutive) ? 'Edit' : 'Add' }} Sales Executive</h4>

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

                            <form method="POST"
                                action="{{ url('admin/add-edit-sales-executive' . (isset($salesExecutive) ? '/' . $user->id : '')) }}">
                                @csrf

                                <div class="form-group">
                                    <label>Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $user->name ?? '') }}" required>
                                </div>

                                <div class="form-group">
                                    <label>Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ old('email', $user->email ?? '') }}" required>
                                </div>

                                <div class="form-group">
                                    <label>Phone <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control"
                                        value="{{ old('phone', $user->phone ?? '') }}" required>
                                </div>

                                @if (!isset($salesExecutive))
                                    <div class="form-group">
                                        <label>Password <span class="text-danger">*</span></label>
                                        <input type="password" name="password" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Confirm Password <span class="text-danger">*</span></label>
                                        <input type="password" name="password_confirmation" class="form-control" required>
                                    </div>
                                @else
                                    <div class="form-group">
                                        <label>New Password</label>
                                        <input type="password" name="password" class="form-control"
                                            placeholder="Leave blank to keep current">
                                    </div>
                                @endif

                                <h5 class="mt-4 mb-3">Location Details</h5>

                                <div class="form-group">
                                    <label>Address</label>
                                    <textarea name="address" class="form-control" rows="3">{{ old('address', $user->address ?? '') }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label>Country</label>
                                    <select class="form-control" id="country_id" name="country_id">
                                        <option value="">Select Country</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country['id'] }}" 
                                                @if (old('country_id', $user->country_id ?? '') == $country['id']) selected @endif>
                                                {{ $country['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>State</label>
                                    <select class="form-control" id="state_id" name="state_id">
                                        <option value="">Select State</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>District</label>
                                    <select class="form-control" id="district_id" name="district_id">
                                        <option value="">Select District</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Block</label>
                                    <select class="form-control" id="block_id" name="block_id">
                                        <option value="">Select Block</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Pincode</label>
                                    <input type="text" name="pincode" class="form-control"
                                        value="{{ old('pincode', $user->pincode ?? '') }}">
                                </div>

                                <h5 class="mt-4 mb-3">Bank Details</h5>

                                <div class="form-group">
                                    <label>Bank Name</label>
                                    <input type="text" name="bank_name" class="form-control"
                                        value="{{ old('bank_name', $salesExecutive->bank_name ?? '') }}">
                                </div>

                                <div class="form-group">
                                    <label>Account Number</label>
                                    <input type="text" name="account_number" class="form-control"
                                        value="{{ old('account_number', $salesExecutive->account_number ?? '') }}">
                                </div>

                                <div class="form-group">
                                    <label>IFSC Code</label>
                                    <input type="text" name="ifsc_code" class="form-control"
                                        value="{{ old('ifsc_code', $salesExecutive->ifsc_code ?? '') }}">
                                </div>

                                <div class="form-group">
                                    <label>Bank Branch</label>
                                    <input type="text" name="bank_branch" class="form-control"
                                        value="{{ old('bank_branch', $salesExecutive->bank_branch ?? '') }}">
                                </div>

                                <div class="form-group">
                                    <label>UPI ID</label>
                                    <input type="text" name="upi_id" class="form-control"
                                        value="{{ old('upi_id', $salesExecutive->upi_id ?? '') }}">
                                </div>

                                <h5 class="mt-4 mb-3">Target Details</h5>

                                <div class="form-group">
                                    <label>Total Target</label>
                                    <input type="number" name="total_target" class="form-control"
                                        value="{{ old('total_target', $salesExecutive->total_target ?? '') }}">
                                </div>

                                <div class="form-group">
                                    <label>Completed Target</label>
                                    <input type="number" name="completed_target" class="form-control"
                                        value="{{ old('completed_target', $salesExecutive->completed_target ?? '') }}">
                                </div>

                                <div class="form-group">
                                    <label>Income Per Target</label>
                                    <input type="number" name="income_per_target" class="form-control"
                                        value="{{ old('income_per_target', $salesExecutive->income_per_target ?? 10) }}">
                                </div>

                                <button type="submit"
                                    class="btn btn-primary">{{ isset($salesExecutive) ? 'Update' : 'Create' }}</button>
                                <a href="{{ route('salesexecutives.index') }}" class="btn btn-light">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Current location values
        var currentCountryId = @json($user->country_id ?? null);
        var currentStateId = @json($user->state_id ?? null);
        var currentDistrictId = @json($user->district_id ?? null);
        var currentBlockId = @json($user->block_id ?? null);

        // Load states based on country
        function loadStates(countryId) {
            if (!countryId) {
                $('#state_id').html('<option value="">Select State</option>');
                $('#district_id').html('<option value="">Select District</option>');
                $('#block_id').html('<option value="">Select Block</option>');
                return;
            }

            $.ajax({
                url: '/admin/get-states/' + countryId,
                type: 'GET',
                success: function(data) {
                    var options = '<option value="">Select State</option>';
                    $.each(data, function(key, state) {
                        var selected = (currentStateId == state.id) ? 'selected' : '';
                        options += '<option value="' + state.id + '" ' + selected + '>' + state.name + '</option>';
                    });
                    $('#state_id').html(options);
                    
                    if (currentStateId) {
                        loadDistricts(currentStateId);
                    }
                }
            });
        }

        // Load districts based on state
        function loadDistricts(stateId) {
            if (!stateId) {
                $('#district_id').html('<option value="">Select District</option>');
                $('#block_id').html('<option value="">Select Block</option>');
                return;
            }

            $.ajax({
                url: '/admin/get-districts/' + stateId,
                type: 'GET',
                success: function(data) {
                    var options = '<option value="">Select District</option>';
                    $.each(data, function(key, district) {
                        var selected = (currentDistrictId == district.id) ? 'selected' : '';
                        options += '<option value="' + district.id + '" ' + selected + '>' + district.name + '</option>';
                    });
                    $('#district_id').html(options);
                    
                    if (currentDistrictId) {
                        loadBlocks(currentDistrictId);
                    }
                }
            });
        }

        // Load blocks based on district
        function loadBlocks(districtId) {
            if (!districtId) {
                $('#block_id').html('<option value="">Select Block</option>');
                return;
            }

            $.ajax({
                url: '/admin/get-blocks/' + districtId,
                type: 'GET',
                success: function(data) {
                    var options = '<option value="">Select Block</option>';
                    $.each(data, function(key, block) {
                        var selected = (currentBlockId == block.id) ? 'selected' : '';
                        options += '<option value="' + block.id + '" ' + selected + '>' + block.name + '</option>';
                    });
                    $('#block_id').html(options);
                }
            });
        }

        // Event handlers
        $('#country_id').change(function() {
            currentStateId = null;
            currentDistrictId = null;
            currentBlockId = null;
            loadStates($(this).val());
        });

        $('#state_id').change(function() {
            currentDistrictId = null;
            currentBlockId = null;
            loadDistricts($(this).val());
        });

        $('#district_id').change(function() {
            currentBlockId = null;
            loadBlocks($(this).val());
        });

        // Load initial data if editing
        if (currentCountryId) {
            loadStates(currentCountryId);
        }
    });
</script>
@endsection
