@include('user.layout.header')

<div class="container-fluid page-body-wrapper">
    @include('user.layout.sidebar')

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="font-weight-bold">Ticket #{{ $query->ticket_id }}</h3>
                            <h6 class="font-weight-normal mb-0">Conversation regarding: <strong>{{ $query->orderProduct->product_name ?? 'Product' }}</strong></h6>
                        </div>
                        <a href="{{ route('student.orders.queries') }}" class="btn btn-outline-primary btn-sm">
                            <i class="ti-arrow-left"></i> Back to Queries
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h4 class="card-title">Query Information</h4>
                            <ul class="list-unstyled">
                                <li class="mb-2"><strong>Order ID:</strong> #{{ $query->order_id }}</li>
                                <li class="mb-2"><strong>Subject:</strong> {{ $query->subject }}</li>
                                <li class="mb-2"><strong>Raised On:</strong> {{ $query->created_at->format('M d, Y h:i A') }}</li>
                                <li class="mb-2">
                                    <strong>Status:</strong> 
                                    @php
                                        $class = 'badge-warning';
                                        if($query->status == 'resolved') $class = 'badge-success';
                                        elseif($query->status == 'ongoing') $class = 'badge-info';
                                        elseif($query->status == 'closed') $class = 'badge-secondary';
                                    @endphp
                                    <span class="badge {{ $class }}">{{ ucfirst($query->status) }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Initial Message</h4>
                            <p class="text-muted" style="white-space: pre-wrap;">{{ $query->message }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-8 text-center">
                    <div class="card card-outline-primary" style="height: 600px; display: flex; flex-direction: column;">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h4 class="mb-0">Messages</h4>
                        </div>
                        <div class="card-body chat-container" id="chat-messages" style="flex: 1; overflow-y: auto; padding: 20px; background-color: #f8f9fa;">
                            @forelse($query->messages as $msg)
                                <div class="message-wrapper mb-4 d-flex {{ $msg->sender_type == 'student' ? 'justify-content-end' : 'justify-content-start' }}">
                                    <div class="message-content p-3" style="max-width: 80%; border-radius: 15px; position: relative; {{ $msg->sender_type == 'student' ? 'background-color: #4B49AC; color: white;' : 'background-color: #ffffff; border: 1px solid #e3e3e3;' }}">
                                        <div class="message-text" style="white-space: pre-wrap;">{{ $msg->message }}</div>
                                        
                                        @if($msg->attachment)
                                            <div class="message-attachment mt-2 pt-2 border-top" style="border-color: rgba(255,255,255,0.2) !important;">
                                                @php
                                                    $ext = pathinfo($msg->attachment, PATHINFO_EXTENSION);
                                                @endphp
                                                @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                                                    <a href="{{ asset($msg->attachment) }}" target="_blank">
                                                        <img src="{{ asset($msg->attachment) }}" class="img-fluid rounded" style="max-height: 200px;">
                                                    </a>
                                                @elseif($ext == 'pdf')
                                                    <a href="{{ asset($msg->attachment) }}" target="_blank" class="btn btn-sm btn-light">
                                                        <i class="ti-file"></i> View PDF
                                                    </a>
                                                @elseif(in_array($ext, ['mp4', 'mov', 'avi']))
                                                    <video width="100%" height="auto" controls class="rounded">
                                                        <source src="{{ asset($msg->attachment) }}" type="video/{{ $ext == 'mov' ? 'quicktime' : $ext }}">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                @endif
                                            </div>
                                        @endif

                                        <div class="message-meta mt-1" style="font-size: 10px; opacity: 0.8; text-align: right;">
                                            {{ $msg->sender_type == 'student' ? 'You' : ($msg->sender_type == 'admin' ? 'Support' : 'Vendor') }} • {{ $msg->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <i class="ti-comments text-muted d-block mb-3" style="font-size: 50px;"></i>
                                    <p class="text-muted">No replies yet. Your query is being reviewed.</p>
                                </div>
                            @endforelse
                        </div>
                        
                        @if($query->status != 'closed')
                        <div class="card-footer bg-white p-3 border-top">
                            <form action="{{ route('student.orders.query.reply', $query->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group mb-2">
                                    <textarea name="message" class="form-control" rows="2" placeholder="Type your reply here..." style="border-radius: 10px; padding: 10px 20px; resize: none;" required></textarea>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="attachment-input">
                                        <label for="attachment" class="mb-0" style="cursor: pointer;">
                                            <i class="ti-clip text-primary mr-2"></i> 
                                            <span class="small text-muted" id="file-name">Attach Photo/Video/PDF</span>
                                            <input type="file" name="attachment" id="attachment" class="d-none" onchange="document.getElementById('file-name').innerText = this.files[0].name">
                                        </label>
                                    </div>
                                    <button class="btn btn-primary" type="submit" style="border-radius: 20px; padding: 8px 25px;">
                                        <i class="ti-location-arrow"></i> Send Reply
                                    </button>
                                </div>
                            </form>
                        </div>
                        @else
                        <div class="card-footer bg-light p-3 border-top text-center">
                            <span class="text-muted"><i class="ti-lock"></i> This ticket is closed. You can no longer reply.</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('user.layout.footer')
    </div>
    <!-- plugins:js -->
    <script src="{{ asset('user/vendors/js/vendor.bundle.base.js') }}"></script>
    <!-- endinject -->
    <script src="{{ asset('user/js/off-canvas.js') }}"></script>
    <script src="{{ asset('user/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('user/js/template.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Scroll to bottom of chat
            var chatContainer = document.getElementById("chat-messages");
            chatContainer.scrollTop = chatContainer.scrollHeight;
        });
    </script>
</body>
</html>
