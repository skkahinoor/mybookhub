@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS for enhanced styling -->
    <style>
        .page-header {
    background: #274472;
    color: #fff;
    padding: 32px 30px;
    border-radius: 12px;
    margin-bottom: 32px;
    box-shadow: 0 6px 22px rgba(39,68,114,0.10);
}
.form-container {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 18px rgba(60,72,100,0.11);
    padding: 32px 30px;
    margin-bottom: 28px;
}
.page-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 8px 0;
    letter-spacing: 0.02em;
}
.page-subtitle {
    font-size: 1rem;
    color: #dde3ec;
    margin: 0;
}

.form-label {
    font-weight: 600;
    color: #274472;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.form-control {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 11px 16px;
    font-size: 0.97rem;
    transition: border-color 0.16s;
    background: #fbfcfd;
    color: #274472;
}
.form-control:focus {
    border-color: #274472;
    box-shadow: 0 0 6px rgba(39,68,114,0.11);
    background: #fff;
}
.form-group {
    margin-bottom: 26px;
}
.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}
.form-icon {
    color: #25836e;
    width: 16px;
}

.required {
    color: #d13e3e;
}

.error-message {
    color: #d13e3e;
    font-size: 0.89rem;
    margin-top: 5px;
}

.btn-submit, .btn-cancel {
    font-weight: 600;
    border-radius: 8px;
    padding: 11px 28px;
    border: none;
    transition: all 0.23s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 1rem;
    cursor: pointer;
}
.btn-submit {
    background: #25836e;
    color: #fff;
    box-shadow: 0 3px 18px rgba(37, 131, 110, 0.09);
}
.btn-submit:hover {
    background: #176276;
    box-shadow: 0 7px 26px rgba(37, 131, 110, 0.15);
}
.btn-cancel {
    background: #dde3ec;
    color: #274472;
    border: 1px solid #ced5de;
    margin-right: 12px;
}
.btn-cancel:hover {
    background: #b7c7e2;
    color: #274472;
    text-decoration: none;
}


.class-item {
    background: #f8f9fa;
    border: 2px solid #e9ecef !important;
}

.class-item label {
    font-size: 0.85rem;
    font-weight: 600;
    color: #274472;
    margin-bottom: 5px;
}

#add-class-btn {
    background: #25836e;
    color: #fff;
    border: none;
}

#add-class-btn:hover {
    background: #176276;
}

.remove-class-btn {
    background: #dc3545;
    color: #fff;
    border: none;
}

.remove-class-btn:hover {
    background: #c82333;
}

