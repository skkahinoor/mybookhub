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
                                <a href="{{ route('delivery_agents.add_edit') }}"
                                    class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
                                    <i class="mdi mdi-plus fs-5"></i> Add Delivery Agent
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
                                <table class="table table-bordered" id="delivery-agents-table">
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
            $('#delivery-agents-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ url()->current() }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'phone', name: 'phone'},
                    {data: 'status', name: 'status', className: 'text-center', orderable: false, searchable: false},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                ordering: true,
                columnDefs: [{
                    orderable: false,
                    targets: [4, 5]
                }]
            });

            // View Delivery Agent Details - Show SweetAlert
            $(document).on('click', '.view-delivery-agent', function(e) {
                e.preventDefault();
                var deliveryAgentId = $(this).data('id');

                // Load delivery agent details and show in SweetAlert
                loadDeliveryAgentDetails(deliveryAgentId);
            });

            function loadDeliveryAgentDetails(id) {
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
                    url: '{{ url('admin/delivery-agent') }}/' + id + '/details',
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
                                            <td style="padding: 8px; font-weight: bold;">District:</td>
                                            <td style="padding: 8px;">${data.district || 'N/A'}</td>
                                        </tr>
                                        <tr style="border-bottom: 1px solid #eee;">
                                            <td style="padding: 8px; font-weight: bold;">Vehicle Type:</td>
                                            <td style="padding: 8px;">${data.vehicle_type || 'N/A'}</td>
                                        </tr>
                                        <tr style="border-bottom: 1px solid #eee;">
                                            <td style="padding: 8px; font-weight: bold;">License Number:</td>
                                            <td style="padding: 8px;">${data.license_number || 'N/A'}</td>
                                        </tr>
                                        <tr style="border-bottom: 1px solid #eee;">
                                            <td style="padding: 8px; font-weight: bold;">Status:</td>
                                            <td style="padding: 8px;">${statusText}</td>
                                        </tr>
                                        <tr style="border-bottom: 1px solid #eee;">
                                            <td style="padding: 8px; font-weight: bold;">ID Proof:</td>
                                            <td style="padding: 8px;">
                                                ${data.id_proof ? `<a href="${data.id_proof}" target="_blank" class="badge badge-info"><i class="mdi mdi-file-find"></i> View ID Proof</a>` : 'Not Uploaded'}
                                            </td>
                                        </tr>
                                        <tr style="border-bottom: 1px solid #eee;">
                                            <td style="padding: 8px; font-weight: bold;">License Image:</td>
                                            <td style="padding: 8px;">
                                                ${data.license_image ? `<a href="${data.license_image}" target="_blank" class="badge badge-info"><i class="mdi mdi-image"></i> View License</a>` : 'Not Uploaded'}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px; font-weight: bold;">Registered On:</td>
                                            <td style="padding: 8px;">${data.created_at || 'N/A'}</td>
                                        </tr>
                                    </table>
                                </div>
                            `;

                            var currentStatus = data.status;
                            var actionStatus = currentStatus == 0 ? 'Active' : 'Inactive';

                            var swalConfig = {
                                title: 'Delivery Agent Details',
                                html: detailsHtml,
                                icon: statusIcon,
                                confirmButtonText: 'Close',
                                confirmButtonColor: '#a71d84',
                                width: '600px',
                                showDenyButton: true,
                                allowOutsideClick: true,
                                allowEscapeKey: true
                            };

                            if (currentStatus == 0) {
                                swalConfig.denyButtonText = '<i class="mdi mdi-check"></i> Approve (Activate)';
                                swalConfig.denyButtonColor = '#28a745';
                            } else {
                                swalConfig.denyButtonText = '<i class="mdi mdi-close"></i> Deactivate';
                                swalConfig.denyButtonColor = '#dc3545';
                            }

                            Swal.fire(swalConfig).then((result) => {
                                if (result.isDenied) {
                                    updateDeliveryAgentStatus(id, actionStatus);
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: 'Failed to load delivery agent details',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error',
                            text: 'Error loading delivery agent details. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }

            function updateDeliveryAgentStatus(deliveryAgentId, status) {
                var confirmMessage = status === 'Active'
                    ? 'Are you sure you want to approve (activate) this delivery agent?'
                    : 'Are you sure you want to deactivate this delivery agent?';

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
                            url: '{{ url('admin/update-delivery-agent-status') }}',
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                delivery_agent_id: deliveryAgentId,
                                status: status
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Delivery agent status updated successfully',
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
                                    text: 'Error updating delivery agent status. Please try again.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    }
                });
            }
        });
    </script>
@endsection
