@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Class Subjects Assignment</h4>

                            <a href="{{ route('admin.class_subjects.create') }}"
                                style="max-width: 200px; float: right; display: inline-block"
                                class="btn btn-block btn-primary"><i class="mdi mdi-plus"></i> Assign Subjects</a>

                            @if (Session::has('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success:</strong> {{ Session::get('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="table-responsive pt-3">
                                <table id="class_subjects" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Section</th>
                                            <th>Category</th>
                                            <th>Class Name</th>
                                            <th>Subject</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($assignments as $key => $assignment)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $assignment->section_name }}</td>
                                                <td>{{ $assignment->category_name }}</td>
                                                <td>{{ $assignment->subcategory_name }}</td>
                                                <td>{{ $assignment->subject_names }}</td>
                                                <td>
                                                    <a
                                                        href="{{ route('admin.class_subjects.edit', $assignment->sub_category_id) }}">
                                                        <i style="font-size: 25px" class="mdi mdi-pencil-box"></i>
                                                    </a>
                                                    {{-- Note: This will delete ALL subjects assigned to this Class --}}
                                                    <a title="Delete All Assigned Subjects" class="confirmDelete"
                                                        data-module="Assignment"
                                                        data-url="{{ route('admin.class_subjects.delete', $assignment->sub_category_id) }}">
                                                        <i style="font-size: 25px"
                                                            class="mdi mdi-delete-forever text-danger"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
                <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2026 All rights
                    reserved.</span>
            </div>
        </footer>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#class_subjects').DataTable();
        });
    </script>
@endpush
