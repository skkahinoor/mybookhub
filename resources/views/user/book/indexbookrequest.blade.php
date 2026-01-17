@include('user.layout.header')

<div class="container-fluid page-body-wrapper">
    @include('user.layout.sidebar')

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                                <div>
                                    <h4 class="card-title mb-1">My Book Requests</h4>
                                    <p class="text-muted mb-0">View, track and message admin about each request.</p>
                                </div>
                                <a href="{{ route('user.query.index') }}" class="btn btn-primary" style="background-color:#cf8938;border:none;">
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
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Book Title</th>
                                                <th>Author</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($requestedBooks as $key => $request)
                                                @php
                                                    $collapseId = 'collapseReq' . $request->id;
                                                    $status = is_numeric($request->status) ? (int) $request->status : $request->status;
                                                    $isResolved = $status === 'resolved' || $status === 'Resolved' || $status === 1;
                                                @endphp
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td><strong>{{ $request->book_title }}</strong></td>
                                                    <td>{{ $request->author_name ?? 'N/A' }}</td>
                                                    <td>
                                                        @if ($status === 0 || $status === 'pending' || $status === 'Pending')
                                                            <span class="badge badge-warning">Pending</span>
                                                        @elseif ($status === 1 || $status === 'in_progress' || $status === 'In Progress')
                                                            <span class="badge badge-info">In Progress</span>
                                                        @elseif ($status === 'resolved' || $status === 'Resolved')
                                                            <span class="badge badge-success">Resolved</span>
                                                        @else
                                                            <span class="badge badge-secondary">Available</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $request->created_at->format('M d, Y') }}</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary" type="button"
                                                            data-toggle="collapse" data-target="#{{ $collapseId }}"
                                                            aria-expanded="false" aria-controls="{{ $collapseId }}">
                                                            💬 Message / View
                                                        </button>
                                                    </td>
                                                </tr>
                                                <tr class="collapse" id="{{ $collapseId }}">
                                                    <td colspan="6">
                                                        <div class="p-3 border rounded">
                                                            <div class="mb-3">
                                                                <strong>Book Title:</strong> {{ $request->book_title }}<br>
                                                                @if ($request->author_name)
                                                                    <strong>Author:</strong> {{ $request->author_name }}<br>
                                                                @endif
                                                                @if ($request->message)
                                                                    <strong>Message:</strong> {{ $request->message }}
                                                                @endif
                                                                <div class="text-muted mt-2" style="font-size:12px;">Requested on {{ $request->created_at->format('F d, Y h:i A') }}</div>
                                                            </div>

                                                            <form action="{{ route('user.book.reply', $request->id) }}" method="POST">
                                                                @csrf
                                                                <div class="form-group mb-2">
                                                                    <label class="font-weight-bold">Your Message <span class="text-danger">*</span></label>
                                                                    <textarea name="message" rows="4" class="form-control" required minlength="10"
                                                                        placeholder="Type your message here... Provide details about the book you're looking for."></textarea>
                                                                    <small class="text-muted">Minimum 10 characters required</small>
                                                                </div>
                                                                <button type="submit" class="btn btn-sm btn-primary" style="background-color:#cf8938;border:none;">
                                                                    📤 Send Message
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
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
