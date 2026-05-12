@extends('admin.layout.layout')


@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="card-title mb-0">{{ $title }}</h4>
                                <div class="d-flex align-items-center">
                                    <button type="button" class="btn btn-danger mr-2" id="bulk-delete-vendors-btn" style="display: none;">
                                        <i class="mdi mdi-delete-sweep"></i> Delete Selected (<span id="selected-vendors-count">0</span>)
                                    </button>
                                    {{-- Hide "Add Vendor" on the Admin listing page; show "Add Staff" instead --}}
                                    @if (Request::is('admin/admins/admin') || Request::is('admin/admins'))
                                        <a href="{{ route('admin.staff.create') }}" class="btn btn-success">
                                            <i class="mdi mdi-account-plus"></i> Add Staff
                                        </a>
                                    @else
                                        <a href="{{ url('admin/add-edit-admin') }}" class="btn btn-primary">
                                            <i class="mdi mdi-plus"></i> Add Vendor
                                        </a>
                                    @endif
                                </div>
                            </div>

                            {{-- Success Message --}}
                            @if (Session::has('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success:</strong> {{ Session::get('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            {{-- Error Message --}}
                            @if (Session::has('error_message'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Error:</strong> {{ Session::get('error_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="table-responsive pt-3">
                                <table class="table table-bordered" id="admins-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 40px; text-align: center;"><input type="checkbox" id="select-all-vendors" style="transform: scale(1.3); cursor: pointer;"></th>
                                            <th>#</th>
                                            <th>Name</th>                                           
                                            <th>Mobile</th>
                                            <th>Email</th>
                                            <th>Image</th>
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
        <!-- content-wrapper ends -->
        <!-- partial:../../partials/_footer.html -->
        <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
                <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2022. All rights
                    reserved.</span>
            </div>
        </footer>
        <!-- partial -->
    </div>

    <!-- DataTables Bootstrap 4 CSS CDN -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">

    <!-- jQuery CDN (required for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- DataTables JS CDN -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#admins-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ url()->current() }}",
                columns: [
                    {data: 'checkbox', name: 'checkbox', orderable: false, searchable: false, class: 'text-center'},
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'mobile', name: 'phone'},
                    {data: 'email', name: 'email'},
                    {data: 'image', name: 'profile_image', orderable: false, searchable: false},
                    {data: 'status', name: 'status', orderable: false, searchable: false},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                order: [[1, 'desc']],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                drawCallback: function() {
                    $('#select-all-vendors').prop('checked', false);
                    updateBulkDeleteButton();
                }
            });

            // Toggle select all
            $('#select-all-vendors').on('click', function() {
                var checked = this.checked;
                $('.select-vendor-checkbox').each(function() {
                    this.checked = checked;
                });
                updateBulkDeleteButton();
            });

            // Individual checkbox click
            $(document).on('click', '.select-vendor-checkbox', function() {
                var allChecked = $('.select-vendor-checkbox').length === $('.select-vendor-checkbox:checked').length;
                $('#select-all-vendors').prop('checked', allChecked);
                updateBulkDeleteButton();
            });

            function updateBulkDeleteButton() {
                var checkedCount = $('.select-vendor-checkbox:checked').length;
                if (checkedCount > 0) {
                    $('#selected-vendors-count').text(checkedCount);
                    $('#bulk-delete-vendors-btn').fadeIn();
                } else {
                    $('#bulk-delete-vendors-btn').fadeOut();
                }
            }

            // Bulk delete click handler
            $('#bulk-delete-vendors-btn').on('click', function() {
                var selectedIds = [];
                $('.select-vendor-checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length > 0) {
                    if (confirm("Are you sure you want to delete the " + selectedIds.length + " selected vendors? This will also delete their vendor profiles. This action cannot be undone.")) {
                        $.ajax({
                            url: "{{ route('admin.bulkDelete') }}",
                            type: "POST",
                            data: {
                                ids: selectedIds,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                if (response.success) {
                                    alert(response.message);
                                    table.ajax.reload();
                                } else {
                                    alert(response.message || "An error occurred while deleting.");
                                }
                            },
                            error: function() {
                                alert("Failed to perform bulk delete action.");
                            }
                        });
                    }
                }
            });
        });
    </script>
@endsection
