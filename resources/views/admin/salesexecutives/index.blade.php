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
                                                <td>
                                                    @if ($se->status == 1)
                                                        {{-- Active: green circle with tick --}}
                                                        <span class="badge bg-success d-inline-flex align-items-center justify-content-center text-white"
                                                              style="width:20px;height:20px;border-radius:32%;font-size:16px;padding:5px !importatnt;">
                                                            <i class="mdi mdi-check"></i>
                                                        </span>
                                                    @else
                                                        {{-- Inactive: red circle with cross --}}
                                                        <span class="badge bg-danger d-inline-flex align-items-center justify-content-center text-white"
                                                              style="width:20px;height:20px;border-radius:32%;font-size:16px; padding:5px !importatnt;">
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

    <!-- Sales Executive Details Modal -->
    <div class="modal fade" id="salesExecutiveModal" tabindex="-1" role="dialog"
        aria-labelledby="salesExecutiveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="salesExecutiveModalLabel">Sales Executive Details</h5>
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
                    <button type="button" class="btn btn-danger" id="deactivateSalesExecutiveBtn" style="display: none;">
                        <i class="mdi mdi-close"></i> Inactive
                    </button>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
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

            var currentSalesExecutiveId = null;

            // View Sales Executive Details Modal
            $(document).on('click', '.view-sales-executive', function(e) {
                e.preventDefault();
                currentSalesExecutiveId = $(this).data('id');

                // Reset modal state
                $('#approveSalesExecutiveBtn').hide();
                $('#deactivateSalesExecutiveBtn').hide();

                // Show modal
                $('#salesExecutiveModal').modal('show');

                // Load sales executive details
                loadSalesExecutiveDetails(currentSalesExecutiveId);
            });

            // Reset modal when closed
            $('#salesExecutiveModal').on('hidden.bs.modal', function() {
                currentSalesExecutiveId = null;
                $('#salesExecutiveModalBody').html(
                    '<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>'
                );
            });

            function loadSalesExecutiveDetails(id) {
                $('#salesExecutiveModalBody').html(
                    '<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>'
                );

                $.ajax({
                    url: '{{ url('admin/sales-executive') }}/' + id + '/details',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            var data = response.data;
                            var statusBadge = data.status == 1 ?
                                '<span class="badge badge-success">Active</span>' :
                                '<span class="badge badge-warning">Inactive (Pending Approval)</span>';

                            var html = `
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="font-weight-bold mb-3">Personal Information</h6>
                                        <table class="table table-sm table-bordered">
                                            <tr>
                                                <td style="width: 40%;"><strong>Name:</strong></td>
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
                                                <td><strong>Address:</strong></td>
                                                <td>${data.address || 'N/A'}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>City:</strong></td>
                                                <td>${data.city || 'N/A'}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>District:</strong></td>
                                                <td>${data.district || 'N/A'}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>State:</strong></td>
                                                <td>${data.state || 'N/A'}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Country:</strong></td>
                                                <td>${data.country || 'N/A'}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Pincode:</strong></td>
                                                <td>${data.pincode || 'N/A'}</td>
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

                            // Show approve / deactivate buttons based on status
                            if (data.status == 0) {
                                $('#approveSalesExecutiveBtn').show();
                                $('#deactivateSalesExecutiveBtn').hide();
                            } else if (data.status == 1) {
                                $('#approveSalesExecutiveBtn').hide();
                                $('#deactivateSalesExecutiveBtn').show();
                            } else {
                                $('#approveSalesExecutiveBtn').hide();
                                $('#deactivateSalesExecutiveBtn').hide();
                            }
                        }
                    },
                    error: function(xhr) {
                        $('#salesExecutiveModalBody').html(
                            '<div class="alert alert-danger">Error loading sales executive details.</div>'
                        );
                        console.error('Error loading sales executive details:', xhr);
                    }
                });
            }

            // Approve Sales Executive (Activate - set status to 1)
            $('#approveSalesExecutiveBtn').on('click', function() {
                if (!confirm('Are you sure you want to approve (activate) this sales executive?')) {
                    return;
                }

                $.ajax({
                    url: '{{ url('admin/update-sales-executive-status') }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        sales_executive_id: currentSalesExecutiveId,
                        status: 'Active'
                    },
                    success: function(response) {
                        $('#salesExecutiveModal').modal('hide');
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Error updating sales executive status.');
                        console.error('Error updating status:', xhr);
                    }
                });
            });

            // Deactivate Sales Executive (set status to 0)
            $('#deactivateSalesExecutiveBtn').on('click', function() {
                if (!confirm('Are you sure you want to deactivate this sales executive?')) {
                    return;
                }

                $.ajax({
                    url: '{{ url('admin/update-sales-executive-status') }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        sales_executive_id: currentSalesExecutiveId,
                        status: 'Inactive'
                    },
                    success: function(response) {
                        $('#salesExecutiveModal').modal('hide');
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Error updating sales executive status.');
                        console.error('Error updating status:', xhr);
                    }
                });
            });
        });
    </script>
@endsection
