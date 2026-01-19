@extends('admin.layout.layout')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Sell Book Requests</h4>
                        
                        @if(Session::has('success_message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Success:</strong> {{ Session::get('success_message') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="table-responsive pt-3">
                            <table class="table table-bordered" id="sellBookRequests">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Book Title</th>
                                        <th>Author</th>
                                        <th>User</th>
                                        <th>Request Status</th>
                                        <th>Book Status</th>
                                        <th>Expected Price</th>
                                        <th>Request Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requests as $key => $request)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $request->book_title }}</td>
                                        <td>{{ $request->author_name ?? 'N/A' }}</td>
                                        <td>{{ $request->user->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($request->request_status == 'pending')
                                                <span class="badge badge-warning">Pending</span>
                                            @elseif($request->request_status == 'approved')
                                                <span class="badge badge-success">Approved</span>
                                            @else
                                                <span class="badge badge-danger">Rejected</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($request->book_status == 'pending_review')
                                                <span class="badge badge-info">Pending Review</span>
                                            @elseif($request->book_status == 'approved')
                                                <span class="badge badge-success">Approved</span>
                                            @elseif($request->book_status == 'rejected')
                                                <span class="badge badge-danger">Rejected</span>
                                            @elseif($request->book_status == 'sold')
                                                <span class="badge badge-primary">Sold</span>
                                            @else
                                                <span class="badge badge-secondary">Not Submitted</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($request->expected_price)
                                                ₹{{ number_format($request->expected_price, 2) }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $request->created_at->format('d M Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.sell-book-requests.show', $request->id) }}" title="View Details">
                                                <i style="font-size:25px;" class="mdi mdi-eye"></i>
                                            </a>
                                        
                                            <form action="{{ route('admin.sell-book-requests.destroy', $request->id) }}"
                                                  method="POST"
                                                  style="display:inline;"
                                                  onsubmit="return confirm('Are you sure you want to delete this sell book request?')">
                                                @csrf
                                                @method('DELETE')
                                        
                                                <button type="submit" style="background:none;border:none;padding:0;">
                                                    <i style="font-size:25px;color:red;" class="mdi mdi-delete"></i>
                                                </button>
                                            </form>
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

<!-- DataTables Bootstrap 4 JS CDN -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {
        $('#sellBookRequests').DataTable({
            "order": [[0, "desc"]]
        });
    });
</script>
@endsection

