@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12 mb-4">
            <h3 class="fw-bold">Welcome {{ (Auth::guard('sales')->user()->name ?? ($user->name ?? 'Sales Executive')) }}</h3>
            <h6 class="text-muted mb-0">All systems are running smoothly!</h6>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card bg-light shadow-sm border-0">
                        <div class="card-body">
                            <p class="mb-2">Total Intitutions</p>
                            <p class="fs-3 mb-0">{{ number_format($sectionsCount ?? 0) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card bg-primary text-white shadow-sm border-0">
                        <div class="card-body">
                            <p class="mb-2">Total Students</p>
                            <p class="fs-3 mb-0">{{ number_format($categoriesCount ?? 0) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card bg-info text-white shadow-sm border-0">
                        <div class="card-body">
                            <p class="mb-2">Total Blocks</p>
                            <p class="fs-3 mb-0">{{ number_format($productsCount ?? 0) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card bg-danger text-white shadow-sm border-0">
                        <div class="card-body">
                            <p class="mb-2">Total Classes</p>
                            <p class="fs-3 mb-0">{{ number_format($vendorsCount ?? 0) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card bg-light shadow-sm border-0">
                        <div class="card-body">
                            <p class="mb-2">Total Orders</p>
                            <p class="fs-3 mb-0">{{ number_format($ordersCount ?? 0) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card bg-primary text-white shadow-sm border-0">
                        <div class="card-body">
                            <p class="mb-2">Total Coupons</p>
                            <p class="fs-3 mb-0">{{ number_format($couponsCount ?? 0) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card bg-info text-white shadow-sm border-0">
                        <div class="card-body">
                            <p class="mb-2">Total Users</p>
                            <p class="fs-3 mb-0">{{ number_format($usersCount ?? 0) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card bg-danger text-white shadow-sm border-0">
                        <div class="card-body">
                            <p class="mb-2">Total Subscribers</p>
                            <p class="fs-3 mb-0">{{ number_format($subscribersCount ?? 0) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
