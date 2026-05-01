@extends('admin.layout.layout') {{-- Adjust this if your layout is different --}}

@section('content')
    <style>
        .request-page .card {
            border: 1px solid #e8edf5;
            border-radius: 14px;
            box-shadow: 0 6px 20px rgba(21, 38, 67, 0.06);
        }
        .request-page .table thead th {
            background: #f5f8fc;
            color: #334155;
            border-bottom: 1px solid #e5eaf1;
            font-weight: 700;
            white-space: nowrap;
        }
        .request-page .table td {
            vertical-align: middle;
        }
        .request-page .page-title {
            font-size: 22px;
            font-weight: 700;
            color: #1f2d3d;
            margin-bottom: 2px;
        }
        .request-page .page-subtitle {
            color: #64748b;
            margin-bottom: 20px;
        }
        .request-page .icon-action {
            font-size: 22px;
            margin-right: 8px;
        }
    </style>
    <div class="main-panel">
        <div class="content-wrapper request-page">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="page-title">Book Request Queries</h4>
                            <p class="page-subtitle">Track, reply, and update request status in one place.</p>
                            {{-- <a style="max-width: 150px; float: right; display: inline-block;" href="{{ url('admin/add-edit-language') }}" class="btn btn-block btn-primary">Add Language</a> --}}
                            @if (Session::has('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success:</strong> {{ Session::get('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            <div class="table-responsive pt-3">
                                <table class="table table-bordered" id="request">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Author Name</th>
                                            <th>Message</th>
                                            <th>Requested By User</th>
                                            <th>Target Vendor</th>
                                            <th>Location</th>
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


    <!-- DataTables Bootstrap 4 CSS CDN -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">

    <!-- jQuery CDN (required for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- DataTables JS CDN -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#request').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ url()->current() }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'book_title', name: 'book_title'},
                    {data: 'author_name', name: 'author_name'},
                    {data: 'message', name: 'message'},
                    {data: 'requested_by', name: 'user.name'},
                    {data: 'target_vendor', name: 'vendor.user.name'},
                    {data: 'location', name: 'user_location_name'},
                    {data: 'status', name: 'status'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                order: [[0, 'desc']]
            });
        });
    </script>
    <script>
        $(document).on("click", ".updateBookStatus", function() {
            var book_id = $(this).attr("book_id");
            var url = $(this).data("url");
            var $icon = $(this).find("i");

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    _token: '{{ csrf_token() }}',
                    book_id: book_id
                },
                success: function(resp) {
                    if (resp.status == 1) {
                        $icon.removeClass('mdi-bookmark-outline')
                            .addClass('mdi-bookmark-check')
                            .attr('title', 'Book Available')
                            .attr('status', 'Active');
                    } else {
                        $icon.removeClass('mdi-bookmark-check')
                            .addClass('mdi-bookmark-outline')
                            .attr('title', 'Book Requested')
                            .attr('status', 'Inactive');
                    }
                },
                error: function() {
                    alert('Failed to update status.');
                }
            });
        });
    </script>
@endsection
