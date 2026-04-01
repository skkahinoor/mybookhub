@extends('admin.layout.layout')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Reply to Customer Query</h4>
                        <div class="row mb-3">
                            <div class="col-12">
                                <span class="badge badge-primary">Ticket ID: {{ $query->ticket_id }}</span>
                                <span class="badge badge-light border">Order #{{ $query->order_id }}</span>
                                <h4 class="mt-2 text-dark">{{ $query->user->name }} - {{ $query->subject }}</h4>
                            </div>
                        </div>

                        <div class="card bg-light mb-4 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title text-muted">Initial Message</h5>
                                <p style="white-space: pre-wrap; font-size: 15px;">{{ $query->message }}</p>
                                <hr>
                                <div class="chat-thread" style="max-height: 400px; overflow-y: auto; padding: 15px; background: #fafafa; border: 1px solid #eee; border-radius: 8px;">
                                    @forelse($query->messages as $msg)
                                        <div class="mb-3 {{ $msg->sender_type == 'student' ? 'text-left' : 'text-right' }}">
                                            <div style="display: inline-block; padding: 10px 15px; border-radius: 12px; max-width: 85%; {{ $msg->sender_type == 'student' ? 'background: #f1f1f1; border: 1px solid #ddd;' : 'background: #4B49AC; color: white;' }}">
                                                <div style="font-size: 0.85em; opacity: 0.8; margin-bottom: 4px;">
                                                    {{ $msg->sender_type == 'student' ? ($query->user->name ?? 'Student') : 'Assigned Staff' }} • {{ $msg->created_at->format('M d, h:i A') }}
                                                </div>
                                                <div style="white-space: pre-wrap; font-size: 0.95em;">{{ $msg->message }}</div>
                                                
                                                @if($msg->attachment)
                                                    <div class="mt-2 pt-2 border-top" style="border-color: rgba(255,255,255,0.2) !important;">
                                                        @php
                                                            $ext = pathinfo($msg->attachment, PATHINFO_EXTENSION);
                                                        @endphp
                                                        @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                                                            <a href="{{ asset($msg->attachment) }}" target="_blank">
                                                                <img src="{{ asset($msg->attachment) }}" class="img-fluid rounded" style="max-height: 150px;">
                                                            </a>
                                                        @elseif($ext == 'pdf')
                                                            <a href="{{ asset($msg->attachment) }}" target="_blank" class="btn btn-xs btn-outline-{{ $msg->sender_type == 'student' ? 'dark' : 'light' }}">
                                                                <i class="mdi mdi-file-pdf"></i> View PDF
                                                            </a>
                                                        @elseif(in_array($ext, ['mp4', 'mov', 'avi']))
                                                            <video width="100%" height="auto" controls class="rounded mt-1">
                                                                <source src="{{ asset($msg->attachment) }}" type="video/{{ $ext == 'mov' ? 'quicktime' : $ext }}">
                                                            </video>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center text-muted py-3">No follow-up messages yet.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <form class="forms-sample" action="{{ url('admin/order-query/reply/'.$query->id) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="admin_reply">Your Reply</label>
                                <textarea name="admin_reply" class="form-control" rows="6" placeholder="Enter your reply to the customer..." required></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="attachment">Attachment (Optional)</label>
                                <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.mp4,.mov,.avi,.pdf">
                                <small class="text-muted">Accepts Image, Video (MP4/MOV/AVI), or PDF (Max 10MB)</small>
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
