@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="card-title mb-0">{{ $title }}</h4>
                                <button id="markAllReadBtn" class="btn btn-info d-flex align-items-center gap-2 shadow-sm">
                                    <i class="mdi mdi-check-all fs-5"></i> Mark All as Read
                                </button>
                            </div>

                            @if (Session::has('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success:</strong> {{ Session::get('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="table-responsive pt-3">
                                @if ($notifications->count() > 0)
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Title</th>
                                                <th>Message</th>
                                                <th>Type</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($notifications as $notification)
                                                <tr class="{{ $notification->is_read ? '' : 'table-warning' }}">
                                                    <td>{{ $loop->iteration + ($notifications->currentPage() - 1) * $notifications->perPage() }}</td>
                                                    <td>
                                                        <strong class="{{ $notification->is_read ? '' : 'font-weight-bold' }}">
                                                            {{ $notification->title }}
                                                        </strong>
                                                    </td>
                                                    <td>{{ Str::limit($notification->message, 50) }}</td>
                                                    <td>
                                                        <span class="badge badge-info">
                                                            {{ ucfirst(str_replace('_', ' ', $notification->type)) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $notification->created_at->format('M d, Y h:i A') }}</td>
                                                    <td>
                                                        @if ($notification->is_read)
                                                            <span class="badge badge-success">Read</span>
                                                        @else
                                                            <span class="badge badge-warning">Unread</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center" style="gap: 10px;">
                                                            @if (!$notification->is_read)
                                                                <a href="#" class="mark-as-read" data-id="{{ $notification->id }}" title="Mark as Read">
                                                                    <i style="font-size: 20px" class="mdi mdi-check-circle text-success"></i>
                                                                </a>
                                                            @endif
                                                            @if ($notification->related_type == 'App\Models\SalesExecutive' && $notification->related_id)
                                                                <a href="#" class="view-sales-executive" data-id="{{ $notification->related_id }}" data-notification-id="{{ $notification->id }}" title="View Sales Executive">
                                                                    <i style="font-size: 20px" class="mdi mdi-eye text-primary"></i>
                                                                </a>
                                                            @endif
                                                            @if ($notification->related_type == 'App\Models\Withdrawal' && $notification->related_id)
                                                                <a href="{{ route('admin.withdrawals.show', $notification->related_id) }}" title="View Withdrawal Request">
                                                                    <i style="font-size: 20px" class="mdi mdi-cash-multiple text-info"></i>
                                                                </a>
                                                            @endif
                                                            @if ($notification->related_type == 'App\Models\InstitutionManagement' && $notification->related_id)
                                                                <a href="#" class="view-institution" data-id="{{ $notification->related_id }}" data-notification-id="{{ $notification->id }}" title="View Institution">
                                                                    <i style="font-size: 20px" class="mdi mdi-school text-warning"></i>
                                                                </a>
                                                            @endif
                                                            @if ($notification->related_type == 'App\Models\Student' && $notification->related_id)
                                                                <a href="#" class="view-student" data-id="{{ $notification->related_id }}" data-notification-id="{{ $notification->id }}" title="View Student">
                                                                    <i style="font-size: 20px" class="mdi mdi-account text-info"></i>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    <div class="mt-3">
                                        {{ $notifications->links() }}
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="mdi mdi-bell-off" style="font-size: 64px; color: #ccc;"></i>
                                        <h4 class="mt-3">No Notifications</h4>
                                        <p class="text-muted">You're all caught up! No notifications to display.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Details Modal -->
    <div class="modal fade" id="studentModal" tabindex="-1" role="dialog" aria-labelledby="studentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="studentModalLabel">Student Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="studentModalBody">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="approveStudentBtn" style="display: none;">
                        <i class="mdi mdi-check"></i> Approve
                    </button>
                    <button type="button" class="btn btn-danger" id="rejectStudentBtn" style="display: none;">
                        <i class="mdi mdi-close"></i> Reject
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Institution Details Modal -->
    <div class="modal fade" id="institutionModal" tabindex="-1" role="dialog" aria-labelledby="institutionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="institutionModalLabel">Institution Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="institutionModalBody">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="approveInstitutionBtn" style="display: none;">
                        <i class="mdi mdi-check"></i> Approve (Activate)
                    </button>
                    <button type="button" class="btn btn-danger" id="rejectInstitutionBtn" style="display: none;">
                        <i class="mdi mdi-close"></i> Reject (Deactivate)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Executive Details Modal -->
    <div class="modal fade" id="salesExecutiveModal" tabindex="-1" role="dialog" aria-labelledby="salesExecutiveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="salesExecutiveModalLabel">Sales Executive Registration Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="salesExecutiveModalBody">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="approveSalesExecutiveBtn" style="display: none;">
                        <i class="mdi mdi-check"></i> Approve (Activate)
                    </button>
                    <button type="button" class="btn btn-danger" id="rejectSalesExecutiveBtn" style="display: none;">
                        <i class="mdi mdi-close"></i> Reject (Deactivate)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Institution Details Modal -->
    <div class="modal fade" id="institutionModal" tabindex="-1" role="dialog" aria-labelledby="institutionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="institutionModalLabel">Institution Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="institutionModalBody">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="approveInstitutionBtn" style="display: none;">
                        <i class="mdi mdi-check"></i> Approve (Activate)
                    </button>
                    <button type="button" class="btn btn-danger" id="rejectInstitutionBtn" style="display: none;">
                        <i class="mdi mdi-close"></i> Reject (Deactivate)
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            var currentSalesExecutiveId = null;
            var currentNotificationId = null;
            var currentInstitutionId = null;
            var currentInstitutionNotificationId = null;
            var currentStudentId = null;
            var currentStudentNotificationId = null;

            // Check if URL has parameters to open modal automatically
            var urlParams = new URLSearchParams(window.location.search);
            var viewSeId = urlParams.get('view-se');
            var notifId = urlParams.get('notif');
            var viewInstId = urlParams.get('view-inst');

            if (viewSeId) {
                currentSalesExecutiveId = viewSeId;
                currentNotificationId = notifId;
                $('#salesExecutiveModal').modal('show');
                loadSalesExecutiveDetails(viewSeId);
            }

            if (viewInstId) {
                currentInstitutionId = viewInstId;
                currentInstitutionNotificationId = notifId;
                $('#institutionModal').modal('show');
                loadInstitutionDetails(viewInstId, notifId);
            }

            // View Institution Details Modal
            $(document).on('click', '.view-institution', function(e) {
                e.preventDefault();
                currentInstitutionId = $(this).data('id');
                currentInstitutionNotificationId = $(this).data('notification-id');

                // Reset modal state
                $('#approveInstitutionBtn').hide();
                $('#rejectInstitutionBtn').hide();

                // Show modal
                $('#institutionModal').modal('show');

                // Load institution details
                loadInstitutionDetails(currentInstitutionId, currentInstitutionNotificationId);
            });

            // Reset institution modal when closed
            $('#institutionModal').on('hidden.bs.modal', function () {
                currentInstitutionId = null;
                currentInstitutionNotificationId = null;
                $('#institutionModalBody').html('<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');
            });

            // View Student Details Modal
            $(document).on('click', '.view-student', function(e) {
                e.preventDefault();
                currentStudentId = $(this).data('id');
                currentStudentNotificationId = $(this).data('notification-id');

                // Reset modal state
                $('#approveStudentBtn').hide();
                $('#rejectStudentBtn').hide();

                // Show modal
                $('#studentModal').modal('show');

                // Load student details
                loadStudentDetails(currentStudentId, currentStudentNotificationId);
            });

            // Reset student modal when closed
            $('#studentModal').on('hidden.bs.modal', function () {
                currentStudentId = null;
                currentStudentNotificationId = null;
                $('#studentModalBody').html('<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');
            });

            function loadInstitutionDetails(id, notificationId) {
                if (notificationId) {
                    currentInstitutionNotificationId = notificationId;
                }
                $('#institutionModalBody').html('<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');

                $.ajax({
                    url: 'institution-management/' + id + '/details',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            var data = response.data;
                            var statusBadge = data.status == 1
                                ? '<span class="badge badge-success">Active</span>'
                                : '<span class="badge badge-warning">Inactive (Pending Approval)</span>';

                            var classesHtml = '';
                            if (data.classes && data.classes.length > 0) {
                                classesHtml = '<h6 class="font-weight-bold mt-3">Classes</h6><table class="table table-sm table-bordered"><thead><tr><th>Class Name</th><th>Total Strength</th></tr></thead><tbody>';
                                data.classes.forEach(function(cls) {
                                    classesHtml += '<tr><td>' + cls.class_name + '</td><td>' + cls.total_strength + '</td></tr>';
                                });
                                classesHtml += '</tbody></table>';
                            }

                            var html = `
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="font-weight-bold">Institution Information</h6>
                                        <table class="table table-sm">
                                            <tr>
                                                <td><strong>Name:</strong></td>
                                                <td>${data.name || 'N/A'}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Type:</strong></td>
                                                <td>${data.type || 'N/A'}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Board:</strong></td>
                                                <td>${data.board || 'N/A'}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Contact Number:</strong></td>
                                                <td>${data.contact_number || 'N/A'}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Status:</strong></td>
                                                <td>${statusBadge}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Added On:</strong></td>
                                                <td>${data.created_at || 'N/A'}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <h6 class="font-weight-bold">Location Information</h6>
                                        <table class="table table-sm">
                                            <tr>
                                                <td><strong>Country:</strong></td>
                                                <td>${data.country || 'N/A'}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>State:</strong></td>
                                                <td>${data.state || 'N/A'}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>District:</strong></td>
                                                <td>${data.district || 'N/A'}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Block:</strong></td>
                                                <td>${data.block || 'N/A'}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Pincode:</strong></td>
                                                <td>${data.pincode || 'N/A'}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                ${classesHtml}
                            `;

                            $('#institutionModalBody').html(html);

                            // Show/hide status buttons based on current status
                            if (data.status == 0) {
                                $('#approveInstitutionBtn').show().off('click').on('click', function() {
                                    if (!confirm('Are you sure you want to approve (activate) this institution?')) {
                                        return;
                                    }
                                    updateInstitutionStatus(1, id, currentInstitutionNotificationId);
                                });
                                $('#rejectInstitutionBtn').hide();
                            } else {
                                $('#approveInstitutionBtn').hide();
                                $('#rejectInstitutionBtn').show().off('click').on('click', function() {
                                    if (!confirm('Are you sure you want to reject (deactivate) this institution?')) {
                                        return;
                                    }
                                    updateInstitutionStatus(0, id, currentInstitutionNotificationId);
                                });
                            }
                        }
                    },
                    error: function(xhr) {
                        $('#institutionModalBody').html('<div class="alert alert-danger">Error loading institution details.</div>');
                        console.error('Error loading institution details:', xhr);
                    }
                });
            }

            function updateInstitutionStatus(status, institutionId, notificationId) {
                $.ajax({
                    url: 'update-institution-status',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        institution_id: institutionId,
                        status: status
                    },
                    success: function(response) {
                        // Mark notification as read
                        if (notificationId) {
                            $.ajax({
                                url: '/admin/notifications/' + notificationId + '/read',
                                type: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });
                        }

                        // Reload the page to update everything
                        location.reload();
                    },
                    error: function(xhr) {
                        console.error('Error updating institution status:', xhr);
                        alert('Error updating institution status');
                    }
                });
            }

            function loadStudentDetails(id, notificationId) {
                if (notificationId) {
                    currentStudentNotificationId = notificationId;
                }
                $('#studentModalBody').html('<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');

                $.ajax({
                    url: 'students/' + id + '/details',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            var data = response.data;
                            var statusBadge = data.status == 1
                                ? '<span class="badge badge-success">Approved</span>'
                                : '<span class="badge badge-warning">Pending</span>';

                            var html = `
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="font-weight-bold">Student Information</h6>
                                        <table class="table table-sm">
                                            <tr>
                                                <td><strong>Name:</strong></td>
                                                <td>${data.name || 'N/A'}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Email:</strong></td>
                                                <td>${data.email || 'N/A'}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Phone:</strong></td>
                                                <td>${data.phone || 'N/A'}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Class:</strong></td>
                                                <td>${data.class || 'N/A'}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Gender:</strong></td>
                                                <td>${data.gender ? data.gender.charAt(0).toUpperCase() + data.gender.slice(1) : 'N/A'}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>DOB:</strong></td>
                                                <td>${data.dob || 'N/A'}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Roll No:</strong></td>
                                                <td>${data.roll_number || 'N/A'}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Institution:</strong></td>
                                                <td>${data.institution || 'N/A'}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Status:</strong></td>
                                                <td>${statusBadge}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Added On:</strong></td>
                                                <td>${data.created_at || 'N/A'}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            `;

                            $('#studentModalBody').html(html);

                            // Show/hide status buttons based on current status
                            if (data.status == 0) {
                                $('#approveStudentBtn').show().off('click').on('click', function() {
                                    if (!confirm('Are you sure you want to approve this student?')) {
                                        return;
                                    }
                                    updateStudentStatus(1, id, currentStudentNotificationId);
                                });
                                $('#rejectStudentBtn').hide();
                            } else {
                                $('#approveStudentBtn').hide();
                                $('#rejectStudentBtn').show().off('click').on('click', function() {
                                    if (!confirm('Are you sure you want to reject this student?')) {
                                        return;
                                    }
                                    updateStudentStatus(0, id, currentStudentNotificationId);
                                });
                            }
                        }
                    },
                    error: function(xhr) {
                        $('#studentModalBody').html('<div class="alert alert-danger">Error loading student details.</div>');
                        console.error('Error loading student details:', xhr);
                    }
                });
            }

            function updateStudentStatus(status, studentId, notificationId) {
                $.ajax({
                    url: 'students/' + studentId + '/update-status',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        status: status
                    },
                    success: function(response) {
                        // Mark notification as read
                        if (notificationId) {
                            $.ajax({
                                url: '/admin/notifications/' + notificationId + '/read',
                                type: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });
                        }

                        // Reload page to reflect updates
                        location.reload();
                    },
                    error: function(xhr) {
                        console.error('Error updating student status:', xhr);
                        alert('Error updating student status');
                    }
                });
            }

            // View Sales Executive Details Modal (same pattern as Institution modal)
            $(document).on('click', '.view-sales-executive', function(e) {
                e.preventDefault();
                currentSalesExecutiveId = $(this).data('id');
                currentNotificationId = $(this).data('notification-id');

                // Reset modal state
                $('#approveSalesExecutiveBtn').hide();
                $('#rejectSalesExecutiveBtn').hide();

                // Show modal
                $('#salesExecutiveModal').modal('show');

                // Load sales executive details
                loadSalesExecutiveDetails(currentSalesExecutiveId);
            });

            // Reset modal when closed
            $('#salesExecutiveModal').on('hidden.bs.modal', function () {
                currentSalesExecutiveId = null;
                currentNotificationId = null;
                $('#salesExecutiveModalBody').html('<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');
            });

            function loadSalesExecutiveDetails(id) {
                $('#salesExecutiveModalBody').html('<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');

                $.ajax({
                    url: '{{ url("admin/sales-executive") }}/' + id + '/details',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            var data = response.data;
                            var statusBadge = data.status == 1
                                ? '<span class="badge badge-success">Active</span>'
                                : '<span class="badge badge-warning">Inactive (Pending Approval)</span>';

                            var html = `
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="font-weight-bold">Personal Information</h6>
                                        <table class="table table-sm">
                                            <tr>
                                                <td><strong>Name:</strong></td>
                                                <td>${data.name || 'N/A'}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Email:</strong></td>
                                                <td>${data.email || 'N/A'}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Phone:</strong></td>
                                                <td>${data.phone || 'N/A'}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Status:</strong></td>
                                                <td>${statusBadge}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Registered On:</strong></td>
                                                <td>${data.created_at || 'N/A'}</td>
                                            </tr>
                                        </table>
                                    </div>

                                </div>

                            `;

                            $('#salesExecutiveModalBody').html(html);

                            // Show/hide status buttons based on current status
                            if (data.status == 0) {
                                $('#approveSalesExecutiveBtn').show();
                                $('#rejectSalesExecutiveBtn').hide();
                            } else {
                                $('#approveSalesExecutiveBtn').hide();
                                $('#rejectSalesExecutiveBtn').show();
                            }
                        }
                    },
                    error: function(xhr) {
                        $('#salesExecutiveModalBody').html('<div class="alert alert-danger">Error loading sales executive details.</div>');
                        console.error('Error loading sales executive details:', xhr);
                    }
                });
            }

            // Approve Sales Executive (Activate - set status to 1)
            $('#approveSalesExecutiveBtn').on('click', function() {
                if (!confirm('Are you sure you want to approve (activate) this sales executive?')) {
                    return;
                }

                updateSalesExecutiveStatus('Active');
            });

            // Reject Sales Executive (Deactivate - set status to 0)
            $('#rejectSalesExecutiveBtn').on('click', function() {
                if (!confirm('Are you sure you want to reject (deactivate) this sales executive?')) {
                    return;
                }

                updateSalesExecutiveStatus('Inactive');
            });

            function updateSalesExecutiveStatus(statusText) {
                $.ajax({
                    url: '{{ url("admin/update-sales-executive-status") }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        sales_executive_id: currentSalesExecutiveId,
                        status: statusText
                    },
                    success: function(response) {
                        // Mark notification as read
                        if (currentNotificationId) {
                            $.ajax({
                                url: '{{ url("admin/notifications") }}/' + currentNotificationId + '/read',
                                type: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });
                        }

                        // Reload the page to update everything
                        location.reload();
                    },
                    error: function(xhr) {
                        console.error('Error updating sales executive status:', xhr);
                        alert('Error updating sales executive status');
                    }
                });
            }

            // Mark individual notification as read
            $('.mark-as-read').on('click', function(e) {
                e.preventDefault();
                var notificationId = $(this).data('id');
                var row = $(this).closest('tr');

                $.ajax({
                    url: '{{ url("admin/notifications") }}/' + notificationId + '/read',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Remove warning class and update status
                            row.removeClass('table-warning');
                            row.find('strong').removeClass('font-weight-bold');
                            row.find('.badge-warning').replaceWith('<span class="badge badge-success">Read</span>');
                            row.find('.mark-as-read').remove();

                            // Show success message
                            alert('Notification marked as read');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error marking notification as read:', xhr);
                        alert('Error marking notification as read');
                    }
                });
            });

            // Mark all notifications as read
            $('#markAllReadBtn').on('click', function() {
                if (!confirm('Mark all notifications as read?')) {
                    return;
                }

                $.ajax({
                    url: '{{ url("admin/notifications/mark-all-read") }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Reload the page to update all notifications
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        console.error('Error marking all notifications as read:', xhr);
                        alert('Error marking all notifications as read');
                    }
                });
            });
        });
    </script>
    @endpush
@endsection

