@include('user.layout.header')

<style>
    .book-request-index .card {
        border: 1px solid #e8edf5;
        border-radius: 14px;
        box-shadow: 0 6px 20px rgba(21, 38, 67, 0.06);
    }
    .book-request-index .hero {
        background: linear-gradient(135deg, #fff8ef 0%, #f6f9ff 100%);
        border: 1px solid #f0e5d6;
        border-radius: 12px;
        padding: 16px 18px;
        margin-bottom: 16px;
    }
    .book-request-index .hero h4 {
        font-size: 22px;
        font-weight: 700;
        color: #1f2d3d;
        margin-bottom: 4px;
    }
    .book-request-index .hero p {
        margin: 0;
        color: #64748b;
    }
    .book-request-index .request-table thead th {
        background: #f6f9fc;
        color: #334155;
        font-weight: 700;
        border-bottom: 1px solid #e5eaf1;
        white-space: nowrap;
    }
    .book-request-index .request-table td {
        vertical-align: middle;
    }
    .book-request-index .status-pill {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }
    .book-request-index .status-pending { background: #fff3cd; color: #8a6d1f; }
    .book-request-index .status-progress { background: #dbeafe; color: #1d4ed8; }
    .book-request-index .status-resolved { background: #dcfce7; color: #166534; }
    .book-request-index .status-other { background: #e2e8f0; color: #334155; }
    .book-request-index .msg-btn {
        border-radius: 20px;
        font-weight: 600;
        border-color: #b6c3d4;
        color: #334155;
    }
    .book-request-index .msg-card {
        border: 1px solid #e8edf5;
        border-radius: 10px;
        background: #fbfdff;
        padding: 16px;
    }
    .book-request-index .send-btn {
        background: #cf8938;
        border: none;
        border-radius: 8px;
        font-weight: 600;
    }
</style>

<div class="container-fluid page-body-wrapper">
    @include('user.layout.sidebar')

    <div class="main-panel book-request-index">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="hero d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px;">
                                <div>
                                    <h4>My Book Requests</h4>
                                    <p>View, track and message admin about each request.</p>
                                </div>
                                <a href="{{ route('student.query.raise') }}" class="btn btn-primary" style="background-color:#cf8938;border:none;border-radius:8px;font-weight:600;">
                                    <span class="mr-1">➕</span> New Book Request
                                </a>
                            </div>

                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            @if ($requestedBooks->isEmpty())
                                <div class="text-center py-5">
                                    <i class="mdi mdi-book-open-variant mdi-48px text-muted mb-3"></i>
                                    <h4 class="text-muted">No requests yet</h4>
                                    <p class="text-muted">You haven't made any book requests yet.</p>
                                    <h5 style="color:#cf8938;">Search & Request Books</h5>
                                </div>
                            @else
                                <div class="row">
                                    @foreach ($requestedBooks as $key => $request)
                                        @php
                                            $collapseId = 'collapseReq' . $request->id;
                                            $status = is_numeric($request->status) ? (int) $request->status : $request->status;
                                            $isResolved = $status === 'resolved' || $status === 'Resolved' || $status === 1;
                                        @endphp
                                        <div class="col-lg-6 mb-4">
                                            <div class="msg-card h-100">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <small class="text-muted d-block">Request #{{ $key + 1 }}</small>
                                                        <h5 class="mb-1" style="font-weight:700;color:#1f2937;">
                                                            <a href="{{ route('student.query.index', ['query_id' => $request->id]) }}" style="color:#1f2937;text-decoration:none;">
                                                                {{ $request->book_title }}
                                                            </a>
                                                        </h5>
                                                        <small class="text-muted">{{ $request->created_at->format('M d, Y') }}</small>
                                                    </div>
                                                    <div>
                                                        @if ($status === 0 || $status === 'pending' || $status === 'Pending')
                                                            <span class="status-pill status-pending">Pending</span>
                                                        @elseif ($status === 1 || $status === 'in_progress' || $status === 'In Progress')
                                                            <span class="status-pill status-progress">In Progress</span>
                                                        @elseif ($status === 'resolved' || $status === 'Resolved')
                                                            <span class="status-pill status-resolved">Resolved</span>
                                                        @else
                                                            <span class="status-pill status-other">Available</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <div><strong>Author:</strong> {{ $request->author_name ?? 'N/A' }}</div>
                                                    <div><strong>Vendor:</strong> {{ $request->vendor->vendorbusinessdetails->shop_name ?? $request->vendor->user->name ?? 'N/A' }}</div>
                                                </div>

                                                <div class="d-flex flex-wrap mb-3" style="gap:8px;">
                                                    <a href="{{ route('student.query.index', ['query_id' => $request->id]) }}" class="btn btn-sm btn-primary" style="background:#2563eb;border:none;border-radius:8px;">
                                                        🔎 Open in My Queries
                                                    </a>
                                                    {{-- <button class="btn btn-sm btn-outline-primary msg-btn" type="button"
                                                        data-toggle="collapse" data-target="#{{ $collapseId }}"
                                                        aria-expanded="false" aria-controls="{{ $collapseId }}">
                                                        💬 Quick Message
                                                    </button> --}}
                                                </div>

                                                <div class="collapse" id="{{ $collapseId }}">
                                                    <div class="border-top pt-3">
                                                        <div class="mb-3">
                                                            @if ($request->message)
                                                                <strong>Message:</strong> {{ $request->message }}
                                                            @else
                                                                <span class="text-muted">No message provided.</span>
                                                            @endif
                                                            <div class="text-muted mt-2" style="font-size:12px;">Requested on {{ $request->created_at->format('F d, Y h:i A') }}</div>
                                                        </div>

                                                        <form action="{{ route('student.book.reply', $request->id) }}" method="POST">
                                                            @csrf
                                                            <div class="form-group mb-2">
                                                                <label class="font-weight-bold">Your Message <span class="text-danger">*</span></label>
                                                                <textarea name="message" rows="4" class="form-control" required minlength="10"
                                                                    placeholder="Type your message here... Provide details about the book you're looking for."></textarea>
                                                                <small class="text-muted">Minimum 10 characters required</small>
                                                            </div>
                                                            <button type="submit" class="btn btn-sm btn-primary send-btn">
                                                                📤 Send Message
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
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
