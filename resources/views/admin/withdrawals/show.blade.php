@extends('admin.layout.layout')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title mb-0">Withdrawal Request Details</h4>
                            <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left"></i> Back to List
                            </a>
                        </div>

                        @if(Session::has('success_message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Success:</strong> {{ Session::get('success_message') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="row">
                            <!-- Left Column - Withdrawal Info -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">Withdrawal Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Request ID:</strong></td>
                                                <td>#{{ $withdrawal->id }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Amount:</strong></td>
                                                <td><span class="h5 text-success">₹{{ number_format($withdrawal->amount, 2) }}</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Payment Method:</strong></td>
                                                <td>
                                                    @if($withdrawal->payment_method == 'bank_transfer')
                                                        <span class="badge badge-info">Bank Transfer</span>
                                                    @else
                                                        <span class="badge badge-success">UPI</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Status:</strong></td>
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
                                            </tr>
                                            <tr>
                                                <td><strong>Requested Date:</strong></td>
                                                <td>{{ $withdrawal->created_at->format('d M Y, h:i A') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Processed Date:</strong></td>
                                                <td>{{ $withdrawal->processed_at ? $withdrawal->processed_at->format('d M Y, h:i A') : 'Not Processed' }}</td>
                                            </tr>
                                            @if($withdrawal->transaction_id)
                                            <tr>
                                                <td><strong>Transaction ID:</strong></td>
                                                <td>{{ $withdrawal->transaction_id }}</td>
                                            </tr>
                                            @endif
                                            @if($withdrawal->remarks)
                                            <tr>
                                                <td><strong>Remarks:</strong></td>
                                                <td>{{ $withdrawal->remarks }}</td>
                                            </tr>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column - Sales Executive Info & Actions -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0">Sales Executive Details</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Name:</strong></td>
                                                <td>{{ $withdrawal->salesExecutive->name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Email:</strong></td>
                                                <td>{{ $withdrawal->salesExecutive->email ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Phone:</strong></td>
                                                <td>{{ $withdrawal->salesExecutive->phone ?? 'N/A' }}</td>
                                            </tr>
                                            @if($withdrawal->salesExecutive->bank_name)
                                            <tr>
                                                <td><strong>Bank Name:</strong></td>
                                                <td>{{ $withdrawal->salesExecutive->bank_name }}</td>
                                            </tr>
                                            @endif
                                            @if($withdrawal->salesExecutive->account_number)
                                            <tr>
                                                <td><strong>Account Number:</strong></td>
                                                <td>{{ $withdrawal->salesExecutive->account_number }}</td>
                                            </tr>
                                            @endif
                                            @if($withdrawal->salesExecutive->ifsc_code)
                                            <tr>
                                                <td><strong>IFSC Code:</strong></td>
                                                <td>{{ $withdrawal->salesExecutive->ifsc_code }}</td>
                                            </tr>
                                            @endif
                                            @if($withdrawal->salesExecutive->upi_id)
                                            <tr>
                                                <td><strong>UPI ID:</strong></td>
                                                <td>{{ $withdrawal->salesExecutive->upi_id }}</td>
                                            </tr>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Form -->
                        @if($withdrawal->status == 'pending')
                        <div class="card">
                            <div class="card-header bg-warning text-white">
                                <h5 class="mb-0">Manage Withdrawal Request</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('admin.withdrawals.updateStatus', $withdrawal->id) }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label"><strong>Action</strong></label>
                                            <select name="status" class="form-control" id="statusSelect" required>
                                                <option value="">Select Action</option>
                                                <option value="approved">Approve</option>
                                                <option value="rejected">Reject</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label"><strong>Transaction ID</strong> <small>(Optional)</small></label>
                                            <input type="text" name="transaction_id" class="form-control" placeholder="Enter transaction ID">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label"><strong>Remarks</strong></label>
                                            <input type="text" name="remarks" class="form-control" placeholder="Add remarks (optional)">
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="mdi mdi-check"></i> Update Status
                                            </button>
                                            <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-secondary">Cancel</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @elseif($withdrawal->status == 'approved')
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">Complete Withdrawal</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('admin.withdrawals.updateStatus', $withdrawal->id) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="completed">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label"><strong>Transaction ID</strong> <small>(Required)</small></label>
                                            <input type="text" name="transaction_id" class="form-control" placeholder="Enter transaction ID" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label"><strong>Remarks</strong></label>
                                            <input type="text" name="remarks" class="form-control" value="{{ $withdrawal->remarks }}" placeholder="Add remarks (optional)">
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-success">
                                                <i class="mdi mdi-check-circle"></i> Mark as Completed
                                            </button>
                                            <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-secondary">Cancel</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.layout.footer')
</div>
@endsection

