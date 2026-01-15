@include('user.layout.header')

<div class="container-fluid page-body-wrapper">
    @include('user.layout.sidebar')

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title mb-0">Sell Old Book Requests</h4>
                                <a href="{{ route('user.sell-book.create') }}" class="btn btn-primary">
                                    <i class="mdi mdi-plus"></i> New Request
                                </a>
                            </div>

                            @if (session('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if (session('error_message'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if (session('info_message'))
                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                    {{ session('info_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if($requests->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Book Title</th>
                                                <th>Author</th>
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
                                                        <a href="{{ route('user.sell-book.show', $request->id) }}" 
                                                           class="btn btn-sm btn-info" title="View Details">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        @if($request->request_status == 'approved' && !$request->hasBookDetails())
                                                            <a href="{{ route('user.sell-book.edit', $request->id) }}" 
                                                               class="btn btn-sm btn-primary" title="Fill Book Details">
                                                                <i class="bi bi-pen"></i>
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info text-center">
                                    <h5>No sell book requests yet!</h5>
                                    <p>Click "New Request" to submit your first book for sale.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('user.layout.footer')

