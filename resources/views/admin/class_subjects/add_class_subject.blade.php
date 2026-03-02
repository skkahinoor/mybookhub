@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-bold">Assign Subjects to Class</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Select Class and Subjects</h4>
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form class="forms-sample" action="{{ route('admin.class_subjects.store') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="section_id">Select Section</label>
                                    <select name="section_id" id="section_id" class="form-control" required>
                                        <option value="">Select Section</option>
                                        @foreach ($sections as $section)
                                            <option value="{{ $section->id }}">{{ $section->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="appendCategoriesLevel">
                                    {{-- Categories will be loaded here via AJAX --}}
                                </div>
                                <div id="appendSubcategoriesLevel">
                                    {{-- Subcategories will be loaded here via AJAX --}}
                                </div>
                                <div class="form-group">
                                    <label>Select Subjects</label>
                                    <div class="row">
                                        @foreach ($subjects as $subject)
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" name="subject_ids[]"
                                                            value="{{ $subject->id }}" class="form-check-input">
                                                        {{ $subject->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary mr-2">Assign Subjects</button>
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
            // We use delegated event because #category_id is loaded via AJAX
            $(document).on('change', '#category_id', function(e) {
                // If this page is Class-Subject Assignment, we load subcategories
                // instead of filters (which custom.js might try to do correctly if we check for target)
                var category_id = $(this).val();
                if ($("#appendSubcategoriesLevel").length > 0 && category_id != "") {
                    $.ajax({
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        type: "get",
                        url: "{{ url('admin/append-subcategories-level') }}",
                        data: {
                            category_id: category_id
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
@endpush
