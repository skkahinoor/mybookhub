@extends('admin.layout.layout')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Reply to Customer Query</h4>
                        <div class="alert alert-info">
                            <strong>Ticket ID:</strong> {{ $query->ticket_id }}<br>
                            <strong>Query from {{ $query->user->name }}</strong><br>
                            <strong>Order ID:</strong> #{{ $query->order_id }}<br>
                            <strong>Product:</strong> {{ $query->orderProduct->product_name ?? 'N/A' }}<br>
                            <strong>Subject:</strong> {{ $query->subject }}<br>
                            <hr>
                            <strong>Message:</strong><br>
                            <p style="white-space: pre-wrap;">{{ $query->message }}</p>
                        </div>

                        <form class="forms-sample" action="{{ url('admin/order-query/reply/'.$query->id) }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="admin_reply">Your Reply</label>
                                <textarea name="admin_reply" class="form-control" rows="6" placeholder="Enter your reply to the customer..." required>{{ $query->admin_reply }}</textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="status">Update Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="pending" @if($query->status == 'pending') selected @endif>Pending</option>
                                    <option value="ongoing" @if($query->status == 'ongoing') selected @endif>Ongoing</option>
                                    <option value="resolved" @if($query->status == 'resolved') selected @endif>Resolved</option>
                                    <option value="closed" @if($query->status == 'closed') selected @endif>Closed</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">Submit Reply</button>
                            <a href="{{ url('admin/order-queries') }}" class="btn btn-light">Back</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
