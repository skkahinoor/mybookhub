@extends('admin.layout.layout')

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

    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-edit"></i>
                    Edit Institution
                </h1>
                <p class="page-subtitle">Update institution information</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="form-container">
                <form method="post" action="{{ url('admin/institution-managements/'.$institution->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-school form-icon"></i>
                                Institution Name <span class="required">*</span>
                            </label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $institution->name) }}" placeholder="Enter institution name" required>
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
                                <option value="school" {{ (old('type', $institution->type) == 'school') ? 'selected' : '' }}>School</option>
                                <option value="college" {{ (old('type', $institution->type) == 'college') ? 'selected' : '' }}>College</option>
                                <option value="university" {{ (old('type', $institution->type) == 'university') ? 'selected' : '' }}>University</option>
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
                                <option value="CBSE" {{ (old('board', $institution->board) == 'CBSE') ? 'selected' : '' }}>CBSE</option>
                                <option value="ICSE" {{ (old('board', $institution->board) == 'ICSE') ? 'selected' : '' }}>ICSE</option>
                                <option value="State Board" {{ (old('board', $institution->board) == 'State Board') ? 'selected' : '' }}>State Board</option>
                                <option value="Other" {{ (old('board', $institution->board) == 'Other') ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('board')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-user form-icon"></i>
                                Principal Name<span class="required">*</span>
                            </label>
                            <input type="text" name="principal_name" class="form-control" value="{{ old('principal_name') }}" placeholder="Enter principal name" required>
                            @error('principal_name')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group" id="class-field" style="display: none;">
                            <label class="form-label">
                                <i class="fas fa-layer-group form-icon"></i>
                                Add Class with Strength
                            </label>
                            <div id="class-list-container">
                                @if($institution->institutionClasses && $institution->institutionClasses->count() > 0)
                                    @foreach($institution->institutionClasses as $index => $instClass)
                                        <div class="class-item mb-3 p-3 border rounded" data-index="{{ $index }}">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <label>Class Name</label>
                                                    <select name="classes[{{ $index }}][class_name]" class="form-control class-select" required>
                                                        <option value="">Select Class</option>
                                                        <option value="Nursery" {{ $instClass->class_name == 'Nursery' ? 'selected' : '' }}>Nursery</option>
                                                        <option value="LKG" {{ $instClass->class_name == 'LKG' ? 'selected' : '' }}>LKG</option>
                                                        <option value="UKG" {{ $instClass->class_name == 'UKG' ? 'selected' : '' }}>UKG</option>
                                                        <option value="Class 1" {{ $instClass->class_name == 'Class 1' ? 'selected' : '' }}>Class 1</option>
                                                        <option value="Class 2" {{ $instClass->class_name == 'Class 2' ? 'selected' : '' }}>Class 2</option>
                                                        <option value="Class 3" {{ $instClass->class_name == 'Class 3' ? 'selected' : '' }}>Class 3</option>
                                                        <option value="Class 4" {{ $instClass->class_name == 'Class 4' ? 'selected' : '' }}>Class 4</option>
                                                        <option value="Class 5" {{ $instClass->class_name == 'Class 5' ? 'selected' : '' }}>Class 5</option>
                                                        <option value="Class 6" {{ $instClass->class_name == 'Class 6' ? 'selected' : '' }}>Class 6</option>
                                                        <option value="Class 7" {{ $instClass->class_name == 'Class 7' ? 'selected' : '' }}>Class 7</option>
                                                        <option value="Class 8" {{ $instClass->class_name == 'Class 8' ? 'selected' : '' }}>Class 8</option>
                                                        <option value="Class 9" {{ $instClass->class_name == 'Class 9' ? 'selected' : '' }}>Class 9</option>
                                                        <option value="Class 10" {{ $instClass->class_name == 'Class 10' ? 'selected' : '' }}>Class 10</option>
                                                        <option value="Class 11" {{ $instClass->class_name == 'Class 11' ? 'selected' : '' }}>Class 11</option>
                                                        <option value="Class 12" {{ $instClass->class_name == 'Class 12' ? 'selected' : '' }}>Class 12</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-5">
                                                    <label>Total Strength</label>
                                                    <input type="number" name="classes[{{ $index }}][strength]" class="form-control" value="{{ $instClass->total_strength }}" min="1" required>
                                                </div>
                                                <div class="col-md-2 d-flex align-items-end">
                                                    <button type="button" class="btn btn-danger btn-sm remove-class-btn">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn btn-success btn-sm mt-2" id="add-class-btn">
                                <i class="fas fa-plus"></i> Add Another Class
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
                            <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number', $institution->contact_number) }}" placeholder="Enter contact number" required>
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
                            <input
                                type="text"
                                name="block_id"
                                class="form-control"
                                id="block-input"
                                placeholder="Enter block name"
                                value="{{ old('block_id', optional($institution->block)->name ?? $institution->block_id) }}">
                            @error('block_id')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-mail-bulk form-icon"></i>
                                Pincode <span class="required">*</span>
                            </label>
                            <input type="text" name="pincode" class="form-control" id="pincode-input" value="{{ old('pincode', $institution->pincode) }}" placeholder="Enter pincode" required>
                            @error('pincode')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-check-circle form-icon"></i>
                                Status <span class="required">*</span>
                            </label>
                            <select name="status" class="form-control" required>
                                <option value="1" {{ (old('status', $institution->status) == 1) ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ (old('status', $institution->status) == 0) ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group text-center mt-4">
                        <a href="{{ url('admin/institution-managements') }}" class="btn-cancel">
                            <i class="fas fa-arrow-left"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i>
                            Update Institution
                        </button>
                    </div>
                </form>
            </div>
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
                url: '{{ route("institution_classes") }}',
                type: 'GET',
                data: { type: type },
                dataType: 'json',
                success: function(response) {
                    console.log('AJAX Success:', response);
                    classSelect.empty();
                    classSelect.append('<option value="">Select Class</option>');

                    if (Array.isArray(response)) {
                        $.each(response, function(index, className) {
                            classSelect.append('<option value="' + className + '">' + className + '</option>');
                        });
                    }

                    // Set old value or current institution class
                    var currentClass = '{{ old("class", $institution->class) }}';
                    if (currentClass) {
                        classSelect.val(currentClass);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Error Details:');
                    console.log('Status:', status);
                    console.log('Error:', error);
                    console.log('Response Text:', xhr.responseText);
                    console.log('Status Code:', xhr.status);
                    console.log('URL:', '{{ route("institution_classes") }}');
                    console.log('Type:', type);

                    // Try to show a more helpful error message
                    var errorMessage = 'Error loading classes. ';
                    if (xhr.status === 404) {
                        errorMessage += 'Route not found. Please check if you are logged in as admin.';
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

    // Trigger change event on page load if type is school
    if ($('select[name="type"]').val() === 'school') {
        $('select[name="type"]').trigger('change');
    }

    // Add new class row
    var classIndex = {{ $institution->institutionClasses ? $institution->institutionClasses->count() : 0 }};
    var availableClasses = ['Nursery', 'LKG', 'UKG', 'Class 1', 'Class 2', 'Class 3', 'Class 4', 'Class 5', 'Class 6', 'Class 7', 'Class 8', 'Class 9', 'Class 10', 'Class 11', 'Class 12'];

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
        return new Promise(function(resolve, reject) {
            $.ajax({
                url: '{{ route("institution_countries") }}',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    var countrySelect = $('#country-select');
                    countrySelect.empty();
                    countrySelect.append('<option value="">Select Country</option>');

                    $.each(response, function(key, value) {
                        countrySelect.append('<option value="' + key + '">' + value + '</option>');
                    });

                    resolve();
                },
                error: function(xhr, status, error) {
                    console.log('Error loading countries:', error);
                    reject(error);
                }
            });
        });
    }

    // Load states based on country
    function loadStates(country) {
        if (!country) {
            $('#state-select').empty().append('<option value="">Select State</option>');
            $('#district-select').empty().append('<option value="">Select District</option>');
            // $('#city-select').empty().append('<option value="">Select City</option>');
            $('#block-input').val('');
            return Promise.resolve();
        }

        return new Promise(function(resolve, reject) {
            $.ajax({
                url: '{{ route("institution_states") }}',
                type: 'GET',
                data: { country: country },
                dataType: 'json',
                success: function(response) {
                    var stateSelect = $('#state-select');
                    stateSelect.empty();
                    stateSelect.append('<option value="">Select State</option>');

                    $.each(response, function(key, value) {
                        stateSelect.append('<option value="' + key + '">' + value + '</option>');
                    });

                    // Clear dependent dropdowns
                    $('#district-select').empty().append('<option value="">Select District</option>');
                    // $('#city-select').empty().append('<option value="">Select City</option>');
                    $('#block-input').val('');

                    resolve();
                },
                error: function(xhr, status, error) {
                    console.log('Error loading states:', error);
                    reject(error);
                }
            });
        });
    }

    // Load districts based on state
    function loadDistricts(state) {
        if (!state) {
            $('#district-select').empty().append('<option value="">Select District</option>');
            // $('#city-select').empty().append('<option value="">Select City</option>');
            $('#block-input').val('');
            return Promise.resolve();
        }

        return new Promise(function(resolve, reject) {
            $.ajax({
                url: '{{ route("institution_districts") }}',
                type: 'GET',
                data: { state: state },
                dataType: 'json',
                success: function(response) {
                    var districtSelect = $('#district-select');
                    districtSelect.empty();
                    districtSelect.append('<option value="">Select District</option>');

                    $.each(response, function(key, value) {
                        districtSelect.append('<option value="' + key + '">' + value + '</option>');
                    });

                    // Clear dependent dropdowns
                    // $('#city-select').empty().append('<option value="">Select City</option>');
                    $('#block-input').val('');

                    resolve();
                },
                error: function(xhr, status, error) {
                    console.log('Error loading districts:', error);
                    reject(error);
                }
            });
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
        $('#block-input').val('');
    });

    // Load countries and populate form with current values
    async function initializeForm() {
        try {
            // Load countries first
            await loadCountries();

            // Get current values
            var currentCountry = '{{ old("country_id", $institution->country_id ?? "") }}';
            var currentState = '{{ old("state_id", $institution->state_id ?? "") }}';
            var currentDistrict = '{{ old("district_id", $institution->district_id ?? "") }}';
            // var currentCity = '{{ old("city_id", $institution->city_id ?? "") }}';
            var currentBlock = '{{ old("block_id", optional($institution->block)->name ?? $institution->block_id) }}';

            // Set country and load states
            if (currentCountry) {
                $('#country-select').val(currentCountry);
                await loadStates(currentCountry);

                // Set state and load districts
                if (currentState) {
                    $('#state-select').val(currentState);
                    await loadDistricts(currentState);

                    // Set district
                    if (currentDistrict) {
                        $('#district-select').val(currentDistrict);
                    }
                    }
                }

            if (currentBlock) {
                $('#block-input').val(currentBlock);
            }
        } catch (error) {
            console.log('Error initializing form:', error);
        }
    }

    // Initialize the form
    initializeForm();
});
</script>

@endsection
