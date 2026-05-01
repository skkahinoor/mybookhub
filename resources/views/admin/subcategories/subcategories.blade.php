@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Classes</h4>

                            @php
                                $adminType = Auth::guard('admin')->user()->type;
                                $prefix = $adminType === 'vendor' ? 'vendor' : 'admin';
                            @endphp
                            <a href="{{ url($prefix . '/add-edit-subcategory') }}"
                                style="max-width: 150px; float: right; display: inline-block"
                                class="btn btn-block btn-primary"><i class="mdi mdi-plus"></i> Add Class</a>

                            @if (Session::has('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success:</strong> {{ Session::get('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="table-responsive pt-3">
                                <table id="subcategories" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Icon</th>
                                            <th>Class Name</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.layout.footer')
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#subcategories').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ url()->current() }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'subcategory_icon', name: 'subcategory_icon', orderable: false, searchable: false},
                    {data: 'subcategory_name', name: 'subcategory_name'},
                    {data: 'status', name: 'status'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ]
            });
        });
    </script>
@endpush
