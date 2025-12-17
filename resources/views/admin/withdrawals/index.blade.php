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
                                    @foreach($withdrawals as $key => $withdrawal)
                                    <tr>
                                        <td>{{ $withdrawal->id }}</td>
                                        <td>
                                            <strong>{{ $withdrawal->salesExecutive->name ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">{{ $withdrawal->salesExecutive->email ?? 'N/A' }}</small>
                                        </td>
                                        <td><strong>₹{{ number_format($withdrawal->amount, 2) }}</strong></td>
                                        <td>
                                            @if($withdrawal->payment_method == 'bank_transfer')
                                                <span class="badge badge-info">Bank Transfer</span>
                                            @else
                                                <span class="badge badge-success">UPI</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($withdrawal->status == 'pending')
                                                <span class="badge badge-warning">Pending</span>
                                            @elseif($withdrawal->status == 'approved')
                                                <span class="badge badge-info">Approved</span>
                                            @elseif($withdrawal->status == 'completed')
                                                <span class="badge badge-success">Completed</span>
                                            @else
                                                <span class="badge badge-danger">Rejected</span>
                                            @endif
                                        </td>
                                        <td>{{ $withdrawal->created_at->format('d M Y, h:i A') }}</td>
                                        <td>{{ $withdrawal->processed_at ? $withdrawal->processed_at->format('d M Y, h:i A') : 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('admin.withdrawals.show', $withdrawal->id) }}" 
                                               class="btn btn-sm btn-primary" title="View Details">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
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
            order: [[0, 'desc']],
            pageLength: 25
        });
    });
</script>
@endsection

