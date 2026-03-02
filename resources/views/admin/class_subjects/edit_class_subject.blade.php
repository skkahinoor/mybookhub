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
                            <h4 class="card-title">Class: {{ $subCategory->subcategory_name }}</h4>
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form class="forms-sample" action="{{ route('admin.class_subjects.update', $subCategory->id) }}"
                                method="POST">
                                @csrf

                                <div class="form-group">
                                    <label for="section_id">Select Section</label>
                                    <select name="section_id" id="section_id" class="form-control" required>
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
                                        <select name="category_id" id="category_id" class="form-control" required>
                                            <option value="">Select Category</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ $currentCategoryId == $category->id ? 'selected' : '' }}>
                                                    {{ $category->category_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div id="appendSubcategoriesLevel">
                                    <div class="form-group">
                                        <label for="subcategory_id">Select Class (Subcategory)</label>
                                        <select name="subcategory_id" id="subcategory_id" class="form-control" required>
                                            <option value="">Select Class</option>
                                            @foreach ($subcategories as $subcategory)
                                                <option value="{{ $subcategory->id }}"
                                                    {{ $subCategory->id == $subcategory->id ? 'selected' : '' }}>
                                                    {{ $subcategory->subcategory_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Select Subjects</label>
                                    <div class="row">
                                        @foreach ($subjects as $subject)
                                            <div class="col-md-4">
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
        });
    </script>
    {{-- Generic section/category handlers are now in public/admin/js/custom.js --}}
@endpush
