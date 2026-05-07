@extends('admin.layout.layout')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Delivery Partner Payouts</h4>
                        <p class="card-description">
                            Manage earnings and payout requests for delivery agents.
                        </p>

                        @if(Session::has('success_message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Success:</strong> {{ Session::get('success_message') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="table-responsive pt-3">
                            <table id="deliveryPayouts" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Request ID</th>
                                        <th>Agent Details</th>
                                        <th>Amount</th>
                                        <th>Request Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payouts as $payout)
                                        <tr>
                                            <td>#{{ $payout->id }}</td>
                                            <td>
                                                <strong>{{ $payout->deliveryAgent->user->name ?? 'N/A' }}</strong><br>
                                                <small>{{ $payout->deliveryAgent->user->phone ?? '' }}</small>
                                            </td>
                                            <td><strong class="text-primary">₹{{ number_format($payout->amount, 2) }}</strong></td>
                                            <td>{{ $payout->created_at->format('d M Y, h:i A') }}</td>
                                            <td>
                                                @if($payout->status == 'Pending')
                                                    <span class="badge badge-warning">Pending</span>
                                                @elseif($payout->status == 'Approved')
                                                    <span class="badge badge-success">Approved</span>
                                                @else
                                                    <span class="badge badge-danger">Rejected</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <button type="button" class="btn btn-sm btn-info mr-2 view-agent-bank" 
                                                            data-bank='@json($payout->deliveryAgent)' 
                                                            title="View Bank Details">
                                                        <i class="mdi mdi-bank"></i>
                                                    </button>

                                                    @if($payout->status == 'Pending')
                                                        <button type="button" class="btn btn-sm btn-primary update-payout-btn" 
                                                                data-id="{{ $payout->id }}" 
                                                                data-agent="{{ $payout->deliveryAgent->user->name }}"
                                                                data-amount="{{ $payout->amount }}"
                                                                title="Update Status">
                                                            Process
                                                        </button>
                                                    @else
                                                        <span class="text-muted small">
                                                            Method: {{ $payout->payment_method }}<br>
                                                            ID: {{ $payout->transaction_id }}
                                                        </span>
                                                    @endif
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
</div>

<!-- Bank Details Modal -->
<div class="modal fade" id="bankDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agent Bank Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="bankDetailsContent">
                <!-- Details will be injected here -->
            </div>
        </div>
    </div>
</div>

<!-- Update Payout Modal -->
<div class="modal fade" id="updatePayoutModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.update_delivery_agent_payout_status') }}" method="POST">
                @csrf
                <input type="hidden" name="payout_id" id="modal_payout_id">
                <div class="modal-header">
                    <h5 class="modal-title">Process Payout Request</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Processing payout for <strong><span id="modal_agent_name"></span></strong>.</p>
                    <p>Amount: <strong class="text-primary">₹<span id="modal_amount"></span></strong></p>
                    
                    <div class="form-group">
                        <label for="status">Action</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="Approved">Approve & Release</option>
                            <option value="Rejected">Reject</option>
                        </select>
                    </div>

                    <div class="form-group" id="transaction_group">
                        <label for="transaction_id">Transaction ID / Reference</label>
                        <input type="text" class="form-control" name="transaction_id" id="transaction_id" placeholder="Enter reference number">
                    </div>

                    <div class="form-group">
                        <label for="payment_method">Payment Method</label>
                        <select name="payment_method" class="form-control">
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="UPI">UPI</option>
                            <option value="Cash">Cash</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="admin_remarks">Admin Remarks (Optional)</label>
                        <textarea class="form-control" name="admin_remarks" rows="2" placeholder="Note for agent..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#deliveryPayouts').DataTable({
            "order": [[0, "desc"]]
        });

        $('.view-agent-bank').click(function() {
            var data = $(this).data('bank');
            var html = '<table class="table table-striped">';
            html += '<tr><th>Account Holder</th><td>' + (data.account_holder_name || 'N/A') + '</td></tr>';
            html += '<tr><th>Bank Name</th><td>' + (data.bank_name || 'N/A') + '</td></tr>';
            html += '<tr><th>Account Number</th><td>' + (data.account_number || 'N/A') + '</td></tr>';
            html += '<tr><th>IFSC Code</th><td>' + (data.ifsc_code || 'N/A') + '</td></tr>';
            html += '<tr><th>UPI ID</th><td>' + (data.upi_id || 'N/A') + '</td></tr>';
            html += '</table>';
            $('#bankDetailsContent').html(html);
            $('#bankDetailsModal').modal('show');
        });

        $('.update-payout-btn').click(function() {
            $('#modal_payout_id').val($(this).data('id'));
            $('#modal_agent_name').text($(this).data('agent'));
            $('#modal_amount').text($(this).data('amount'));
            $('#updatePayoutModal').modal('show');
        });

        $('#status').change(function() {
            if ($(this).val() == 'Rejected') {
                $('#transaction_group').hide();
            } else {
                $('#transaction_group').show();
            }
        });
    });
</script>
@endpush
