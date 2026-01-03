@extends('layouts.app')

@section('title')
    Vendor Details
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="mb-1">{{ $vendor->name }}</h3>
                    <p class="text-muted mb-0">Vendor details and login information</p>
                </div>
                <a href="{{ route('sales.vendors.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Vendors
                </a>
            </div>

            <div class="row g-3">
                <div class="col-lg-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Vendor Profile</h5>
                            <div class="mb-2"><strong>Name:</strong> {{ $vendor->name }}</div>
                            <div class="mb-2"><strong>Email:</strong> {{ $vendor->email }}</div>
                            <div class="mb-2"><strong>Mobile:</strong> {{ $vendor->mobile }}</div>
                            <div class="mb-2">
                                <strong>Status:</strong>
                                @if($vendor->status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </div>
                            {{-- <div class="mb-2">
                                <strong>Confirmed:</strong>
                                <span class="badge {{ $vendor->confirm === 'Yes' ? 'bg-info text-dark' : 'bg-secondary' }}">
                                    {{ $vendor->confirm }}
                                </span>
                            </div> --}}
                            {{-- <div class="mb-2"><strong>Created:</strong> {{ $vendor->created_at?->format('d M Y, h:i A') }}</div>
                            <div class="mb-2"><strong>Updated:</strong> {{ $vendor->updated_at?->format('d M Y, h:i A') }}</div> --}}
                        </div>
                    </div>
                </div>

                {{-- <div class="col-lg-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Admin Account</h5>
                            @if($adminAccount)
                                <div class="mb-2"><strong>Login Email:</strong> {{ $adminAccount->email }}</div>
                                <div class="mb-2"><strong>Login Mobile:</strong> {{ $adminAccount->mobile }}</div>
                                <div class="mb-2"><strong>Account Status:</strong>
                                    @if($adminAccount->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </div>
                                <div class="mb-2"><strong>Default Password:</strong> 123456</div>
                                <p class="text-muted small mb-0">Ask the vendor to reset their password after first login.</p>
                            @else
                                <p class="mb-0">No linked admin account found for this vendor.</p>
                            @endif
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
</div>
@endsection

