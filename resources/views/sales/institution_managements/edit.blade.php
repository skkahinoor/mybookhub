@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
                    <form method="post" action="{{ url('sales/institution-managements/' . $institution->id) }}">
                        @csrf
                        @method('PUT')

                        {{-- ── Row 1: Name + Type ── --}}
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
                                <select name="type" class="form-control" id="type-select" required>
                                    <option value="">Select Type</option>
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->id }}"
                                            {{ old('type', $institution->type) == $section->id ? 'selected' : '' }}>
                                            {{ $section->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- ── Row 2: Board + Classes ── --}}
                        <div class="form-row">
                            <div class="form-group" id="board-field" style="display: none;">
                                <label class="form-label">
                                    <i class="fas fa-certificate form-icon"></i>
                                    Board <span class="required">*</span>
                                </label>
                                <select name="board" class="form-control" id="board-select" required>
                                    <option value="">Select Board</option>
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
                                    {{-- Existing classes pre-rendered; JS will replace selects with real options --}}
                                    @foreach ($institution->institutionClasses as $idx => $instClass)
                                        <div class="class-item mb-3 p-3 border rounded" data-index="{{ $idx }}">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <label>Subcategory (Class)</label>
                                                    <select name="classes[{{ $idx }}][sub_category_id]"
                                                        class="form-control class-select preloaded-class"
                                                        data-selected="{{ $instClass->sub_category_id }}" required>
                                                        {{-- Options filled by JS after board loads --}}
                                                        <option value="{{ $instClass->sub_category_id }}">
                                                            {{ optional($instClass->subcategory)->subcategory_name ?? 'Class #' . $instClass->sub_category_id }}
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="col-md-5">
                                                    <label>Total Strength</label>
                                                    <input type="number" name="classes[{{ $idx }}][strength]"
                                                        class="form-control" value="{{ $instClass->total_strength }}"
                                                        min="1" required>
                                                </div>
                                                <div class="col-md-2 d-flex align-items-end">
                                                    <button type="button" class="btn btn-danger btn-sm remove-class-btn">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-success btn-sm mt-2" id="add-class-btn">
                                    <i class="fas fa-plus"></i> Add Class
                                </button>
                                @error('classes')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- ── Row 3: Principal Name ── --}}
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user form-icon"></i>
                                    Principal Name <span class="required">*</span>
                                </label>
                                <input type="text" name="principal_name" class="form-control"
                                    value="{{ old('principal_name', $institution->principal_name) }}"
                                    placeholder="Enter principal name" required>
                                @error('principal_name')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

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
                        </div>

                        {{-- ── Row 4: Country + State + District ── --}}
                        <div class="form-row">
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
                        </div>

                        {{-- ── Row 5: Block + Pincode ── --}}
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-cube form-icon"></i>
                                    Block
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
                                <input type="text" name="pincode" class="form-control" id="pincode-input"
                                    value="{{ old('pincode', $institution->pincode) }}" placeholder="Enter pincode"
                                    required>
                                @error('pincode')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- ── Buttons ── --}}
                        <div class="form-group text-center mt-4">
                            <a href="{{ url('sales/institution-managements') }}" class="btn-cancel">
                                <i class="fas fa-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn-submit">
                                <i class="fas fa-save"></i> Update Institution
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    @php
        $existingClassesForJs = $institution->institutionClasses->map(function ($c) {
            return [
                'sub_category_id' => $c->sub_category_id,
                'strength' => $c->total_strength,
                'name' => optional($c->subcategory)->subcategory_name ?? 'Class #' . $c->sub_category_id,
            ];
        })->values()->all();
    @endphp
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {

            /* ── PHP → JS existing values ── */
            var existingType = '{{ old('type', $institution->type) }}';
            var existingBoard = '{{ old('board', $institution->board) }}';
            var existingCountry = '{{ old('country_id', $institution->country_id ?? '') }}';
            var existingState = '{{ old('state_id', $institution->state_id ?? '') }}';
            var existingDistrict = '{{ old('district_id', $institution->district_id ?? '') }}';
            var existingBlock = '{{ old('block_id', $institution->block_id ?? '') }}';

            /* Existing classes as a JS array */
            var existingClasses = @json($existingClassesForJs);

            var currentSubcategories = [];
            var classIndex = existingClasses.length;

            /* ════════════════════════════════
               Helper: build options html
            ════════════════════════════════ */
            function subcatOptions(selectedId) {
                var html = '<option value="">Select Subcategory</option>';
                $.each(currentSubcategories, function(i, sub) {
                    var sel = (sub.id == selectedId) ? 'selected' : '';
                    html += `<option value="${sub.id}" ${sel}>${sub.subcategory_name}</option>`;
                });
                return html;
            }

            /* ════════════════════════════════
               Institution Type → load boards
            ════════════════════════════════ */
            function loadCategories(sectionId, callback) {
                if (!sectionId) {
                    $('#board-field').hide();
                    $('#class-field').hide();
                    return;
                }
                $.ajax({
                    url: '{{ route('sales.institution.categories') }}',
                    data: {
                        section_id: sectionId
                    },
                    success: function(cats) {
                        var boardSel = $('#board-select');
                        boardSel.empty().append('<option value="">Select Board</option>');
                        $.each(cats, function(i, cat) {
                            var sel = (cat.id == existingBoard) ? 'selected' : '';
                            boardSel.append(
                                `<option value="${cat.id}" ${sel}>${cat.category_name}</option>`
                            );
                        });
                        $('#board-field').show();
                        if (callback) callback();
                    }
                });
            }

            /* ════════════════════════════════
               Board → load subcategories
            ════════════════════════════════ */
            function loadAllClasses(callback) {
                $.ajax({
                    url: '{{ route('sales.institution.classes') }}',
                    success: function(subs) {
                        currentSubcategories = subs;
                        $('#class-field').show();
                        /* Update any already-rendered (preloaded) selects */
                        $('.preloaded-class').each(function() {
                            var selectedId = $(this).data('selected');
                            $(this).html(subcatOptions(selectedId));
                            $(this).removeClass('preloaded-class');
                        });
                        if (callback) callback();
                    }
                });
            }

            /* ════════════════════════════════
               Render a new class row
            ════════════════════════════════ */
            function addClassRow(selectedSubId, strength) {
                var optHtml = subcatOptions(selectedSubId || '');
                var html = `
            <div class="class-item mb-3 p-3 border rounded" data-index="${classIndex}">
                <div class="row">
                    <div class="col-md-5">
                        <label>Subcategory (Class)</label>
                        <select name="classes[${classIndex}][sub_category_id]"
                                class="form-control class-select" required>
                            ${optHtml}
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label>Total Strength</label>
                        <input type="number" name="classes[${classIndex}][strength]"
                               class="form-control" value="${strength || ''}"
                               placeholder="e.g., 50" min="1" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm remove-class-btn">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>`;
                $('#class-list-container').append(html);
                classIndex++;
            }

            /* ════════════════════════════════
               Wire up change events
            ════════════════════════════════ */
            $('#type-select').on('change', function() {
                var sectionId = $(this).val();
                loadCategories(sectionId);
            });

            $('#board-select').on('change', function() {
                var catId = $(this).val();
                if (catId) {
                    $('#class-field').show();
                } else {
                    $('#class-field').hide();
                }
            });

            $('#add-class-btn').on('click', function() {
                if (currentSubcategories.length === 0) {
                    alert('Please wait for classes to load.');
                    return;
                }
                addClassRow('', '');
            });

            $(document).on('click', '.remove-class-btn', function() {
                $(this).closest('.class-item').remove();
            });

            /* ════════════════════════════════
               Init: pre-populate type → board → classes
            ════════════════════════════════ */
            if (existingType) {
                loadCategories(existingType);
            }

            loadAllClasses();

            /* ════════════════════════════════
               Location cascading
            ════════════════════════════════ */
            function loadCountries(callback) {
                $.ajax({
                    url: '{{ route('institution_countries') }}',
                    success: function(resp) {
                        var sel = $('#country-select');
                        sel.empty().append('<option value="">Select Country</option>');
                        $.each(resp, function(key, val) {
                            sel.append(
                                `<option value="${key}" ${key == existingCountry ? 'selected' : ''}>${val}</option>`
                            );
                        });
                        if (callback) callback();
                    }
                });
            }

            function loadStates(country, callback) {
                if (!country) return;
                $.ajax({
                    url: '{{ route('institution_states') }}',
                    data: {
                        country: country
                    },
                    success: function(resp) {
                        var sel = $('#state-select');
                        sel.empty().append('<option value="">Select State</option>');
                        $.each(resp, function(key, val) {
                            sel.append(
                                `<option value="${key}" ${key == existingState ? 'selected' : ''}>${val}</option>`
                            );
                        });
                        if (callback) callback();
                    }
                });
            }

            function loadDistricts(state, callback) {
                if (!state) return;
                $.ajax({
                    url: '{{ route('institution_districts') }}',
                    data: {
                        state: state
                    },
                    success: function(resp) {
                        var sel = $('#district-select');
                        sel.empty().append('<option value="">Select District</option>');
                        $.each(resp, function(key, val) {
                            sel.append(
                                `<option value="${key}" ${key == existingDistrict ? 'selected' : ''}>${val}</option>`
                            );
                        });
                        if (callback) callback();
                    }
                });
            }

            function loadBlocks(district, callback) {
                if (!district) return;
                $.ajax({
                    url: '{{ route('institution_blocks') }}',
                    data: {
                        district: district
                    },
                    success: function(resp) {
                        var sel = $('#block-select');
                        sel.empty().append('<option value="">Select Block</option>');
                        $.each(resp, function(key, val) {
                            sel.append(
                                `<option value="${key}" ${key == existingBlock ? 'selected' : ''}>${val}</option>`
                            );
                        });
                        if (callback) callback();
                    }
                });
            }

            /* Live change handlers */
            $('#country-select').on('change', function() {
                existingState = '';
                existingDistrict = '';
                existingBlock = '';
                loadStates($(this).val());
            });

            $('#state-select').on('change', function() {
                existingDistrict = '';
                existingBlock = '';
                loadDistricts($(this).val());
            });

            $('#district-select').on('change', function() {
                existingBlock = '';
                loadBlocks($(this).val());
            });

            /* Init location chain */
            loadCountries(function() {
                if (existingCountry) {
                    loadStates(existingCountry, function() {
                        if (existingState) {
                            loadDistricts(existingState, function() {
                                if (existingDistrict) {
                                    loadBlocks(existingDistrict);
                                }
                            });
                        }
                    });
                }
            });
        });
    </script>
@endsection
