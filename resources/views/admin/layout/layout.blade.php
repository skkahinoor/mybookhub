<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">



    {{-- X-CSRF-TOKEN: https://laravel.com/docs/9.x/csrf#csrf-x-csrf-token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">



    <title>Admin Panel</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ url('admin/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ url('admin/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ url('admin/vendors/css/vendor.bundle.base.css') }}">
    <!-- endinject -->

    {{-- Font Awesome --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
          integrity="sha512-LX1L+z..."
          crossorigin="anonymous"
          referrerpolicy="no-referrer" />

    <!-- Plugin css for this page (The icons from Skydash Admin Panel template) -->
    <link rel="stylesheet" href="{{ url('admin/vendors/mdi/css/materialdesignicons.min.css') }}">


    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{ url('admin/vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ url('admin/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('admin/js/select.dataTables.min.css') }}">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ url('admin/css/vertical-layout-light/style.css') }}">
    <!-- endinject -->
    <link rel="shortcut icon" href="{{ asset('uploads/favicons/' . $headerLogo->favicon) }}" />

    {{-- DataTable --}}
    <link rel="stylesheet" href="{{ url('admin/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ url('admin/css/dataTables.bootstrap4.min.css') }}">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

</head>

<body>
    <div class="container-scroller">

        @include('admin.layout.header')

        @php
            use App\Models\HeaderLogo;
            $headerLogo = HeaderLogo::first();
        @endphp

        <div class="navbar-brand-wrapper">
            <a class="navbar-brand" href="{{ url('admin/dashboard') }}">
                <img src="{{ asset('uploads/logos/' . ($headerLogo->logo ?? 'default.png')) }}" alt="logo"
                    height="50">
            </a>
        </div>




        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial -->




            @include('admin.layout.sidebar')




            <!-- partial -->
            <!-- container-scroller -->




            <!-- Middle Content (varies from a page to another) -->
            @yield('content')




            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>

    <!-- jQuery first -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



    <!-- Then DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />



    <!-- plugins:js -->
    <script src="{{ url('admin/vendors/js/vendor.bundle.base.js') }}"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="{{ url('admin/vendors/chart.js/Chart.min.js') }}"></script>
    <script src="{{ url('admin/vendors/datatables.net/jquery.dataTables.js') }}"></script>
    <script src="{{ url('admin/vendors/datatables.net-bs4/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ url('admin/js/dataTables.select.min.js') }}"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{ url('admin/js/off-canvas.js') }}"></script>
    <script src="{{ url('admin/js/hoverable-collapse.js') }}"></script>
    <script src="{{ url('admin/js/template.js') }}"></script>
    <script src="{{ url('admin/js/settings.js') }}"></script>
    <script src="{{ url('admin/js/todolist.js') }}"></script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    <script src="{{ url('admin/js/dashboard.js') }}"></script>
    <script src="{{ url('admin/js/Chart.roundedBarCharts.js') }}"></script>
    <!-- End custom js for this page-->



    {{-- NOTE: I MOVED THIS SECTION TO admin/js/My-Sweet-Alert.js FILE! After the SweetAlert2 CDN link block in the Country! I downloaded the library using 'npm' --}}
    {{-- The SweetAlert2 package for Confirm Deletion Message in sections.blade.php --}}
    {{-- <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}} <!-- CDNs blocked in Country! -->
    {{-- <script type="module" src="{{ url('admin/js/sweetalert2.js') }}"></script>
        <script type="module" src="{{ url('admin/js/My-Sweet-Alert.js') }}"></script> --}}




    {{-- Start: Our Custom Admin JS --}}
    <script src="{{ url('admin/js/custom.js') }}"></script>

    {{-- End: Our Custom Admin JS --}}


    @stack('scripts')
    {{-- Notification Scripts --}}
    <script>
    (function() {
        // Wait for jQuery to be available
        function initNotifications() {
            if (typeof jQuery === 'undefined') {
                setTimeout(initNotifications, 100);
                return;
            }
            
            var $ = jQuery;
            
            $(document).ready(function() {
                // Load notifications on page load
                loadNotifications();

                // Refresh notifications every 30 seconds
                setInterval(loadNotifications, 30000);

                function loadNotifications() {
                    $.ajax({
                        url: '{{ url("admin/notifications/get") }}',
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            // Check if response is valid
                            if (!response || typeof response !== 'object') {
                                console.error('Invalid response format:', response);
                                $('#notificationsList').html('<div class="dropdown-item preview-item"><div class="preview-item-content"><h6 class="preview-subject font-weight-normal">Error loading notifications</h6></div></div>');
                                return;
                            }
                            
                            // Update notification count
                            var unreadCount = response.unreadCount || 0;
                            $('#notificationCount').text(unreadCount);
                            
                            // Update notification count badge visibility
                            if (unreadCount > 0) {
                                $('#notificationCount').show();
                            } else {
                                $('#notificationCount').hide();
                            }

                            // Update notifications list
                            var notificationsHtml = '';
                            var notifications = response.notifications || [];
                            
                            if (notifications.length > 0) {
                                notifications.forEach(function(notification) {
                                    var timeAgo = getTimeAgo(notification.created_at);
                                    var isReadClass = notification.is_read ? '' : 'font-weight-bold';
                                    
                                    // Determine icon and link based on notification type
                                    var iconClass = 'ti-bell';
                                    var iconBg = 'bg-warning';
                                    var linkUrl = '#';
                                    
                                    if (notification.type === 'withdrawal_request' && notification.related_id) {
                                        iconClass = 'ti-money';
                                        iconBg = 'bg-info';
                                        linkUrl = 'withdrawals/' + notification.related_id;
                                    } else if (notification.type === 'sales_executive_registration' && notification.related_id) {
                                        iconClass = 'ti-user';
                                        iconBg = 'bg-success';
                                        linkUrl = 'add-edit-sales-executive/' + notification.related_id;
                                    } else if (notification.type === 'institution_added' && notification.related_id) {
                                        iconClass = 'ti-bookmark-alt';
                                        iconBg = 'bg-warning';
                                        linkUrl = 'notifications?view-inst=' + notification.related_id + '&notif=' + notification.id;
                                    }
                                    
                                    notificationsHtml += `
                                        <a href="${linkUrl}" class="dropdown-item preview-item notification-item ${isReadClass}" data-id="${notification.id}" data-type="${notification.type}" data-related-id="${notification.related_id || ''}">
                                            <div class="preview-thumbnail">
                                                <div class="preview-icon ${iconBg}">
                                                    <i class="${iconClass} mx-0"></i>
                                                </div>
                                            </div>
                                            <div class="preview-item-content">
                                                <h6 class="preview-subject ${isReadClass}">${notification.title}</h6>
                                                <p class="font-weight-light small-text mb-0 text-muted">${notification.message}</p>
                                                <p class="font-weight-light small-text mb-0 text-muted">${timeAgo}</p>
                                            </div>
                                        </a>
                                    `;
                                });
                            } else {
                                notificationsHtml = `
                                    <div class="dropdown-item preview-item">
                                        <div class="preview-item-content">
                                            <h6 class="preview-subject font-weight-normal">No notifications</h6>
                                            <p class="font-weight-light small-text mb-0 text-muted">You're all caught up!</p>
                                        </div>
                                    </div>
                                `;
                            }
                            $('#notificationsList').html(notificationsHtml);

                            // Add click handler for notifications
                            $('.notification-item').on('click', function(e) {
                                var notificationId = $(this).data('id');
                                var linkUrl = $(this).attr('href');
                                var notificationType = $(this).data('type');
                                var relatedId = $(this).data('related-id');
                                
                                // Mark as read in background (don't wait for response)
                                if (notificationId) {
                                    markAsRead(notificationId, false); // false = don't reload notifications
                                }
                                
                                // Handle sales executive registration - navigate to notifications page to open modal
                                if (notificationType === 'sales_executive_registration' && relatedId) {
                                    e.preventDefault();
                                    // Navigate to notifications page which has the modal
                                    window.location.href = 'notifications?view-se=' + relatedId + '&notif=' + notificationId;
                                    return false;
                                }
                                
                                // Handle institution added - navigate to notifications page to open modal
                                if (notificationType === 'institution_added' && relatedId) {
                                    e.preventDefault();
                                    // Navigate to notifications page which has the modal
                                    window.location.href = 'notifications?view-inst=' + relatedId + '&notif=' + notificationId;
                                    return false;
                                }
                                
                                // For withdrawal requests and other types, allow navigation
                                // If it's just a hash link, prevent default navigation
                                if (!linkUrl || linkUrl === '#') {
                                    e.preventDefault();
                                }
                                // Otherwise allow navigation to proceed
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('Error loading notifications:', {
                                status: status,
                                error: error,
                                response: xhr.responseText,
                                statusCode: xhr.status
                            });
                            
                            // Show error message in dropdown
                            $('#notificationsList').html(`
                                <div class="dropdown-item preview-item">
                                    <div class="preview-item-content">
                                        <h6 class="preview-subject font-weight-normal text-danger">Error loading notifications</h6>
                                        <p class="font-weight-light small-text mb-0 text-muted">Please refresh the page</p>
                                    </div>
                                </div>
                            `);
                        }
                    });
                }

                function markAsRead(notificationId, reloadNotifications = true) {
                    $.ajax({
                        url: '{{ url("admin/notifications") }}/' + notificationId + '/read',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            // Reload notifications after marking as read (only if requested)
                            if (reloadNotifications) {
                                loadNotifications();
                            } else {
                                // Just update the count without full reload
                                var currentCount = parseInt($('#notificationCount').text()) || 0;
                                if (currentCount > 0) {
                                    $('#notificationCount').text(currentCount - 1);
                                    if (currentCount - 1 === 0) {
                                        $('#notificationCount').hide();
                                    }
                                }
                            }
                        },
                        error: function(xhr) {
                            console.error('Error marking notification as read:', xhr);
                        }
                    });
                }

                function getTimeAgo(dateString) {
                    var date = new Date(dateString);
                    var now = new Date();
                    var seconds = Math.floor((now - date) / 1000);
                    
                    if (seconds < 60) {
                        return seconds + ' seconds ago';
                    } else if (seconds < 3600) {
                        var minutes = Math.floor(seconds / 60);
                        return minutes + ' minute' + (minutes > 1 ? 's' : '') + ' ago';
                    } else if (seconds < 86400) {
                        var hours = Math.floor(seconds / 3600);
                        return hours + ' hour' + (hours > 1 ? 's' : '') + ' ago';
                    } else {
                        var days = Math.floor(seconds / 86400);
                        return days + ' day' + (days > 1 ? 's' : '') + ' ago';
                    }
                }

                // Function to open sales executive modal (available globally)
                window.openSalesExecutiveModal = function(salesExecutiveId, notificationId) {
                    // Check if modal exists (it's only on notifications page)
                    if ($('#salesExecutiveModal').length === 0) {
                        // If modal doesn't exist, navigate to notifications page
                        window.location.href = 'notifications';
                        return;
                    }
                    
                    // Reset modal state
                    $('#approveSalesExecutiveBtn').hide();
                    $('#rejectSalesExecutiveBtn').hide();
                    
                    // Show modal
                    $('#salesExecutiveModal').modal('show');
                    
                    // Load sales executive details
                    loadSalesExecutiveDetails(salesExecutiveId, notificationId);
                };

                function loadSalesExecutiveDetails(id, notificationId) {
                    $('#salesExecutiveModalBody').html('<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');
                    
                    $.ajax({
                        url: '/admin/sales-executive/' + id + '/details',
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
                                    $('#approveSalesExecutiveBtn').show().off('click').on('click', function() {
                                        if (!confirm('Are you sure you want to approve (activate) this sales executive?')) {
                                            return;
                                        }
                                        updateSalesExecutiveStatus('Active', id, notificationId);
                                    });
                                    $('#rejectSalesExecutiveBtn').hide();
                                } else {
                                    $('#approveSalesExecutiveBtn').hide();
                                    $('#rejectSalesExecutiveBtn').show().off('click').on('click', function() {
                                        if (!confirm('Are you sure you want to reject (deactivate) this sales executive?')) {
                                            return;
                                        }
                                        updateSalesExecutiveStatus('Inactive', id, notificationId);
                                    });
                                }
                            }
                        },
                        error: function(xhr) {
                            $('#salesExecutiveModalBody').html('<div class="alert alert-danger">Error loading sales executive details.</div>');
                            console.error('Error loading sales executive details:', xhr);
                        }
                    });
                }

                function updateSalesExecutiveStatus(statusText, salesExecutiveId, notificationId) {
                    $.ajax({
                        url: '/admin/update-sales-executive-status',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            sales_executive_id: salesExecutiveId,
                            status: statusText
                        },
                        success: function(response) {
                            // Mark notification as read
                            if (notificationId) {
                                markAsRead(notificationId, false);
                            }
                            
                            // Reload notifications and close modal
                            loadNotifications();
                            $('#salesExecutiveModal').modal('hide');
                            
                            // Show success message
                            alert('Sales executive status updated successfully!');
                        },
                        error: function(xhr) {
                            console.error('Error updating sales executive status:', xhr);
                            alert('Error updating sales executive status');
                        }
                    });
                }
            });
        }
        
        // Start initialization
        initNotifications();
    })();
    </script>

</body>

</html>
