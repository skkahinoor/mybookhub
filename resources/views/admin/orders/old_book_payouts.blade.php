@extends('admin.layout.layout')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Student Old Book Payouts</h4>
                        <p class="card-description">
                            Manage payments for old books sold by students.
                        </p>

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

                        <div class="table-responsive pt-3">
                            <table id="oldBookPayouts" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Book Details</th>
                                        <th>Seller (Student)</th>
                                        <th>Buyer Paid</th>
                                        <th>Commission</th>
                                        <th>Advance Paid?</th>
                                        <th>Net Payout</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payouts as $payout)
                                        @php
                                            $sellingPrice = $payout->product_price - $payout->commission;
                                            $isAdvancePaid = $payout->product_attribute->contact_details_paid == 1;
                                            // If advance paid, payout is full price (Price + Commission collected from buyer)
                                            // because admin already got one commission from advance payment.
                                            $netPayout = $isAdvancePaid ? $payout->product_price : $sellingPrice;
                                        @endphp
                                        <tr>
                                            <td>#{{ $payout->order_id }}</td>
                                            <td>
                                                <strong>{{ $payout->product_name }}</strong><br>
                                                <small>Qty: {{ $payout->product_qty }}</small>
                                            </td>
                                            <td>
                                                {{ $payout->product_attribute->user->name ?? 'N/A' }}<br>
                                                <small>{{ $payout->product_attribute->user->email ?? '' }}</small>
                                            </td>
                                            <td>₹{{ number_format($payout->product_price, 2) }}</td>
                                            <td>₹{{ number_format($payout->commission, 2) }}</td>
                                            <td>
                                                @if($isAdvancePaid)
                                                    <span class="badge badge-success">Yes (₹{{ number_format($payout->product_attribute->platform_charge, 2) }})</span>
                                                @else
                                                    <span class="badge badge-secondary">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong class="text-primary">₹{{ number_format($netPayout, 2) }}</strong>
                                            </td>
                                            <td>
                                                @php
                                                    $statusBadge = 'badge-secondary';
                                                    if($payout->item_status == 'Delivered') $statusBadge = 'badge-success';
                                                    if($payout->item_status == 'Cancelled') $statusBadge = 'badge-danger';
                                                    if($payout->item_status == 'Shipped') $statusBadge = 'badge-info';
                                                @endphp
                                                <span class="badge {{ $statusBadge }}">{{ $payout->item_status }}</span>
                                                <br>
                                                <small class="text-muted">Order: {{ $payout->order->order_status ?? '' }}</small>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <button type="button" class="btn btn-sm btn-info mr-2 view-bank-details" 
                                                            data-user-id="{{ $payout->product_attribute->user_id }}" 
                                                            title="View Bank Details">
                                                        <i class="mdi mdi-bank"></i>
                                                    </button>

                                                    @if($payout->vendor_payout_status == 'Released')
                                                        <span class="text-success small">Released: <br>{{ $payout->vendor_payout_note }}</span>
                                                    @elseif($payout->item_status == 'Delivered')
                                                        <button type="button" class="btn btn-sm btn-primary release-payout-btn" 
                                                                data-id="{{ $payout->id }}" 
                                                                data-name="{{ $payout->product_name }}"
                                                                data-amount="{{ $netPayout }}"
                                                                title="Release Payment">
                                                            Release
                                                        </button>
                                                    @else
                                                        <span class="text-muted small">Wait for Delivery</span>
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
                <h5 class="modal-title">Student Bank Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="bankDetailsContent">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Release Payout Modal -->
<div class="modal fade" id="releasePayoutModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.release_old_book_payout') }}" method="POST">
                @csrf
                <input type="hidden" name="order_item_id" id="payout_item_id">
                <div class="modal-header">
                    <h5 class="modal-title">Release Payout</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>You are releasing payment for <strong><span id="payout_product_name"></span></strong>.</p>
                    <p>Amount to be paid: <strong class="text-primary">₹<span id="payout_amount"></span></strong></p>
                    
                    <div class="form-group">
                        <label for="payout_note">Payout Note (Transaction ID, etc.)</label>
                        <textarea class="form-control" name="payout_note" id="payout_note" rows="3" placeholder="Enter payout details..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Confirm Payout</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#oldBookPayouts').DataTable({
            "order": [[0, "desc"]]
        });

        // View Bank Details
        $('.view-bank-details').click(function() {
            var userId = $(this).data('user-id');
            $('#bankDetailsModal').modal('show');
            $('#bankDetailsContent').html('<div class="text-center"><div class="spinner-border text-primary" role="status"></div></div>');

            $.ajax({
                url: "{{ url('admin/get-user-bank-details') }}",
                type: "GET",
                data: { user_id: userId },
                success: function(response) {
                    if (response.status == 'success') {
                        var html = '<table class="table table-striped">';
                        html += '<tr><th>Account Holder</th><td>' + (response.data.account_holder_name || 'N/A') + '</td></tr>';
                        html += '<tr><th>Bank Name</th><td>' + (response.data.bank_name || 'N/A') + '</td></tr>';
                        html += '<tr><th>Account Number</th><td>' + (response.data.account_number || 'N/A') + '</td></tr>';
                        html += '<tr><th>IFSC Code</th><td>' + (response.data.ifsc_code || 'N/A') + '</td></tr>';
                        html += '<tr><th>UPI ID</th><td>' + (response.data.upi_id || 'N/A') + '</td></tr>';
                        html += '</table>';
                        $('#bankDetailsContent').html(html);
                    } else {
                        $('#bankDetailsContent').html('<div class="alert alert-danger">Details not found.</div>');
                    }
                }
            });
        });

        // Release Payout Modal
        $('.release-payout-btn').click(function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var amount = $(this).data('amount');

            $('#payout_item_id').val(id);
            $('#payout_product_name').text(name);
            $('#payout_amount').text(amount);
            $('#releasePayoutModal').modal('show');
        });
    });
</script>
@endpush
