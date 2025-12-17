@extends('layouts.app')
@section('title')
    Withdrawal Requests
@endsection
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="page-title mb-0">Withdrawal Requests</h2>
                    <p class="text-muted mb-0">Manage your withdrawal requests</p>
                </div>
                @if($canWithdraw)
                    <a href="{{ route('sales.withdrawals.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Request Withdrawal
                    </a>
                @else
                    <button class="btn btn-secondary" disabled title="You need at least ₹{{ number_format($minimumWithdrawal, 2) }} available to request withdrawal">
                        <i class="bi bi-lock me-1"></i> Request Withdrawal
                    </button>
                @endif
            </div>
        </div>
    </div>

    @if(Session::has('success_message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> {{ Session::get('success_message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(Session::has('error_message'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> {{ Session::get('error_message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Balance Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1">Total Earnings</p>
                            <h3 class="mb-0">₹{{ number_format($totalEarning, 2) }}</h3>
                        </div>
                        <i class="bi bi-graph-up-arrow fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1">Available Balance</p>
                            <h3 class="mb-0">₹{{ number_format($availableBalance, 2) }}</h3>
                        </div>
                        <i class="bi bi-wallet2 fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1">Total Withdrawn</p>
                            <h3 class="mb-0">₹{{ number_format($totalWithdrawn, 2) }}</h3>
                        </div>
                        <i class="bi bi-cash-stack fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Minimum Earning Requirement -->
    @if(!$canWithdraw)
        <div class="alert alert-warning mb-4">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Minimum Balance Required:</strong> You need at least ₹{{ number_format($minimumWithdrawal, 2) }} available before you can request a withdrawal. Your current available balance is ₹{{ number_format($availableBalance, 2) }}.
        </div>
    @endif

    <!-- Withdrawal Requests Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">Withdrawal History</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Requested Date</th>
                            <th>Processed Date</th>
                            <th>Transaction ID</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($withdrawals as $index => $withdrawal)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>₹{{ number_format($withdrawal->amount, 2) }}</strong></td>
                                <td>
                                    @if($withdrawal->payment_method == 'bank_transfer')
                                        <span class="badge bg-info">Bank Transfer</span>
                                    @else
                                        <span class="badge bg-success">UPI</span>
                                    @endif
                                </td>
                                <td>
                                    @if($withdrawal->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($withdrawal->status == 'approved')
                                        <span class="badge bg-info">Approved</span>
                                    @elseif($withdrawal->status == 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>{{ $withdrawal->created_at->format('d M Y, h:i A') }}</td>
                                <td>{{ $withdrawal->processed_at ? $withdrawal->processed_at->format('d M Y, h:i A') : 'N/A' }}</td>
                                <td>
                                    @if($withdrawal->transaction_id)
                                        <span class="badge bg-primary">{{ $withdrawal->transaction_id }}</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $withdrawal->remarks ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                    <p class="text-muted mb-0">No withdrawal requests found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

