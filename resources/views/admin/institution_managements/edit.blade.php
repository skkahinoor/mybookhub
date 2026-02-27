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
                box-shadow: 0 6px 22px rgba(39, 68, 114, 0.10);
            }

            .form-container {
                background: #ffffff;
                border-radius: 12px;
                box-shadow: 0 4px 18px rgba(60, 72, 100, 0.11);
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
                box-shadow: 0 0 6px rgba(39, 68, 114, 0.11);
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

            .btn-submit,
            .btn-cancel {
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

                .form-container,
                .page-header {
                    padding: 20px 10px;
                }

                .page-title {
                    font-size: 1.3rem;
                }
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

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="form-container">
                    <form method="post" action="{{ url('admin/institution-managements/' . $institution->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-school form-icon"></i>
                                    Institution Name <span class="required">*</span>
                                </label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', $institution->name) }}" placeholder="Enter institution name"
                                    required>
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
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->id }}"
                                            {{ old('type', $institution->type) == $section->id ? 'selected' : '' }}>
                                            {{ $section->name }}</option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group" id="board-field" style="display: none;">
                                <label class="form-label">
                                    <i class="fas fa-certificate form-icon"></i>
                                    Board <span class="required">*</span>
                                </label>
                                <select name="board" class="form-control" required>
                                    <option value="">Select Board</option>
                                </select>
                                @error('board')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group" id="class-field"
                                style="{{ $institution->institutionClasses && $institution->institutionClasses->count() > 0 ? 'display:block;' : 'display:none;' }}">
                                <label class="form-label">
                                    <i class="fas fa-layer-group form-icon"></i>
                                    Add Class with Strength
                                </label>
                                <div id="class-list-container">
                                    @if ($institution->institutionClasses && $institution->institutionClasses->count() > 0)
                                        @foreach ($institution->institutionClasses as $index => $instClass)
                                            <div class="class-item mb-3 p-3 border rounded"
                                                data-index="{{ $index }}">
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <label>Subcategory (Class)</label>
                                                        <select name="classes[{{ $index }}][sub_category_id]"
                                                            class="form-control class-select" required>
                                                            <option value="">Select Subcategory</option>
                                                            @foreach ($subcategories as $subcategory)
                                                                <option value="{{ $subcategory->id }}"
                                                                    {{ $instClass->sub_category_id == $subcategory->id ? 'selected' : '' }}>
                                                                    {{ $subcategory->subcategory_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <label>Total Strength</label>
                                                        <input type="number" name="classes[{{ $index }}][strength]"
                                                            class="form-control" value="{{ $instClass->total_strength }}"
                                                            min="1" required>
                                                    </div>
                                                    <div class="col-md-2 d-flex align-items-end">
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm remove-class-btn">
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


                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user form-icon"></i>
                                    Principal Name<span class="required">*</span>
                                </label>
                                <input type="text" name="principal_name" class="form-control"
                                    value="{{ $institution->principal_name ?? old('principal_name') }}" placeholder="Enter principal name" required>
                                @error('principal_name')
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
                                    value="{{ old('contact_number', $institution->contact_number) }}"
                                    placeholder="Enter contact number" required>
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
                                <select name="block_id" class="form-control" id="block-select" required>
                                    <option value="">Select Block</option>
                                </select>
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
                                <input type="text" name="pincode" class="form-control" id="pincode-input"
                                    value="{{ old('pincode', $institution->pincode) }}" placeholder="Enter pincode"
                                    required>
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
                                    <option value="1"
                                        {{ old('status', $institution->status) == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0"
                                        {{ old('status', $institution->status) == 0 ? 'selected' : '' }}>Inactive
                                    </option>
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
            var currentSubcategories = [];
            var classIndex =
                {{ $institution->institutionClasses ? $institution->institutionClasses->count() : 0 }};

            // Fetch sections on load
            function loadSections() {
                $.ajax({
                    url: '{{ route('admin.institution.sections') }}',
                    type: 'GET',
                    success: function(response) {
                        var typeSelect = $('select[name="type"]');
                        var selectedType = '{{ old('type', $institution->type) }}';
                        typeSelect.empty().append('<option value="">Select Type</option>');
                        $.each(response, function(index, section) {
                            typeSelect.append(
                                `<option value="${section.id}" ${selectedType == section.id ? 'selected' : ''}>${section.name}</option>`
                            );
                        });
                        if (selectedType) typeSelect.trigger('change');
                    }
                });
            }

            // Handle institution type change -> Load Boards (Categories)
            $('select[name="type"]').change(function() {
                var section_id = $(this).val();
                var boardSelect = $('select[name="board"]');
                var boardField = $('#board-field');
                var selectedBoard = '{{ old('board', $institution->board) }}';

                if (section_id) {
                    if (boardField.length) boardField.show();
                    boardSelect.empty().append('<option value="">Loading...</option>');
                    $.ajax({
                        url: '{{ route('admin.institution.categories') }}',
                        type: 'GET',
                        data: {
                            section_id: section_id
                        },
                        success: function(response) {
                            boardSelect.empty().append(
                                '<option value="">Select Board</option>');
                            $.each(response, function(index, category) {
                                boardSelect.append(
                                    `<option value="${category.id}" ${selectedBoard == category.id ? 'selected' : ''}>${category.category_name}</option>`
                                );
                            });
                            $('#class-field').show();
                            if (selectedBoard) {
                                boardSelect.trigger('change');
                            }
                        }
                    });
                } else {
                    if (boardField.length) boardField.hide();
                    boardSelect.empty().append('<option value="">Select Board</option>');
                    $('#class-field').hide();
                }
            });

            // Handle board change (No longer loads classes but can still be used for other logic if needed)
            $('select[name="board"]').change(function() {
                // If you need any logic when board changes, add it here
            });

            // Add new class row
            $('#add-class-btn').on('click', function() {
                if (currentSubcategories.length === 0) {
                    alert('No classes available.');
                    return;
                }

                var optionsHtml = '<option value="">Select Subcategory</option>';
                $.each(currentSubcategories, function(index, sub) {
                    optionsHtml += `<option value="${sub.id}">${sub.subcategory_name}</option>`;
                });

                var classHtml = `
                    <div class="class-item mb-3 p-3 border rounded" data-index="${classIndex}">
                        <div class="row">
                            <div class="col-md-5">
                                <label>Subcategory (Class)</label>
                                <select name="classes[${classIndex}][sub_category_id]" class="form-control class-select" required>
                                    ${optionsHtml}
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

            $(document).on('click', '.remove-class-btn', function() {
                $(this).closest('.class-item').remove();
            });

            // Location cascading logic
            function loadCountries() {
                $.ajax({
                    url: '{{ route('admin.institution.countries') }}',
                    type: 'GET',
                    success: function(response) {
                        var countrySelect = $('#country-select');
                        countrySelect.empty().append('<option value="">Select Country</option>');
                        var selectedCountry = '{{ old('country_id', $institution->country_id) }}';
                        $.each(response, function(key, value) {
                            countrySelect.append(
                                `<option value="${key}" ${selectedCountry == key ? 'selected' : ''}>${value}</option>`
                            );
                        });
                        if (selectedCountry) countrySelect.trigger('change');
                    }
                });
            }

            $('#country-select').change(function() {
                var country = $(this).val();
                if (country) {
                    $.ajax({
                        url: '{{ route('admin.institution.states') }}',
                        type: 'GET',
                        data: {
                            country: country
                        },
                        success: function(response) {
                            var stateSelect = $('#state-select');
                            stateSelect.empty().append(
                                '<option value="">Select State</option>');
                            var selectedState =
                                '{{ old('state_id', $institution->state_id) }}';
                            $.each(response, function(key, value) {
                                stateSelect.append(
                                    `<option value="${key}" ${selectedState == key ? 'selected' : ''}>${value}</option>`
                                );
                            });
                            if (selectedState) stateSelect.trigger('change');
                        }
                    });
                }
            });

            $('#state-select').change(function() {
                var state = $(this).val();
                if (state) {
                    $.ajax({
                        url: '{{ route('admin.institution.districts') }}',
                        type: 'GET',
                        data: {
                            state: state
                        },
                        success: function(response) {
                            var districtSelect = $('#district-select');
                            districtSelect.empty().append(
                                '<option value="">Select District</option>');
                            var selectedDistrict =
                                '{{ old('district_id', $institution->district_id) }}';
                            $.each(response, function(key, value) {
                                districtSelect.append(
                                    `<option value="${key}" ${selectedDistrict == key ? 'selected' : ''}>${value}</option>`
                                );
                            });
                            if (selectedDistrict) districtSelect.trigger('change');
                        }
                    });
                }
            });

            $('#district-select').change(function() {
                var district = $(this).val();
                if (district) {
                    $.ajax({
                        url: '{{ route('admin.institution.blocks') }}',
                        type: 'GET',
                        data: {
                            district: district
                        },
                        success: function(response) {
                            var blockSelect = $('#block-select');
                            blockSelect.empty().append(
                                '<option value="">Select Block</option>');
                            var selectedBlock =
                                '{{ old('block_id', $institution->block_id) }}';
                            $.each(response, function(key, value) {
                                blockSelect.append(
                                    `<option value="${key}" ${selectedBlock == key ? 'selected' : ''}>${value}</option>`
                                );
                            });
                        }
                    });
                }
            });

            function loadClasses() {
                $.ajax({
                    url: '{{ route('admin.institution.classes') }}',
                    type: 'GET',
                    success: function(response) {
                        currentSubcategories = response;
                    }
                });
            }

            loadSections();
            loadClasses();
            loadCountries();
        });
    </script>

@endsection
