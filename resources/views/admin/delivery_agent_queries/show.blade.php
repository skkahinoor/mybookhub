@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title mb-0">Query Details: {{ $query->subject }}</h4>
                                <a href="{{ route('admin.delivery_agent_queries.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
                            </div>

                            @if (Session::has('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success:</strong> {{ Session::get('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            @if (Session::has('error_message'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Error:</strong> {{ Session::get('error_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <p><strong>Agent Name:</strong> {{ $query->user ? $query->user->name : 'N/A' }}</p>
                                    <p><strong>Phone:</strong> {{ $query->user ? $query->user->phone : 'N/A' }}</p>
                                    <p><strong>Email:</strong> {{ $query->user ? $query->user->email : 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Created At:</strong> {{ date('d M Y, h:i A', strtotime($query->created_at)) }}</p>
                                    <p><strong>Status:</strong> 
                                        @if($query->status === 'Open')
                                            <span class="badge bg-warning">Open</span>
                                        @elseif($query->status === 'Solved')
                                            <span class="badge bg-success">Solved</span>
                                        @else
                                            <span class="badge bg-secondary">Closed</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <!-- Update Status Form -->
                            <div class="mb-4">
                                <form action="{{ route('admin.delivery_agent_queries.status', $query->id) }}" method="POST" class="d-flex align-items-center" style="gap: 15px;">
                                    @csrf
                                    <label class="mb-0 fw-bold">Update Status:</label>
                                    <select name="status" class="form-control" style="width: auto;">
                                        <option value="Open" {{ $query->status == 'Open' ? 'selected' : '' }}>Open</option>
                                        <option value="Solved" {{ $query->status == 'Solved' ? 'selected' : '' }}>Solved</option>
                                        <option value="Closed" {{ $query->status == 'Closed' ? 'selected' : '' }}>Closed</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                </form>
                            </div>

                            <hr>

                            <h5 class="mb-3">Conversation</h5>
                            <div class="conversation-box p-3 mb-4" style="border: 1px solid #ccc; border-radius: 8px; max-height: 400px; overflow-y: auto; background-color: #f9f9f9;">
                                @foreach($query->messages as $message)
                                    <div class="mb-3 d-flex {{ $message->sender_type == 'admin' ? 'justify-content-end' : 'justify-content-start' }}">
                                        <div style="max-width: 70%; padding: 10px 15px; border-radius: 15px; {{ $message->sender_type == 'admin' ? 'background-color: #052CA3; color: white;' : 'background-color: #e2e8f0; color: black;' }}">
                                            <p class="mb-1" style="font-size: 14px;">{!! nl2br(e($message->message)) !!}</p>
                                            <small style="font-size: 11px; {{ $message->sender_type == 'admin' ? 'color: #ccc;' : 'color: #666;' }}">
                                                {{ date('d M Y, h:i A', strtotime($message->created_at)) }}
                                            </small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if($query->status === 'Open')
                                <form action="{{ route('admin.delivery_agent_queries.reply', $query->id) }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="message">Send a Reply</label>
                                        <textarea name="message" id="message" rows="4" class="form-control" required placeholder="Type your reply here..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success">Send Reply</button>
                                </form>
                            @else
                                <div class="alert alert-secondary">This query is currently marked as {{ $query->status }} and cannot receive new replies.</div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.layout.footer')
    </div>
@endsection
