@include('user.layout.header')

<div class="container-fluid page-body-wrapper">
    @include('user.layout.sidebar')

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="card-title mb-0">Notifications</h4>
                                @if($unreadCount > 0)
                                    <button id="student-mark-all-read"
                                            class="btn btn-sm btn-primary"
                                            type="button">
                                        Mark all as read
                                    </button>
                                @endif
                            </div>

                            @if($notifications->count())
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Message</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($notifications as $n)
                                            <tr class="{{ $n->is_read ? '' : 'table-warning' }}">
                                                <td class="font-weight-bold">
                                                    {{ $n->title }}
                                                </td>
                                                <td style="max-width: 420px;">
                                                    <span class="text-muted d-block" style="white-space: normal;">
                                                        {{ \Illuminate\Support\Str::limit($n->message, 120) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($n->is_read)
                                                        <span class="badge badge-success">Read</span>
                                                    @else
                                                        <span class="badge badge-warning">Unread</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $n->created_at?->format('M d, Y h:i A') }}
                                                    </small>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-3">
                                    {{ $notifications->links() }}
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="ti-bell-off" style="font-size: 48px; color:#cbd5e1;"></i>
                                    <h5 class="mt-3 mb-1">No notifications</h5>
                                    <p class="text-muted mb-0">You’re all caught up.</p>
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

<!-- plugins:js -->
<script src="{{ asset('user/vendors/js/vendor.bundle.base.js') }}"></script>
<!-- endinject -->
<!-- inject:js -->
<script src="{{ asset('user/js/off-canvas.js') }}"></script>
<script src="{{ asset('user/js/hoverable-collapse.js') }}"></script>
<script src="{{ asset('user/js/template.js') }}"></script>
<script src="{{ asset('user/js/settings.js') }}"></script>
<!-- endinject -->

<script>
    (function () {
        var btn = document.getElementById('student-mark-all-read');
        if (!btn) return;

        btn.addEventListener('click', function () {
            if (!confirm('Mark all notifications as read?')) return;

            fetch("{{ route('student.notifications.mark_all_read') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'),
                    "Accept": "application/json"
                }
            }).then(function () {
                window.location.reload();
            }).catch(function () {});
        });
    })();
</script>

