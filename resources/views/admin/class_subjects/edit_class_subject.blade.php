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
                                <input type="hidden" name="subcategory_id" value="{{ $subCategory->id }}">

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
                                {{-- Subcategory is fixed by ID but we can show it --}}

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
            // When Section changes: load Categories via AJAX
            $('#section_id').on('change', function() {
                var section_id = $(this).val();
                if (section_id != "") {
                    $.ajax({
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        type: "get",
                        url: "{{ url('admin/append-categories-level') }}",
                        data: {
                            section_id: section_id
                        },
                        success: function(resp) {
                            $("#appendCategoriesLevel").html(resp);
                        },
                        error: function() {
                            alert("Error loading categories. Please try again.");
                        }
                    });
                }
            });
        });
    </script>
@endpush
