<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success - BookHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow text-center">
                    <div class="card-body py-5">
                        <div class="mb-4">
                            <svg width="80" height="80" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-success">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                                <path d="M8 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <h3 class="text-success mb-3">Payment Successful!</h3>
                        <p class="text-muted mb-4">Your Pro plan has been activated successfully.</p>
                        <p class="mb-4">You can now enjoy unlimited book uploads and coupons.</p>
                        <a href="{{ route('admin.login') }}" class="btn btn-primary">Go to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

