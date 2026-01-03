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
                                <a href="{{ route('sales_executives.add_edit') }}"
                                    class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
                                    <i class="mdi mdi-plus fs-5"></i> Add Sales Executive
                                </a>
                            </div>


                            <style>
                                .status-icon {
                                    width: 28px;
                                    height: 28px;
                                    border-radius: 35%;
                                    display: inline-flex;
                                    align-items: center;
                                    justify-content: center;
                                    font-size: 15px;
                                    line-height: 1;
                                }
                                </style>



                            @if (Session::has('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success:</strong> {{ Session::get('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="table-responsive pt-3">
                                <table class="table table-bordered" id="sales-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($salesExecutives as $se)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $se->name }}</td>
                                                <td>{{ $se->email }}</td>
                                                <td>{{ $se->phone }}</td>
                                                <td class="text-center">
                                                    @if ($se->status == 1)
                                                        {{-- Active --}}
                                                        <span class="badge bg-success status-icon text-white">
                                                            <i class="mdi mdi-check"></i>
                                                        </span>
                                                    @else
                                                        {{-- Inactive --}}
                                                        <span class="badge bg-danger status-icon text-white">
                                                            <i class="mdi mdi-close"></i>
                                                        </span>
                                                    @endif
                                                </td>



                                                <td>
                                                    <div class="d-flex align-items-center" style="gap: 10px;">
                                                        <a href="javascript:void(0)" class="view-sales-executive"
                                                            data-id="{{ $se->id }}" title="View Details">
                                                            <i style="font-size: 20px; color: #a71d84;" class="mdi mdi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('sales_executives.add_edit', $se->id) }}"
                                                            title="Edit">
                                                            <i style="font-size: 20px" class="mdi mdi-pencil"></i>
                                                        </a>
                                                        <a href="{{ route('sales_executives.delete', $se->id) }}"
                                                            title="Delete"
                                                            onclick="return confirm('Delete this sales executive?');">
                                                            <i style="font-size: 20px; color: #e74c3c;"
                                                                class="mdi mdi-delete"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
                <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2022. All rights
                    reserved.</span>
            </div>
        </footer>
    </div>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(function() {
            $('#sales-table').DataTable({
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                ordering: true,
                columnDefs: [{
                    orderable: false,
                    targets: [4, 5]
                }]
            });

            // View Sales Executive Details - Show SweetAlert
            $(document).on('click', '.view-sales-executive', function(e) {
                e.preventDefault();
                var salesExecutiveId = $(this).data('id');

                // Load sales executive details and show in SweetAlert
                loadSalesExecutiveDetails(salesExecutiveId);
            });

            function loadSalesExecutiveDetails(id) {
                // Show loading SweetAlert
                Swal.fire({
                    title: 'Loading...',
                    text: 'Please wait while we fetch the details',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '{{ url('admin/sales-executive') }}/' + id + '/details',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            var data = response.data;
                            var statusText = data.status == 1 ? 'Active' : 'Inactive (Pending Approval)';
                            var statusIcon = data.status == 1 ? 'success' : 'warning';

                            // Format the details as HTML
                            var detailsHtml = `
                                <div style="text-align: left; padding: 10px;">
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <tr style="border-bottom: 1px solid #eee;">
                                            <td style="padding: 8px; font-weight: bold; width: 40%;">Name:</td>
                                            <td style="padding: 8px;">${data.name || 'N/A'}</td>
                                        </tr>
                                        <tr style="border-bottom: 1px solid #eee;">
                                            <td style="padding: 8px; font-weight: bold;">Email:</td>
                                            <td style="padding: 8px;">${data.email || 'N/A'}</td>
                                        </tr>
                                        <tr style="border-bottom: 1px solid #eee;">
                                            <td style="padding: 8px; font-weight: bold;">Phone:</td>
                                            <td style="padding: 8px;">${data.phone || 'N/A'}</td>
                                        </tr>
                                        <tr style="border-bottom: 1px solid #eee;">
                                            <td style="padding: 8px; font-weight: bold;">Address:</td>
                                            <td style="padding: 8px;">${data.address || 'N/A'}</td>
                                        </tr>
                                        <tr style="border-bottom: 1px solid #eee;">
                                            <td style="padding: 8px; font-weight: bold;">City:</td>
                                            <td style="padding: 8px;">${data.city || 'N/A'}</td>
                                        </tr>
                                        <tr style="border-bottom: 1px solid #eee;">
                                            <td style="padding: 8px; font-weight: bold;">District:</td>
                                            <td style="padding: 8px;">${data.district || 'N/A'}</td>
                                        </tr>
                                        <tr style="border-bottom: 1px solid #eee;">
                                            <td style="padding: 8px; font-weight: bold;">State:</td>
                                            <td style="padding: 8px;">${data.state || 'N/A'}</td>
                                        </tr>
                                        <tr style="border-bottom: 1px solid #eee;">
                                            <td style="padding: 8px; font-weight: bold;">Country:</td>
                                            <td style="padding: 8px;">${data.country || 'N/A'}</td>
                                        </tr>
                                        <tr style="border-bottom: 1px solid #eee;">
                                            <td style="padding: 8px; font-weight: bold;">Pincode:</td>
                                            <td style="padding: 8px;">${data.pincode || 'N/A'}</td>
                                        </tr>
                                        <tr style="border-bottom: 1px solid #eee;">
                                            <td style="padding: 8px; font-weight: bold;">Status:</td>
                                            <td style="padding: 8px;">${statusText}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px; font-weight: bold;">Registered On:</td>
                                            <td style="padding: 8px;">${data.created_at || 'N/A'}</td>
                                        </tr>
                                    </table>
                                </div>
                            `;

                            // Store current status for use in callback
                            var currentStatus = data.status;
                            var actionStatus = currentStatus == 0 ? 'Active' : 'Inactive';

                            // Configure buttons based on status
                            var swalConfig = {
                                title: 'Sales Executive Details',
                                html: detailsHtml,
                                icon: statusIcon,
                                confirmButtonText: 'Close',
                                confirmButtonColor: '#a71d84',
                                width: '600px',
                                showDenyButton: false,
                                allowOutsideClick: true,
                                allowEscapeKey: true
                            };

                            // Show approve button if status is inactive (0)
                            if (currentStatus == 0) {
                                swalConfig.showDenyButton = true;
                                swalConfig.denyButtonText = '<i class="mdi mdi-check"></i> Approve (Activate)';
                                swalConfig.denyButtonColor = '#28a745';
                                swalConfig.confirmButtonText = 'Close';
                            }
                            // Show deactivate button if status is active (1)
                            else if (currentStatus == 1) {
                                swalConfig.showDenyButton = true;
                                swalConfig.denyButtonText = '<i class="mdi mdi-close"></i> Deactivate';
                                swalConfig.denyButtonColor = '#dc3545';
                                swalConfig.confirmButtonText = 'Close';
                            }

                            // Show details in SweetAlert
                            Swal.fire(swalConfig).then((result) => {
                                if (result.isDenied) {
                                    // Action button clicked (Approve or Deactivate)
                                    updateSalesExecutiveStatus(id, actionStatus);
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: 'Failed to load sales executive details',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error',
                            text: 'Error loading sales executive details. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        console.error('Error loading sales executive details:', xhr);
                    }
                });
            }

            // Function to update sales executive status
            function updateSalesExecutiveStatus(salesExecutiveId, status) {
                var confirmMessage = status === 'Active'
                    ? 'Are you sure you want to approve (activate) this sales executive?'
                    : 'Are you sure you want to deactivate this sales executive?';

                Swal.fire({
                    title: 'Confirm Action',
                    text: confirmMessage,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: status === 'Active' ? '#28a745' : '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, proceed',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Please wait',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: '{{ url('admin/update-sales-executive-status') }}',
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                sales_executive_id: salesExecutiveId,
                                status: status
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Sales executive status updated successfully',
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#a71d84'
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Error updating sales executive status. Please try again.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                                console.error('Error updating status:', xhr);
                            }
                        });
                    }
                });
            }

        });
    </script>
@endsection
