@extends('admin.layout.layout')
@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Coupons</h4>
                            <a href="{{ url('admin/add-edit-coupon') }}"
                                style="max-width: 150px; float: right; display: inline-block"
                                class="btn btn-block btn-primary"><i class="mdi mdi-plus"></i> Add Coupon</a>

                            @if (Session::has('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success:</strong> {{ Session::get('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="table-responsive pt-3">
                                {{-- DataTable --}}
                                <table id="coupons" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Coupon Code</th>
                                            <th>Coupon Type</th>
                                            <th>Amount</th>
                                            <th>Expiry Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($coupons as $coupon)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $coupon['coupon_code'] }}</td>
                                                <td>{{ $coupon['coupon_type'] }}</td>
                                                <td>
                                                    {{ $coupon['amount'] }}
                                                    @if ($coupon['amount_type'] == 'Percentage')
                                                        %
                                                    @else
                                                        INR
                                                    @endif
                                                </td>
                                                <td>{{ $coupon['expiry_date'] }}</td>
                                                <td>
                                                    @if ($adminType === 'vendor')
                                                        <a class="updateCouponStatus" id="coupon-{{ $coupon['id'] }}"
                                                            coupon_id="{{ $coupon['id'] }}"
                                                            data-url="{{ route('vendor.updatecouponstatus') }}"
                                                            href="javascript:void(0)">
                                                            <i style="font-size: 25px" class="mdi mdi-bookmark-check"
                                                                status="Active"></i>
                                                        </a>
                                                    @else
                                                        @if ($coupon['status'] == 1)
                                                            <a class="updateCouponStatus" id="coupon-{{ $coupon['id'] }}"
                                                                coupon_id="{{ $coupon['id'] }}"
                                                                data-url="{{ route('admin.updatecouponstatus') }}"
                                                                href="javascript:void(0)">
                                                                <i style="font-size: 25px" class="mdi mdi-bookmark-check"
                                                                    status="Active"></i>
                                                            </a>
                                                        @else
                                                            <a class="updateCouponStatus" id="coupon-{{ $coupon['id'] }}"
                                                                coupon_id="{{ $coupon['id'] }}"
                                                                data-url="{{ route('admin.updatecouponstatus') }}"
                                                                href="javascript:void(0)">
                                                                <i style="font-size: 25px" class="mdi mdi-bookmark-outline"
                                                                    status="Inactive"></i>
                                                            </a>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ url('admin/add-edit-coupon/' . $coupon['id']) }}">
                                                        <i style="font-size:25px" class="mdi mdi-pencil-box"></i>
                                                    </a>
                                                    <a href="{{ url('admin/delete-coupon/' . $coupon['id']) }}"
                                                        onclick="return confirm('Are you sure you want to delete this coupon?')"
                                                        title="Delete Coupon">
                                                        <i style="font-size:25px;color:red;"
                                                            class="mdi mdi-file-excel-box"></i>
                                                    </a>
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

        <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
                <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2022. All rights
                    reserved.</span>
            </div>
        </footer>
        <!-- partial -->
    </div>
@endsection
