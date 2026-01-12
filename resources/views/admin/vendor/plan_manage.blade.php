@extends('admin.layout.layout')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Vendor Plan Management</h4>

                    @if (session('success_message'))
                        <div class="alert alert-success">{{ session('success_message') }}</div>
                    @endif

                    @if (session('error_message'))
                        <div class="alert alert-danger">{{ session('error_message') }}</div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card border-{{ $vendor->plan === 'pro' ? 'primary' : 'secondary' }}">
                                <div class="card-body">
                                    <h5 class="card-title">Current Plan: <span class="badge badge-{{ $vendor->plan === 'pro' ? 'primary' : 'secondary' }}">{{ strtoupper($vendor->plan) }}</span></h5>
                                    
                                    @if($vendor->plan === 'pro')
                                        <p class="mb-2"><strong>Plan Started:</strong> 
                                            {{ $vendor->plan_started_at ? $vendor->plan_started_at->format('M d, Y') : 'N/A' }}
                                        </p>
                                        <p class="mb-2"><strong>Plan Expires:</strong> 
                                            {{ $vendor->plan_expires_at ? $vendor->plan_expires_at->format('M d, Y') : 'N/A' }}
                                        </p>
                                        @if($vendor->plan_expires_at && $vendor->plan_expires_at->isFuture())
                                            <p class="mb-2">
                                                <strong>Days Remaining:</strong> 
                                                <span class="badge badge-{{ $vendor->plan_expires_at->diffInDays(now()) <= 7 ? 'warning' : 'success' }}">
                                                    {{ $vendor->plan_expires_at->diffInDays(now()) }} days
                                                </span>
                                            </p>
                                        @endif
                                    @else
                                        <p class="mb-2"><strong>Monthly Upload Limit:</strong> {{ $freePlanBookLimit ?? 100 }} books</p>
                                        <p class="mb-2"><strong>Books Uploaded This Month:</strong> {{ $productsThisMonth }}/{{ $freePlanBookLimit ?? 100 }}</p>
                                        <div class="progress mb-2">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $freePlanBookLimit > 0 ? ($productsThisMonth / $freePlanBookLimit) * 100 : 0 }}%">
                                                {{ $freePlanBookLimit > 0 ? round(($productsThisMonth / $freePlanBookLimit) * 100) : 0 }}%
                                            </div>
                                        </div>
                                        <p class="text-muted small">Coupons: Not Available</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Plan Features</h5>
                                    
                                    @if($vendor->plan === 'free')
                                        <ul class="list-unstyled">
                                            <li><i class="mdi mdi-check text-success"></i> Up to {{ $freePlanBookLimit ?? 100 }} books per month</li>
                                            <li><i class="mdi mdi-close text-danger"></i> No coupon creation</li>
                                            <li><i class="mdi mdi-check text-success"></i> Basic vendor dashboard</li>
                                        </ul>
                                        <hr>
                                        <h6>Upgrade to Pro Plan</h6>
                                        <ul class="list-unstyled">
                                            <li><i class="mdi mdi-check text-primary"></i> Unlimited book uploads</li>
                                            <li><i class="mdi mdi-check text-primary"></i> Unlimited coupons</li>
                                            <li><i class="mdi mdi-check text-primary"></i> Priority support</li>
                                        </ul>
                                        <form method="POST" action="{{ route('vendor.plan.upgrade') }}" class="mt-3">
                                            @csrf
                                            <button type="submit" class="btn btn-primary">Upgrade to Pro - ₹{{ number_format( $proPlanPrice, 2) }}/month</button>
                                        </form>
                                    @else
                                        <ul class="list-unstyled">
                                            <li><i class="mdi mdi-check text-success"></i> Unlimited book uploads</li>
                                            <li><i class="mdi mdi-check text-success"></i> Unlimited coupons</li>
                                            <li><i class="mdi mdi-check text-success"></i> Priority support</li>
                                        </ul>
                                        <hr>
                                        @if($vendor->plan_expires_at && $vendor->plan_expires_at->isFuture())
                                            <form method="POST" action="{{ route('vendor.plan.renew') }}" class="mt-3">
                                                @csrf
                                                <button type="submit" class="btn btn-primary">Renew Subscription</button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('vendor.plan.downgrade') }}" class="mt-2" onsubmit="return confirm('Are you sure you want to downgrade to Free plan? You will lose access to unlimited uploads and coupons.');">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-secondary">Downgrade to Free</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