/* Responsive tweaks for mobile devices */
@media (max-width: 576px) {
    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }
    .form-container, .page-header {
        padding: 20px 10px;
    }
    .page-title { font-size: 1.3rem; }
}


    </style>


    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Add Institution</h4>
        <a href="{{ route('sales.institution_managements.index') }}" class="btn btn-outline-secondary">Back to list</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('sales.institution_managements.store') }}">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-school form-icon"></i>
                            Institution Name <span class="required">*</span>
                        </label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                            placeholder="Enter institution name" required>
                        @error('name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-building form-icon"></i>
                            Institution Type <span class="required">*</span>
                        </label>
                        <select name="type" class="form-control" required>
                            <option value="">Select Type</option>
                            <option value="school" {{ old('type') == 'school' ? 'selected' : '' }}>School</option>
                            <option value="college" {{ old('type') == 'college' ? 'selected' : '' }}>College
                            </option>
                            <option value="university" {{ old('type') == 'university' ? 'selected' : '' }}>
                                University</option>
                        </select>
                        @error('type')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-certificate form-icon"></i>
                            Board <span class="required">*</span>
                        </label>
                        <select name="board" class="form-control" required>
                            <option value="">Select Board</option>
                            <option value="CBSE" {{ old('board') == 'CBSE' ? 'selected' : '' }}>CBSE</option>
                            <option value="ICSE" {{ old('board') == 'ICSE' ? 'selected' : '' }}>ICSE</option>
                            <option value="State Board" {{ old('board') == 'State Board' ? 'selected' : '' }}>State
                                Board</option>
                            <option value="Other" {{ old('board') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('board')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group" id="class-field" style="display: none;">
                        <label class="form-label">
                            <i class="fas fa-layer-group form-icon"></i>
                            Add Class with Strength
                        </label>
                        <div id="class-list-container">
                            <!-- Classes will be added dynamically here -->
                        </div>
                        <button type="button" class="btn btn-success btn-sm mt-2" id="add-class-btn">
                            <i class="fas fa-plus"></i> Add Class
                        </button>
                        @error('class')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-phone form-icon"></i>
                            Contact Number <span class="required">*</span>
                        </label>
                        <input type="text" name="contact_number" class="form-control"
                            value="{{ old('contact_number') }}" placeholder="Enter contact number" required>
                        @error('contact_number')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-flag form-icon"></i>
                            Country <span class="required">*</span>
                        </label>
                        <select name="country_id" class="form-control" id="country-select" required>
                            <option value="">Select Country</option>
                        </select>
                        @error('country_id')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-map form-icon"></i>
                            State <span class="required">*</span>
                        </label>
                        <select name="state_id" class="form-control" id="state-select" required>
                            <option value="">Select State</option>
                        </select>
                        @error('state_id')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-map-marker-alt form-icon"></i>
                            District <span class="required">*</span>
                        </label>
                        <select name="district_id" class="form-control" id="district-select" required>
                            <option value="">Select District</option>
                        </select>
                        @error('district_id')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-cube form-icon"></i>
                            Block <span class="required">*</span>
                        </label>
                        <select name="block_id" class="form-control" id="block-select">
                            <option value="">Select Block</option>
                        </select>
                        @error('block_id')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-mail-bulk form-icon"></i>
                            Pincode <span class="required">*</span>
                        </label>
                        <input type="number" name="pincode" class="form-control" id="pincode-input"
                            value="{{ old('pincode') }}" placeholder="Enter pincode" required>
                        @error('pincode')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                

                <div class="form-group text-center mt-4">
                    <a href="{{ url('sales/institution-managements') }}" class="btn-cancel">
                        <i class="fas fa-arrow-left"></i>
                        Cancel
                    </a>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i>
                        Create Institution
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle institution type change
            $('select[name="type"]').change(function() {
                var type = $(this).val();
                var classField = $('#class-field');
                var classSelect = $('#class-select');

                if (type === 'school') {
                    // Show class field
                    classField.show();

                    // Load classes via AJAX
                    $.ajax({
                        url: '{{ route('institution_classes') }}',
                        type: 'GET',
                        data: {
                            type: type
                        },
                        dataType: 'json',
                        success: function(response) {
                            console.log('AJAX Success:', response);
                            classSelect.empty();
                            classSelect.append('<option value="">Select Class</option>');

                            if (Array.isArray(response)) {
                                $.each(response, function(index, className) {
                                    classSelect.append('<option value="' + className +
                                        '">' + className + '</option>');
                                });
                            }

                            // Set old value if exists
                            @if (old('class'))
                                classSelect.val('{{ old('class') }}');
                            @endif
                        },
                        error: function(xhr, status, error) {
                            console.log('AJAX Error Details:');
                            console.log('Status:', status);
                            console.log('Error:', error);
                            console.log('Response Text:', xhr.responseText);
                            console.log('Status Code:', xhr.status);
                            console.log('URL:', '{{ route('institution_classes') }}');
                            console.log('Type:', type);

                            // Try to show a more helpful error message
                            var errorMessage = 'Error loading classes. ';
                            if (xhr.status === 404) {
                                errorMessage +=
                                    'Route not found. Please check if you are logged in as sales.';
                            } else if (xhr.status === 403) {
                                errorMessage += 'Access denied. Please check your permissions.';
                            } else if (xhr.status === 500) {
                                errorMessage += 'Server error. Please try again later.';
                            } else {
                                errorMessage += 'Please check console for details.';
                            }
                            alert(errorMessage);
                        }
                    });
                } else {
                    // Hide class field and clear value
                    classField.hide();
                    classSelect.empty();
                    classSelect.append('<option value="">Select Class</option>');
                }
            });

            // Trigger change event on page load if type is already selected
            if ($('select[name="type"]').val() === 'school') {
                $('select[name="type"]').trigger('change');
            }

            // Add new class row
            var classIndex = 0;
            var availableClasses = ['Nursery', 'LKG', 'UKG', 'Class 1', 'Class 2', 'Class 3', 'Class 4', 'Class 5',
                'Class 6', 'Class 7', 'Class 8', 'Class 9', 'Class 10', 'Class 11', 'Class 12'
            ];

            $('#add-class-btn').on('click', function() {
                var classOptions = availableClasses.map(cls =>
                    `<option value="${cls}">${cls}</option>`
                ).join('');

                var classHtml = `
            <div class="class-item mb-3 p-3 border rounded" data-index="${classIndex}">
                <div class="row">
                    <div class="col-md-5">
                        <label>Class Name</label>
                        <select name="classes[${classIndex}][class_name]" class="form-control class-select" required>
                            <option value="">Select Class</option>
                            ${classOptions}
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label>Total Strength</label>
                        <input type="number" name="classes[${classIndex}][strength]" class="form-control" placeholder="e.g., 50" min="1" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm remove-class-btn">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
                $('#class-list-container').append(classHtml);
                classIndex++;
            });

            // Remove class row
            $(document).on('click', '.remove-class-btn', function() {
                $(this).closest('.class-item').remove();
            });

            // Load countries on page load
            function loadCountries() {
                $.ajax({
                    url: '{{ route('institution_countries') }}',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        var countrySelect = $('#country-select');
                        countrySelect.empty();
                        countrySelect.append('<option value="">Select Country</option>');

                        $.each(response, function(key, value) {
                            countrySelect.append('<option value="' + key + '">' + value +
                                '</option>');
                        });

                        // Set old value if exists and trigger change to load states
                        @if (old('country_id'))
                            countrySelect.val('{{ old('country_id') }}');
                            loadStates('{{ old('country_id') }}');
                        @endif
                    },
                    error: function(xhr, status, error) {
                        console.log('Error loading countries:', error);
                    }
                });
            }

            // Load states based on country
            function loadStates(country) {
                if (!country) {
                    $('#state-select').empty().append('<option value="">Select State</option>');
                    $('#district-select').empty().append('<option value="">Select District</option>');
                    // $('#city-select').empty().append('<option value="">Select City</option>');
                    $('#block-select').empty().append('<option value="">Select Block</option>');
                    return;
                }

                $.ajax({
                    url: '{{ route('institution_states') }}',
                    type: 'GET',
                    data: {
                        country: country
                    },
                    dataType: 'json',
                    success: function(response) {
                        var stateSelect = $('#state-select');
                        stateSelect.empty();
                        stateSelect.append('<option value="">Select State</option>');

                        $.each(response, function(key, value) {
                            stateSelect.append('<option value="' + key + '">' + value +
                                '</option>');
                        });

                        // Clear dependent dropdowns
                        $('#district-select').empty().append(
                            '<option value="">Select District</option>');
                        // $('#city-select').empty().append('<option value="">Select City</option>');
                        $('#block-select').empty().append('<option value="">Select Block</option>');

                        // Set old value if exists and trigger change to load districts
                        @if (old('state_id'))
                            stateSelect.val('{{ old('state_id') }}');
                            loadDistricts('{{ old('state_id') }}');
                        @endif
                    },
                    error: function(xhr, status, error) {
                        console.log('Error loading states:', error);
                    }
                });
            }

            // Load districts based on state
            function loadDistricts(state) {
                if (!state) {
                    $('#district-select').empty().append('<option value="">Select District</option>');
                    // $('#city-select').empty().append('<option value="">Select City</option>');
                    $('#block-select').empty().append('<option value="">Select Block</option>');
                    return;
                }

                $.ajax({
                    url: '{{ route('institution_districts') }}',
                    type: 'GET',
                    data: {
                        state: state
                    },
                    dataType: 'json',
                    success: function(response) {
                        var districtSelect = $('#district-select');
                        districtSelect.empty();
                        districtSelect.append('<option value="">Select District</option>');

                        $.each(response, function(key, value) {
                            districtSelect.append('<option value="' + key + '">' + value +
                                '</option>');
                        });

                        // Clear dependent dropdowns
                        // $('#city-select').empty().append('<option value="">Select City</option>');
                        $('#block-select').empty().append('<option value="">Select Block</option>');

                        // Set old value if exists and trigger change to load blocks
                        @if (old('district_id'))
                            districtSelect.val('{{ old('district_id') }}');
                            loadBlocks('{{ old('district_id') }}');
                        @endif
                    },
                    error: function(xhr, status, error) {
                        console.log('Error loading districts:', error);
                    }
                });
            }



            // Load blocks based on district
            function loadBlocks(district) {
                if (!district) {
                    $('#block-select').empty().append('<option value="">Select Block</option>');
                    return;
                }

                $.ajax({
                    url: '{{ route('institution_blocks') }}',
                    type: 'GET',
                    data: {
                        district: district
                    },
                    dataType: 'json',
                    success: function(response) {
                        var blockSelect = $('#block-select');
                        blockSelect.empty();
                        blockSelect.append('<option value="">Select Block</option>');

                        $.each(response, function(key, value) {
                            blockSelect.append('<option value="' + key + '">' + value +
                                '</option>');
                        });

                        // Set old value if exists
                        @if (old('block_id'))
                            blockSelect.val('{{ old('block_id') }}');
                        @endif
                    },
                    error: function(xhr, status, error) {
                        console.log('Error loading blocks:', error);
                    }
                });
            }

            // Event handlers for cascading dropdowns
            $('#country-select').on('change', function() {
                var country = $(this).val();
                loadStates(country);
            });

            $('#state-select').on('change', function() {
                var state = $(this).val();
                loadDistricts(state);
            });

            // $('#district-select').on('change', function() {
            //     var district = $(this).val();
            //     loadCities(district);
            // });

            $('#district-select').on('change', function() {
                var district = $(this).val();
                loadBlocks(district);
            });

            // Load countries on page load
            loadCountries();
        });
    </script>
@endsection


