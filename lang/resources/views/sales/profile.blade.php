@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="card shadow-lg border-0">
                <div class="row g-0">
                    <!-- Navigation Sidebar -->
                    <div class="col-lg-3 border-end">
                        <nav class="p-4">
                            <ul class="nav flex-column nav-pills" id="sales-account-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a href="#dashboard" class="nav-link active" id="dashboard-tab"
                                       data-bs-toggle="tab" role="tab" aria-controls="dashboard"
                                       aria-selected="true">Dashboard</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a href="#edit-account" class="nav-link" id="edit-account-tab"
                                       data-bs-toggle="tab" role="tab" aria-controls="edit-account"
                                       aria-selected="false">Account Details</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a href="#change-password" class="nav-link" id="change-password-tab"
                                       data-bs-toggle="tab" role="tab" aria-controls="change-password"
                                       aria-selected="false">Change Password</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a href="{{ route('sales.logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('sales-logout-form').submit();"
                                       class="nav-link">Logout</a>
                                    <form id="sales-logout-form" action="{{ route('sales.logout') }}" method="POST" style="display:none;">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </nav>
                    </div>

                    <!-- Content Area -->
                    <div class="col-lg-9">
                        <div class="p-4 p-md-5">
                            <div class="tab-content" id="sales-account-tabs-content">
                                <!-- Dashboard Tab -->
                                <div class="tab-pane fade show active" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
                                    <div class="mb-4 text-center">
                                        <h2 class="text-primary fw-bold mb-1">My Profile</h2>
                                        <p class="text-muted mb-0 fs-6">Welcome back, <strong>{{ $sales->name }}</strong>.</p>
                                    </div>

                                    @if(session('success_message'))
                                        <div class="alert alert-success text-center py-2 mb-4">{{ session('success_message') }}</div>
                                    @endif

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="p-3 border rounded bg-light h-100">
                                                <div class="small text-muted">Email</div>
                                                <div class="fw-semibold">{{ $sales->email }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="p-3 border rounded bg-light h-100">
                                                <div class="small text-muted">Phone</div>
                                                <div class="fw-semibold">{{ $sales->phone ?: 'Not provided' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="p-3 border rounded bg-light h-100">
                                                <div class="small text-muted">Address</div>
                                                <div class="fw-semibold">{{ $sales->address ?: 'Not provided' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Account Details Tab -->
                                <div class="tab-pane fade" id="edit-account" role="tabpanel" aria-labelledby="edit-account-tab">
                                    @if ($errors->any())
                                        <div class="alert alert-danger py-2 mb-4">
                                            <ul class="mb-0 ps-3">
                                                @foreach ($errors->all() as $error)
                                                    <li class="small">{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <form class="" method="POST" action="{{ route('sales.profile.update') }}">
                                        @csrf
                                        <div class="row g-3 mb-2">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Name</label>
                                                <input type="text" name="name" class="form-control" value="{{ old('name', $sales->name) }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Email</label>
                                                <input type="email" name="email" class="form-control" value="{{ old('email', $sales->email) }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Phone</label>
                                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $sales->phone) }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">UPI ID</label>
                                                <input type="text" name="upi_id" class="form-control" value="{{ old('upi_id', $sales->upi_id) }}">
                                            </div>
                                        </div>

                                        <div class="row g-3 mb-2">
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Address</label>
                                                <input type="text" name="address" class="form-control" value="{{ old('address', $sales->address) }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">City</label>
                                                <input type="text" name="city" class="form-control" value="{{ old('city', $sales->city) }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">District</label>
                                                <input type="text" name="district" class="form-control" value="{{ old('district', $sales->district) }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">State</label>
                                                <input type="text" name="state" class="form-control" value="{{ old('state', $sales->state) }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Pincode</label>
                                                <input type="text" name="pincode" class="form-control" value="{{ old('pincode', $sales->pincode) }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Country</label>
                                                <input type="text" name="country" class="form-control" value="{{ old('country', $sales->country) }}">
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <hr>
                                            <h5 class="text-primary mb-3 fw-bold">Bank Details</h5>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Bank Name</label>
                                                    <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $sales->bank_name) }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Branch</label>
                                                    <input type="text" name="bank_branch" class="form-control" value="{{ old('bank_branch', $sales->bank_branch) }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Account Number</label>
                                                    <input type="text" name="account_number" class="form-control" value="{{ old('account_number', $sales->account_number) }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">IFSC Code</label>
                                                    <input type="text" name="ifsc_code" class="form-control" value="{{ old('ifsc_code', $sales->ifsc_code) }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4 text-end">
                                            <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Change Password Tab -->
                                <div class="tab-pane fade" id="change-password" role="tabpanel" aria-labelledby="change-password-tab">
                                    <form class="" method="POST" action="{{ route('sales.profile.update') }}" style="max-width: 520px;">
                                        @csrf
                                        <!-- Preserve required fields for validation -->
                                        <input type="hidden" name="name" value="{{ $sales->name }}">
                                        <input type="hidden" name="email" value="{{ $sales->email }}">

                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">New Password</label>
                                                <input type="password" name="password" class="form-control" autocomplete="new-password" required>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Confirm Password</label>
                                                <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password" required>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <button type="submit" class="btn btn-primary">Update Password</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            $('#sales-account-tabs .nav-link').removeClass('active');
            $(e.target).addClass('active');
        });
    });
    </script>
@endsection
