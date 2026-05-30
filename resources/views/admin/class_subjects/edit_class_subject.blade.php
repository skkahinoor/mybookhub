@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-bold">Update Class Subjects Assignment</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Class: {{ $subCategory ? $subCategory->subcategory_name : 'N/A' }}</h4>
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form class="forms-sample" action="{{ route('admin.class_subjects.update', $subCategory ? $subCategory->id : 0) }}"
                                method="POST">
                                @csrf

                                <div class="form-group">
                                    <label for="section_id">Select Section</label>
                                    <input type="hidden" name="section_id" value="{{ $currentSectionId }}">
                                    <select id="section_id" class="form-control" disabled>
                                        <option value="">Select Section</option>
                                        @foreach ($sections as $section)
                                            <option value="{{ $section->id }}"
                                                {{ $currentSectionId == $section->id ? 'selected' : '' }}>
                                                {{ $section->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="appendCategoriesLevel">
                                    <div class="form-group">
                                        <label for="category_id">Select Category</label>
                                        <input type="hidden" name="category_id" value="{{ $currentCategoryId }}">
                                        <select id="category_id" class="form-control" disabled>
                                            <option value="">Select Category</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ $currentCategoryId == $category->id ? 'selected' : '' }}>
                                                    {{ $category->category_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div id="appendSubcategoriesLevel" style="display: {{ $subCategory ? 'block' : 'none' }}">
                                    <div class="form-group">
                                        <label for="subcategory_id">Select Class (Subcategory)</label>
                                        <input type="hidden" name="subcategory_id" value="{{ $subCategory ? $subCategory->id : '' }}">
                                        <select id="subcategory_id" class="form-control" disabled>
                                            <option value="">Select Class</option>
                                            @foreach ($subcategories as $subcategory)
                                                <option value="{{ $subcategory->id }}"
                                                    {{ ($subCategory && $subCategory->id == $subcategory->id) ? 'selected' : '' }}>
                                                    {{ $subcategory->subcategory_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" id="subjects-container" style="display: {{ $subCategory ? 'block' : 'none' }}">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="mb-0">Select Subjects</label>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-xs btn-outline-primary" id="selectAllFiltered" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Select All Visible</button>
                                            <button type="button" class="btn btn-xs btn-outline-secondary" id="deselectAllFiltered" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; margin-left: 5px;">Deselect All Visible</button>
                                        </div>
                                    </div>
                                    <div class="input-group mb-3">
                                        <input type="text" id="search-subjects" class="form-control" placeholder="Search subjects here...">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="clear-search"><i class="mdi mdi-close"></i></button>
                                        </div>
                                    </div>
                                    <div class="row" id="subjects-list" style="max-height: 250px; overflow-y: auto; border: 1px solid #e3e3e3; padding: 15px; border-radius: 4px; margin: 0 1px 15px 1px; background-color: #fcfcfc;">
                                        @foreach ($subjects as $subject)
                                            <div class="col-md-4 subject-item">
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" name="subject_ids[]"
                                                            value="{{ $subject->id }}" class="form-check-input"
                                                            {{ in_array($subject->id, $assignedSubjectIds) ? 'checked' : '' }}>
                                                        {{ $subject->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary mr-2">Update Assignment</button>
                                <a href="{{ route('admin.class_subjects.index') }}" class="btn btn-light">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Handle Category change to load Classes (Subcategories)
            // We use delegated event because #category_id might be reloaded via AJAX
            $(document).on('change', '#category_id', function(e) {
                var category_id = $(this).val();
                if ($("#appendSubcategoriesLevel").length > 0 && category_id != "") {
                    // Get currently selected subcategory ID to preserve it if possible
                    var selected_id = $("#subcategory_id").val();
                    $.ajax({
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        type: "get",
                        url: "{{ url('admin/append-subcategories-level') }}",
                        data: {
                            category_id: category_id,
                            selected_id: selected_id
                        },
                        success: function(resp) {
                            $("#appendSubcategoriesLevel").html(resp);
                        },
                        error: function() {
                            alert("Error loading classes");
                        }
                    });
                }
            });
            // Live search for subjects
            $('#search-subjects').on('keyup input', function() {
                var value = $(this).val().toLowerCase();
                var visibleCount = 0;
                
                $('#subjects-list .subject-item').each(function() {
                    var text = $(this).text().toLowerCase();
                    if (text.indexOf(value) > -1) {
                        $(this).show();
                        visibleCount++;
                    } else {
                        $(this).hide();
                    }
                });

                if (visibleCount === 0) {
                    if ($('#no-subjects-found').length === 0) {
                        $('#subjects-list').append('<div id="no-subjects-found" class="col-12 text-muted text-center py-3">No matching subjects found</div>');
                    }
                } else {
                    $('#no-subjects-found').remove();
                }
            });

            // Clear search field
            $('#clear-search').on('click', function() {
                $('#search-subjects').val('').trigger('input');
            });

            // Select all visible subjects
            $('#selectAllFiltered').on('click', function() {
                $('#subjects-list .subject-item:visible').find('input[type="checkbox"]').prop('checked', true);
            });

            // Deselect all visible subjects
            $('#deselectAllFiltered').on('click', function() {
                $('#subjects-list .subject-item:visible').find('input[type="checkbox"]').prop('checked', false);
            });
        });
    </script>
    {{-- Generic section/category handlers are now in public/admin/js/custom.js --}}
@endpush
