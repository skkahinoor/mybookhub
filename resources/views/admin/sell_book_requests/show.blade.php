@extends('admin.layout.layout')
<style>
    .close-btn {
    background: transparent;
    border: none;
    font-size: 25px;
    color: #fff;
}
</style>
@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Book Listing Details</h4>
                            <a href="{{ route('admin.sell-book-requests.index') }}" class="btn btn-light mb-3">Back to List</a>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="mb-4">Book Information</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="30%">Name:</th>
                                            <td>{{ $requestData->product->product_name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>ISBN:</th>
                                            <td>{{ $requestData->product->product_isbn ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Author(s):</th>
                                            <td>
                                                @if($requestData->product && $requestData->product->authors)
                                                    {{ $requestData->product->authors->pluck('name')->implode(', ') }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Base Price:</th>
                                            <td>₹{{ $requestData->product->product_price ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Selling Price:</th>
                                            <td class="text-primary font-weight-bold">
                                                @php
                                                    $finalPrice = $requestData->price;
                                                    if (!$finalPrice && $requestData->product && $requestData->product->product_price > 0) {
                                                        if ($requestData->condition) {
                                                            $finalPrice = ($requestData->product->product_price * $requestData->condition->percentage) / 100;
                                                        } else {
                                                            $finalPrice = $requestData->product->product_price;
                                                        }
                                                    }
                                                @endphp
                                                ₹{{ $finalPrice ?? 'N/A' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Condition:</th>
                                            <td>
                                                @if($requestData->condition)
                                                    <span class="badge badge-info">{{ $requestData->condition->name }}</span>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="mb-4">Seller Information</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="30%">Type:</th>
                                            <td>
                                                @if($requestData->admin_type === 'vendor')
                                                    <span class="badge badge-warning">Vendor</span>
                                                @else
                                                    <span class="badge badge-info">User (Student)</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Name:</th>
                                            <td>
                                                @if($requestData->admin_type === 'vendor' && $requestData->vendor && $requestData->vendor->user)
                                                    {{ $requestData->vendor->user->name ?? 'Vendor #'.$requestData->vendor_id }}
                                                @else
                                                    {{ $requestData->user->name ?? 'N/A' }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Email:</th>
                                            <td>
                                                @if($requestData->admin_type === 'vendor' && $requestData->vendor && $requestData->vendor->user)
                                                    {{ $requestData->vendor->user->email ?? 'N/A' }}
                                                @else
                                                    {{ $requestData->user->email ?? 'N/A' }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Phone:</th>
                                            <td>
                                                @if($requestData->admin_type === 'vendor' && $requestData->vendor && $requestData->vendor->user)
                                                    {{ $requestData->vendor->user->mobile ?? 'N/A' }}
                                                @else
                                                    {{ $requestData->user->mobile ?? 'N/A' }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Requested On:</th>
                                            <td>{{ $requestData->created_at ? $requestData->created_at->format('d M Y, h:i A') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status:</th>
                                            <td>
                                                @if($requestData->admin_approved == 1)
                                                    <span class="badge badge-success">Approved</span>
                                                @else
                                                    <span class="badge badge-warning">Pending Approval</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>

                                    @if($requestData->user_location_name)
                                    <div class="mt-4" style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 15px;">
                                        <div class="text-uppercase font-weight-bold mb-2" style="font-size: 11px; color: #92400e; letter-spacing: 0.5px;">
                                            Capture Location
                                        </div>
                                        <div style="font-size: 14px; color: #1f2937; line-height: 1.5; margin-bottom: 8px;">
                                            {{ $requestData->user_location_name }}
                                        </div>
                                        <div class="d-flex align-items-center" style="font-size: 12px; color: #6b7280;">
                                            <i class="mdi mdi-target mr-1" style="font-size: 16px;"></i>
                                            <span class="mr-3">{{ $requestData->user_location }}</span>
                                            
                                            <a href="https://www.google.com/maps?q={{ $requestData->user_location }}" target="_blank" 
                                               class="text-primary font-weight-bold text-uppercase d-flex align-items-center" style="text-decoration: none; font-size: 11px;">
                                                <i class="mdi mdi-map-marker text-danger mr-1" style="font-size: 16px;"></i> View Map
                                            </a>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="mt-4">
                                        @if($requestData->admin_approved == 0)
                                            <form action="{{ route('admin.sell-book-requests.approve', $requestData->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-success mr-2">Approve Listing</button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.sell-book-requests.reject', $requestData->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Reject and delete this request?')">Reject Request</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
 
                            <hr class="my-4">
 
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h5 class="mb-4">Bank Details</h5>
                                    @if($requestData->admin_type === 'vendor')
                                        @if($requestData->vendor && $requestData->vendor->bank)
                                            <table class="table table-bordered">
                                                <tr><th>Account Holder</th><td>{{ $requestData->vendor->bank->account_holder_name ?? 'N/A' }}</td></tr>
                                                <tr><th>Bank Name</th><td>{{ $requestData->vendor->bank->bank_name ?? 'N/A' }}</td></tr>
                                                <tr><th>Account Number</th><td>{{ $requestData->vendor->bank->account_number ?? 'N/A' }}</td></tr>
                                                <tr><th>IFSC Code</th><td>{{ $requestData->vendor->bank->ifsc_code ?? 'N/A' }}</td></tr>
                                                <tr><th>UPI ID</th><td>{{ $requestData->vendor->bank->upi_id ?? 'N/A' }}</td></tr>
                                            </table>
                                        @else
                                            <div class="alert alert-warning">Vendor bank details not found.</div>
                                        @endif
                                    @else
                                        <table class="table table-bordered">
                                            <tr><th>Account Holder</th><td>{{ $requestData->user->account_holder_name ?? 'N/A' }}</td></tr>
                                            <tr><th>Bank Name</th><td>{{ $requestData->user->bank_name ?? 'N/A' }}</td></tr>
                                            <tr><th>Account Number</th><td>{{ $requestData->user->account_number ?? 'N/A' }}</td></tr>
                                            <tr><th>IFSC Code</th><td>{{ $requestData->user->ifsc_code ?? 'N/A' }}</td></tr>
                                            <tr><th>UPI ID</th><td>{{ $requestData->user->upi_id ?? 'N/A' }}</td></tr>
                                        </table>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <h5 class="mb-4">Payout Information</h5>
                                    @if(isset($payoutInfo) && $payoutInfo)
                                        <div class="card bg-light border">
                                            <div class="card-body">
                                                <p><strong>Sold in Order:</strong> #{{ $payoutInfo->order_id }}</p>
                                                <p><strong>Order Status:</strong> {{ $payoutInfo->order->order_status ?? 'N/A' }}</p>
                                                <p><strong>Item Status:</strong> <span class="badge badge-info">{{ $payoutInfo->item_status }}</span></p>
                                                <p><strong>Payout Status:</strong> 
                                                    @if($payoutInfo->vendor_payout_status == 'Released')
                                                        <span class="badge badge-success">Released</span>
                                                    @else
                                                        <span class="badge badge-warning">Pending</span>
                                                    @endif
                                                </p>
                                                
                                                @php
                                                    $isAdvancePaid = $payoutInfo->product_attribute->contact_details_paid == 1;
                                                    $netPayout = $isAdvancePaid ? $payoutInfo->product_price : ($payoutInfo->product_price - $payoutInfo->commission);
                                                @endphp
                                                
                                                <p><strong>Net Payout Amount:</strong> <strong class="text-primary">₹{{ number_format($netPayout, 2) }}</strong></p>
 
                                                @if($payoutInfo->vendor_payout_note)
                                                    <div class="alert alert-secondary p-2 mt-2">
                                                        <small><strong>Payout Note:</strong><br>{{ $payoutInfo->vendor_payout_note }}</small>
                                                    </div>
                                                @endif
 
                                                @if($payoutInfo->vendor_payout_status != 'Released')
                                                    <button type="button" class="btn btn-primary mt-3 release-payout-btn" 
                                                            data-id="{{ $payoutInfo->id }}" 
                                                            data-name="{{ $payoutInfo->product_name }}"
                                                            data-amount="{{ $netPayout }}">
                                                        Release Payment
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-info border">
                                            <i class="mdi mdi-clock-outline mr-1"></i>
                                            This book has not been sold yet. Payout information will appear here once an order is placed and delivered.
                                        </div>
                                    @endif
                                </div>
                            </div>
 
                            <hr class="my-4">

                                @if($requestData->user_old_book_image || $requestData->video_upload)
                                 <div class="row mt-4">
                                     @if($requestData->user_old_book_image)
                                         <div class="col-md-6 text-center">
                                             <h5>Book Image</h5>
                                             <img src="{{ getBookCoverUrl($requestData->user_old_book_image) }}" class="img-thumbnail" style="max-width: 300px;">
                                         </div>
                                     @endif
                                     
                                     @if($requestData->video_upload)
                                         <div class="col-md-6 text-center d-flex flex-column justify-content-center">
                                             <h5>Book Video</h5>
                                             <div class="mt-2">
                                                 <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#videoModal">
                                                     <i class="mdi mdi-play-circle"></i> Watch Video Glance
                                                 </button>
                                             </div>
                                         </div>

                                         <!-- Video Modal -->
                                         <div class="modal fade" id="videoModal" tabindex="-1" role="dialog" aria-labelledby="videoModalLabel" aria-hidden="true">
                                             <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                                 <div class="modal-content" style="background: #000; border: none; border-radius: 8px; overflow: hidden;">
                                                     <div class="modal-header" style="border: none; position: absolute; right: 10px; z-index: 1055;">
                                                         <button type="button" class="close-btn" data-dismiss="modal">
    <i class="fa fa-times"></i>
</button>
                                                     </div>
                                                     <div class="modal-body p-0">
                                                         <video width="100%" height="auto" controls style="display: block; max-height: 80vh;">
                                                             <source src="{{ asset('front/videos/product_videos/' . $requestData->video_upload) }}" type="video/{{ pathinfo($requestData->video_upload, PATHINFO_EXTENSION) }}">
                                                             Your browser does not support the video tag.
                                                         </video>
                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                     @endif
                                 </div>
                                @endif
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
                                <textarea class="form-control" name="payout_note" id="payout_note" rows="3" placeholder="Enter payout details..." required></textarea>
                                <small class="text-muted">This information will be visible to the seller.</small>
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
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#videoModal').on('shown.bs.modal', function () {
            var videoPlayer = $(this).find('video')[0];
            if (videoPlayer) {
                videoPlayer.play().catch(function(error) {
                    console.log("Video autoplay prevented by browser:", error);
                });
            }
        });
        
        $('#videoModal').on('hidden.bs.modal', function () {
            var videoPlayer = $(this).find('video')[0];
            if (videoPlayer) {
                videoPlayer.pause();
                videoPlayer.currentTime = 0;
            }
        });
 
        // Release Payout Modal Logic
        $('.release-payout-btn').on('click', function() {
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
