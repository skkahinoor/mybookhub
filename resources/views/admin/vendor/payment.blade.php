<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pro Plan Payment - BookHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Pro Plan Payment</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h5>Upgrade to Pro Plan</h5>
                            <p class="text-muted">Get unlimited book uploads and coupons</p>
                            <h3 class="text-primary">₹{{ number_format($amount , 2) }} <small class="text-muted">/month</small></h3>
                        </div>

                        <div id="payment-status"></div>

                        <form id="payment-form">
                            @csrf
                            <input type="hidden" name="vendor_id" value="{{ $vendor->id }}">
                            <input type="hidden" name="razorpay_order_id" value="{{ $order_id }}">
                            
                            <button type="button" class="btn btn-primary w-100 btn-lg" id="pay-button">
                                Pay ₹{{ number_format($amount, 2) }}
                            </button>
                        </form>

                        <div class="mt-3 text-center">
                            <a href="{{ route('admin.login') }}" class="text-muted">Cancel and go back</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('pay-button').addEventListener('click', function() {
            var options = {
                "key": "{{ $key_id }}",
                "amount": {{ $amount }},
                "currency": "{{ $currency }}",
                "name": "BookHub",
                "description": "Pro Plan Subscription - ₹{{ number_format($amount / 100, 2) }}/month",
                "order_id": "{{ $order_id }}",
                "handler": function (response) {
                    // Submit payment details to server
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ route('vendor.payment.verify') }}";
                    
                    var csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = "{{ csrf_token() }}";
                    form.appendChild(csrfInput);

                    var vendorIdInput = document.createElement('input');
                    vendorIdInput.type = 'hidden';
                    vendorIdInput.name = 'vendor_id';
                    vendorIdInput.value = "{{ $vendor->id }}";
                    form.appendChild(vendorIdInput);

                    var orderIdInput = document.createElement('input');
                    orderIdInput.type = 'hidden';
                    orderIdInput.name = 'razorpay_order_id';
                    orderIdInput.value = response.razorpay_order_id;
                    form.appendChild(orderIdInput);

                    var paymentIdInput = document.createElement('input');
                    paymentIdInput.type = 'hidden';
                    paymentIdInput.name = 'razorpay_payment_id';
                    paymentIdInput.value = response.razorpay_payment_id;
                    form.appendChild(paymentIdInput);

                    var signatureInput = document.createElement('input');
                    signatureInput.type = 'hidden';
                    signatureInput.name = 'razorpay_signature';
                    signatureInput.value = response.razorpay_signature;
                    form.appendChild(signatureInput);

                    document.body.appendChild(form);
                    form.submit();
                },
                "prefill": {
                    "name": "{{ $vendor->name }}",
                    "email": "{{ $vendor->email }}",
                    "contact": "{{ $vendor->mobile }}"
                },
                "theme": {
                    "color": "#1f3c88"
                },
                "modal": {
                    "ondismiss": function(){
                        console.log("Payment cancelled");
                    }
                }
            };

            var rzp = new Razorpay(options);
            rzp.open();
        });
    </script>
</body>
</html>

