@extends('admin.layout.layout')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">Staff Management</h4>
                            <a href="{{ route('admin.staff.create') }}" class="btn btn-success">
                                <i class="mdi mdi-account-plus"></i> Add Staff
                            </a>
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
                            <table class="table table-bordered" id="staff-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Mobile</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Image</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.layout.footer')
</div>

{{-- DataTables --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function () {
    var table = $('#staff-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.staff.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name',     name: 'name' },
            { data: 'phone',    name: 'phone' },
            { data: 'email',    name: 'email' },
            { data: 'role_name',name: 'role_name', orderable: false, searchable: false },
            { data: 'image',    name: 'profile_image', orderable: false, searchable: false },
            { data: 'status',   name: 'status',   orderable: false, searchable: false },
            { data: 'actions',  name: 'actions',  orderable: false, searchable: false },
        ],
        order: [[0, 'desc']],
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
    });

    // Status toggle
    $(document).on('click', '.updateStaffStatus', function () {
        var staffId  = $(this).attr('staff_id');
        var status   = $(this).find('i').attr('status');
        var url      = $(this).data('url');
        var icon     = $(this).find('i');
        var self     = $(this);

        $.ajax({
            url: url,
            type: 'POST',
            data: { staff_id: staffId, status: status, _token: '{{ csrf_token() }}' },
            success: function (resp) {
                if (resp.status == 1) {
                    icon.removeClass('mdi-bookmark-outline').addClass('mdi-bookmark-check');
                    icon.attr('status', 'Active');
                } else {
                    icon.removeClass('mdi-bookmark-check').addClass('mdi-bookmark-outline');
                    icon.attr('status', 'Inactive');
                }
            },
            error: function () {
                alert('Failed to update status. Please try again.');
            }
        });
    });
});
</script>
@endsection
