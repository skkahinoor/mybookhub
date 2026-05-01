@extends('admin.layout.layout')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Withdrawal Requests Management</h4>

                        @if(Session::has('success_message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Success:</strong> {{ Session::get('success_message') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if(Session::has('error_message'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error:</strong> {{ Session::get('error_message') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <form method="POST" action="{{ route('admin.withdrawals.minimum.update') }}" class="card">
                                    @csrf
                                    <div class="card-body">
                                        <h6 class="card-title mb-3">Minimum Withdrawal Amount</h6>
                                        <div class="form-group mb-3">
                                            <label for="minimum_withdrawal_amount" class="form-label">Amount (₹)</label>
                                            <input type="number"
                                                   step="0.01"
                                                   min="1"
                                                   class="form-control"
                                                   id="minimum_withdrawal_amount"
                                                   name="minimum_withdrawal_amount"
                                                   value="{{ old('minimum_withdrawal_amount', $minimumWithdrawal) }}"
                                                   required>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                        <small class="text-muted d-block mt-2">Current minimum: ₹{{ number_format($minimumWithdrawal, 2) }}</small>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Statistics Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h6 class="mb-1">Pending Requests</h6>
                                        <h3>{{ $pendingCount }}</h3>
                                        <small>₹{{ number_format($pendingAmount, 2) }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h6 class="mb-1">Approved</h6>
                                        <h3>{{ $approvedCount }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h6 class="mb-1">Completed</h6>
                                        <h3>{{ $completedCount }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h6 class="mb-1">Total Processed</h6>
                                        <h3>₹{{ number_format($totalAmount, 2) }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive pt-3">
                            <table class="table table-bordered" id="withdrawalsTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Sales Executive</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Status</th>
                                        <th>Requested Date</th>
                                        <th>Processed Date</th>
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
    @include('admin.layout.footer')
</div>

<!-- DataTables Bootstrap 4 CSS CDN -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">

<!-- jQuery CDN (required for DataTables) -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<!-- DataTables JS CDN -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#withdrawalsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.withdrawals.index') }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'sales_executive', name: 'salesExecutive.name'},
                {data: 'amount', name: 'amount'},
                {data: 'payment_method', name: 'payment_method'},
                {data: 'status', name: 'status'},
                {data: 'created_at', name: 'created_at'},
                {data: 'processed_date', name: 'processed_at'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            order: [[0, 'desc']],
            pageLength: 25
        });
    });
</script>
@endsection

