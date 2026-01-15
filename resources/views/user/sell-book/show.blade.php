@include('user.layout.header')

<div class="container-fluid page-body-wrapper">
    @include('user.layout.sidebar')

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-10 grid-margin stretch-card mx-auto">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title mb-0">Book Details</h4>
                                <a href="{{ route('user.sell-book.index') }}" class="btn btn-secondary">
                                    <i class="mdi mdi-arrow-left"></i> Back to List
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

                            <!-- Request Status Card -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">Request Status</h6>
                                            @if($request->request_status == 'pending')
                                                <span class="badge badge-warning badge-lg">Pending Approval</span>
                                                <p class="mt-2 text-muted">Your request is waiting for admin approval.</p>
                                            @elseif($request->request_status == 'approved')
                                                <span class="badge badge-success badge-lg">Approved</span>
                                                <p class="mt-2 text-success">Your request has been approved! You can now fill book details.</p>
                                            @else
                                                <span class="badge badge-danger badge-lg">Rejected</span>
                                                @if($request->admin_notes)
                                                    <p class="mt-2 text-danger">{{ $request->admin_notes }}</p>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">Book Status</h6>
                                            @if($request->book_status == 'pending_review')
                                                <span class="badge badge-info badge-lg">Pending Review</span>
                                                <p class="mt-2 text-info">Book details submitted. Waiting for admin review.</p>
                                            @elseif($request->book_status == 'approved')
                                                <span class="badge badge-success badge-lg">Approved</span>
                                                <p class="mt-2 text-success">Your book has been approved and is ready for sale!</p>
                                            @elseif($request->book_status == 'rejected')
                                                <span class="badge badge-danger badge-lg">Rejected</span>
                                                @if($request->final_admin_notes)
                                                    <p class="mt-2 text-danger">{{ $request->final_admin_notes }}</p>
                                                @endif
                                            @elseif($request->book_status == 'sold')
                                                <span class="badge badge-primary badge-lg">Sold</span>
                                                <p class="mt-2 text-primary">Congratulations! Your book has been sold.</p>
                                            @else
                                                <span class="badge badge-secondary badge-lg">Not Submitted</span>
                                                <p class="mt-2 text-muted">Book details not yet submitted.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Button -->
                            @if($request->request_status == 'approved' && !$request->hasBookDetails())
                                <div class="alert alert-info">
                                    <strong>Next Step:</strong> Your request has been approved. Please fill in the book details.
                                    <a href="{{ route('user.sell-book.edit', $request->id) }}" class="btn btn-primary btn-sm ml-2">
                                        Fill Book Details
                                    </a>
                                </div>
                            @endif

                            <!-- Book Information -->
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-4">Book Information</h5>
                                    
                                    <div class="row">
                                        @if($request->book_image)
                                            <div class="col-md-4 mb-3">
                                                <img src="{{ asset($request->book_image) }}" alt="Book Image" 
                                                     class="img-fluid rounded" style="max-height: 300px;">
                                            </div>
                                        @endif
                                        <div class="{{ $request->book_image ? 'col-md-8' : 'col-md-12' }}">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <th width="30%">Book Title:</th>
                                                    <td>{{ $request->book_title }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Author Name:</th>
                                                    <td>{{ $request->author_name ?? 'N/A' }}</td>
                                                </tr>
                                                @if($request->isbn)
                                                    <tr>
                                                        <th>ISBN:</th>
                                                        <td>{{ $request->isbn }}</td>
                                                    </tr>
                                                @endif
                                                @if($request->publisher)
                                                    <tr>
                                                        <th>Publisher:</th>
                                                        <td>{{ $request->publisher }}</td>
                                                    </tr>
                                                @endif
                                                @if($request->edition)
                                                    <tr>
                                                        <th>Edition:</th>
                                                        <td>{{ $request->edition }}</td>
                                                    </tr>
                                                @endif
                                                @if($request->year_published)
                                                    <tr>
                                                        <th>Year Published:</th>
                                                        <td>{{ $request->year_published }}</td>
                                                    </tr>
                                                @endif
                                                @if($request->book_condition)
                                                    <tr>
                                                        <th>Book Condition:</th>
                                                        <td>
                                                            <span class="badge badge-info">{{ $request->book_condition }}</span>
                                                        </td>
                                                    </tr>
                                                @endif
                                                @if($request->expected_price)
                                                    <tr>
                                                        <th>Expected Price:</th>
                                                        <td><strong>₹{{ number_format($request->expected_price, 2) }}</strong></td>
                                                    </tr>
                                                @endif
                                                @if($request->book_description)
                                                    <tr>
                                                        <th>Description:</th>
                                                        <td>{{ $request->book_description }}</td>
                                                    </tr>
                                                @endif
                                                @if($request->request_message)
                                                    <tr>
                                                        <th>Request Message:</th>
                                                        <td>{{ $request->request_message }}</td>
                                                    </tr>
                                                @endif
                                                @if($request->admin_notes)
                                                    <tr>
                                                        <th>Admin Notes:</th>
                                                        <td class="text-info">{{ $request->admin_notes }}</td>
                                                    </tr>
                                                @endif
                                                @if($request->final_admin_notes)
                                                    <tr>
                                                        <th>Final Admin Notes:</th>
                                                        <td class="text-info">{{ $request->final_admin_notes }}</td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <th>Request Date:</th>
                                                    <td>{{ $request->created_at->format('d M Y, h:i A') }}</td>
                                                </tr>
                                                @if($request->updated_at != $request->created_at)
                                                    <tr>
                                                        <th>Last Updated:</th>
                                                        <td>{{ $request->updated_at->format('d M Y, h:i A') }}</td>
                                                    </tr>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('user.layout.footer')

