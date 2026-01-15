@extends('admin.layout.layout')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title mb-0">Sell Book Request Details</h4>
                            <a href="{{ route('admin.sell-book-requests.index') }}" class="btn btn-light">
                                <i class="mdi mdi-arrow-left"></i> Back to List
                            </a>
                        </div>

                        @if(Session::has('success_message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Success:</strong> {{ Session::get('success_message') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <!-- Status Cards -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Request Status</h6>
                                        @if($request->request_status == 'pending')
                                            <span class="badge badge-warning badge-lg">Pending</span>
                                        @elseif($request->request_status == 'approved')
                                            <span class="badge badge-success badge-lg">Approved</span>
                                        @else
                                            <span class="badge badge-danger badge-lg">Rejected</span>
                                        @endif
                                        
                                        <!-- Update Request Status Form -->
                                        <form method="POST" action="{{ route('admin.sell-book-requests.update-request-status', $request->id) }}" class="mt-3">
                                            @csrf
                                            <div class="form-group">
                                                <label>Change Request Status:</label>
                                                <select name="request_status" class="form-control" required>
                                                    <option value="pending" {{ $request->request_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="approved" {{ $request->request_status == 'approved' ? 'selected' : '' }}>Approved</option>
                                                    <option value="rejected" {{ $request->request_status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Admin Notes:</label>
                                                <textarea name="admin_notes" class="form-control" rows="3" 
                                                          placeholder="Add notes for the user...">{{ $request->admin_notes }}</textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-sm">Update Request Status</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Book Status</h6>
                                        @if($request->book_status == 'pending_review')
                                            <span class="badge badge-info badge-lg">Pending Review</span>
                                        @elseif($request->book_status == 'approved')
                                            <span class="badge badge-success badge-lg">Approved</span>
                                        @elseif($request->book_status == 'rejected')
                                            <span class="badge badge-danger badge-lg">Rejected</span>
                                        @elseif($request->book_status == 'sold')
                                            <span class="badge badge-primary badge-lg">Sold</span>
                                        @else
                                            <span class="badge badge-secondary badge-lg">Not Submitted</span>
                                        @endif
                                        
                                        @if($request->hasBookDetails())
                                            <!-- Update Book Status Form -->
                                            <form method="POST" action="{{ route('admin.sell-book-requests.update-book-status', $request->id) }}" class="mt-3">
                                                @csrf
                                                <div class="form-group">
                                                    <label>Change Book Status:</label>
                                                    <select name="book_status" class="form-control" required>
                                                        <option value="pending_review" {{ $request->book_status == 'pending_review' ? 'selected' : '' }}>Pending Review</option>
                                                        <option value="approved" {{ $request->book_status == 'approved' ? 'selected' : '' }}>Approved</option>
                                                        <option value="rejected" {{ $request->book_status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                        <option value="sold" {{ $request->book_status == 'sold' ? 'selected' : '' }}>Sold</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Final Admin Notes:</label>
                                                    <textarea name="final_admin_notes" class="form-control" rows="3" 
                                                              placeholder="Add final notes...">{{ $request->final_admin_notes }}</textarea>
                                                </div>
                                                <button type="submit" class="btn btn-primary btn-sm">Update Book Status</button>
                                            </form>
                                        @else
                                            <p class="mt-3 text-muted">Book details not yet submitted by user.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

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
                                            <tr>
                                                <th>Requested By:</th>
                                                <td>{{ $request->user->name ?? 'N/A' }} ({{ $request->user->email ?? 'N/A' }})</td>
                                            </tr>
                                            @if($request->request_message)
                                                <tr>
                                                    <th>Request Message:</th>
                                                    <td>{{ $request->request_message }}</td>
                                                </tr>
                                            @endif
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
                                                    <td><span class="badge badge-info">{{ $request->book_condition }}</span></td>
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
    @include('admin.layout.footer')
</div>
@endsection

