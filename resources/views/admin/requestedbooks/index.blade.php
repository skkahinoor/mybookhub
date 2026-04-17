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
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($bookRequests as $key => $book)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $book['book_title'] }}</td>
                                                <td>{{ $book['author_name'] }}</td>
                                                <td>{{ $book['message'] }}</td>
                                                <td>{{ $book->user->name ?? 'User not found' }}</td>
                                                <td>
                                                    {{ $book->vendor->vendorbusinessdetails->shop_name ?? $book->vendor->user->name ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    @php
                                                        $status = $book['status'];
                                                        $badgeClass = 'secondary';
                                                        $statusLabel = ucfirst(str_replace('_', ' ', $status));
                                                        
                                                        if($status == 'awaiting_response') { $badgeClass = 'warning'; $statusLabel = '🟡 Awaiting Response'; }
                                                        elseif($status == 'vendor_replied') { $badgeClass = 'info'; $statusLabel = '🔵 Vendor Replied'; }
                                                        elseif($status == 'available') { $badgeClass = 'success'; $statusLabel = '🟢 Available'; }
                                                        elseif($status == 'not_available') { $badgeClass = 'danger'; $statusLabel = '🔴 Not Available'; }
                                                    @endphp
                                                    <span class="badge badge-{{ $badgeClass }}" style="padding: 6px 10px; font-weight: 600;">{{ $statusLabel }}</span>
                                                </td>
                                                <td>
                                                    {{-- @if ($adminType !== 'vendor')
                                                        <a href="{{ url('admin/requestedbooks/reply/' . $book['id']) }}"
                                                            title="Reply">
                                                            <i style="font-size:25px;" class="mdi mdi-reply"></i>
                                                        </a>
                                                    @endif --}}
                                                    @php
                                                        $replyRoute = ($adminType === 'vendor') ? 'vendor.requestbook.reply' : 'requestbook.reply';
                                                    @endphp
                                                    <a href="{{ route($replyRoute, $book['id']) }}"
                                                        title="Reply">
                                                        <i class="mdi mdi-reply icon-action"></i>
                                                    </a>

                                                    @if ($adminType === 'vendor')
                                                        <form
                                                            action="{{ route('vendor.bookrequests.delete', $book['id']) }}"
                                                            method="POST" style="display:inline;"
                                                            onsubmit="return confirm('Are you sure you want to delete this request?')">
                                                            @csrf
                                                            @method('DELETE')

                                                            <button type="submit"
                                                                style="background:none;border:none;padding:0;">
                                                                <i style="color:red;"
                                                                    class="mdi mdi-file-excel-box"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        {{-- ADMIN DASHBOARD DELETE --}}
                                                        <form
                                                            action="{{ route('admin.bookrequests.delete', $book['id']) }}"
                                                            method="POST" style="display:inline;"
                                                            onsubmit="return confirm('Are you sure you want to delete this request?')">
                                                            @csrf
                                                            @method('DELETE')

                                                            <button type="submit"
                                                                style="background:none;border:none;padding:0;">
                                                                <i style="color:red;"
                                                                    class="mdi mdi-file-excel-box"></i>
                                                            </button>
                                                        </form>
                                                    @endif
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
            $('#request').DataTable();
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
