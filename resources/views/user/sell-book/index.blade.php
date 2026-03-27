@include('user.layout.header')

<div class="container-fluid page-body-wrapper">
    @include('user.layout.sidebar')

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title mb-0">My Books</h4>
                                <a href="{{ route('student.sell-book.create') }}" class="btn btn-primary">
                                    <i class="mdi mdi-plus"></i> Add New Book
                                </a>
                            </div>

                            @if (session('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if (session('error_message'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if($userProducts->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Image</th>
                                                <th>Book Name</th>
                                                <th>Condition</th>
                                                <th>Category</th>
                                                <th>Price</th>
                                                <th>Selling Price</th>
                                                <th width="150">Sell Faster</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($userProducts as $key => $attribute)
                                                @php $product = $attribute->product; @endphp
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>
                                                        @if(!empty($attribute->user_old_book_image))
                                                            <img src="{{ asset('front/images/product_images/small/'.$attribute->user_old_book_image) }}" alt="image" style="width:50px; height:50px; object-fit: cover; border-radius: 4px;">
                                                        @else
                                                            <img src="{{ asset('front/images/product_images/small/no-image.png') }}" alt="image" style="width:50px; height:50px; object-fit: cover; border-radius: 4px;">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $product->product_name ?? 'N/A' }}
                                                        <br><small class="text-muted">ISBN: {{ $product->product_isbn ?? 'N/A' }}</small>
                                                    </td>
                                                    <td>
                                                        {{ ucfirst($product->condition ?? 'Old') }}
                                                    </td>
                                                    <td>
                                                        {{ $product->category->category_name ?? 'N/A' }}
                                                    </td>
                                                    <td>
                                                        {{ (isset($product->product_price) && $product->product_price) ? '₹'.number_format($product->product_price, 2) : 'N/A' }}
                                                    </td>
                                                    <td>
                                                        @if($attribute->user_product_price)
                                                            ₹{{ number_format($attribute->user_product_price, 2) }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($attribute->admin_approved == 1 && $attribute->is_sold == 0)
                                                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                                <label class="btn btn-outline-primary btn-xs @if($attribute->show_contact == 1) active @endif">
                                                                    <input type="radio" class="toggle-sell-faster" name="sell_faster_{{$attribute->id}}" data-id="{{$attribute->id}}" value="1" @if($attribute->show_contact == 1) checked @endif> Yes
                                                                </label>
                                                                <label class="btn btn-outline-secondary btn-xs @if($attribute->show_contact == 0) active @endif">
                                                                    <input type="radio" class="toggle-sell-faster" name="sell_faster_{{$attribute->id}}" data-id="{{$attribute->id}}" value="0" @if($attribute->show_contact == 0) checked @endif> No
                                                                </label>
                                                            </div>
                                                            @if($attribute->show_contact == 1 && $attribute->contact_details_paid == 0)
                                                                <div class="mt-1">
                                                                    <small class="text-info d-block">Platform Charge: ₹{{ number_format($attribute->platform_charge, 2) }}</small>
                                                                    <button class="btn btn-success btn-xs pay-charge" data-id="{{$attribute->id}}">Pay Now</button>
                                                                </div>
                                                            @elseif($attribute->contact_details_paid == 1)
                                                                <small class="text-success d-block"><i class="mdi mdi-check-circle"></i> Paid & Promoted</small>
                                                            @endif
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($attribute->is_sold == 1)
                                                            <span class="badge badge-secondary">Sold Out</span>
                                                        @elseif($attribute->admin_approved == 1)
                                                            <span class="badge badge-success">Approved</span>
                                                        @else
                                                            <span class="badge badge-warning">Pending Review</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($attribute->admin_approved == 1 && $attribute->is_sold == 0)
                                                            @if($attribute->show_contact == 1 && $attribute->contact_details_paid == 1)
                                                                <button class="btn btn-info btn-xs mark-sold" data-id="{{$attribute->id}}">
                                                                    Mark as Sold
                                                                </button>
                                                            @else
                                                                <span class="text-muted" title="Manual mark as sold is for Sell Faster users only">Standard Sale</span>
                                                            @endif
                                                        @elseif($attribute->is_sold == 1)
                                                            <span class="badge badge-secondary p-1">Completed</span>
                                                        @else
                                                            <span class="text-muted">Wait for Approval</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info text-center">
                                    <h5>No books added yet!</h5>
                                    <p>Click "Add New Book" to sell your first book.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('user.layout.footer')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Toggle Sell Faster
        $('.toggle-sell-faster').change(function() {
            var attribute_id = $(this).data('id');
            var show_contact = $(this).val();
            
            $.ajax({
                url: "{{ route('student.sell-book.toggle-sell-faster') }}",
                type: 'POST',
                data: { attribute_id: attribute_id, show_contact: show_contact },
                success: function(response) {
                    if(response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                        });
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    var msg = 'Something went wrong. Please try again.';
                    if(xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire('Error', msg, 'error');
                }
            });
        });

        // Pay Charge via Razorpay
        $('.pay-charge').click(function() {
            var attribute_id = $(this).data('id');
            
            Swal.fire({
                title: 'Confirm Payment',
                text: "You will be redirected to Razorpay to pay the platform charge.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Proceed to Pay'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Step 1: Create Order
                    $.ajax({
                        url: "{{ route('student.sell-book.razorpay.create-order') }}",
                        type: 'POST',
                        data: { attribute_id: attribute_id },
                        success: function(response) {
                            if(response.status) {
                                // Step 2: Open Razorpay Checkout
                                var options = {
                                    "key": response.key,
                                    "amount": response.amount * 100,
                                    "currency": "INR",
                                    "name": "BookHub",
                                    "description": response.description,
                                    "order_id": response.order_id,
                                    "handler": function (rzp_response){
                                        // Step 3: Verify Payment
                                        $.ajax({
                                            url: "{{ route('student.sell-book.razorpay.verify-payment') }}",
                                            type: 'POST',
                                            data: {
                                                attribute_id: attribute_id,
                                                razorpay_payment_id: rzp_response.razorpay_payment_id,
                                                razorpay_order_id: rzp_response.razorpay_order_id,
                                                razorpay_signature: rzp_response.razorpay_signature
                                            },
                                            success: function(verify_response) {
                                                if(verify_response.status) {
                                                    Swal.fire('Success!', verify_response.message, 'success').then(() => {
                                                        location.reload();
                                                    });
                                                } else {
                                                    Swal.fire('Error!', verify_response.message, 'error');
                                                }
                                            }
                                        });
                                    },
                                    "prefill": {
                                        "name": response.name,
                                        "email": response.email,
                                        "contact": response.phone
                                    },
                                    "theme": {
                                        "color": "#3399cc"
                                    }
                                };
                                var rzp1 = new Razorpay(options);
                                rzp1.open();
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Failed to initiate payment.', 'error');
                        }
                    });
                }
            });
        });

        // Mark as Sold
        $('.mark-sold').click(function() {
            var attribute_id = $(this).data('id');
            
            Swal.fire({
                title: 'Mark as Sold?',
                text: "This will hide the book from public listing.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Mark as Sold'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('student.sell-book.mark-as-sold') }}",
                        type: 'POST',
                        data: { attribute_id: attribute_id },
                        success: function(response) {
                            if(response.status) {
                                Swal.fire('Updated!', response.message, 'success').then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Request failed.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
