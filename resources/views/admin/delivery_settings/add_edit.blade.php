@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h4 class="card-title">{{ $title }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Shipping Charge Details</h4>

                            @if (Session::has('error_message'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Error:</strong> {{ Session::get('error_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <form class="forms-sample"
                                @if (empty($delivery_setting->id)) action="{{ url('admin/add-edit-shipping-charge') }}" @else action="{{ url('admin/add-edit-shipping-charge/' . $delivery_setting->id) }}" @endif
                                method="post"> @csrf
                                
                                <div class="form-group">
                                    <label for="min_order_amount">Minimum Order Amount (₹)</label>
                                    <input type="number" step="0.01" class="form-control" id="min_order_amount"
                                        placeholder="Enter Minimum Order Amount" name="min_order_amount"
                                        @if (!empty($delivery_setting->min_order_amount)) value="{{ $delivery_setting->min_order_amount }}" @else value="{{ old('min_order_amount') }}" @endif required>
                                </div>

                                <div class="form-group">
                                    <label for="delivery_charge">Delivery Charge (₹)</label>
                                    <input type="number" step="0.01" class="form-control" id="delivery_charge"
                                        placeholder="Enter Delivery Charge" name="delivery_charge"
                                        @if (!empty($delivery_setting->delivery_charge)) value="{{ $delivery_setting->delivery_charge }}" @else value="{{ old('delivery_charge') }}" @endif required>
                                </div>

                                <div class="form-group">
                                    <label for="agent_rate_per_km">Delivery Agent Rate (per KM) (₹)</label>
                                    <input type="number" step="0.01" class="form-control" id="agent_rate_per_km"
                                        placeholder="Enter Delivery Agent Rate per KM" name="agent_rate_per_km"
                                        @if (!empty($delivery_setting->agent_rate_per_km)) value="{{ $delivery_setting->agent_rate_per_km }}" @else value="{{ old('agent_rate_per_km') }}" @endif required>
                                </div>

                                <div class="form-group">
                                    <label for="is_free_delivery">Is Free Delivery?</label>
                                    <input type="checkbox" id="is_free_delivery" name="is_free_delivery"
                                        @if ($delivery_setting->is_free_delivery == 1) checked @endif>
                                </div>

                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <input type="checkbox" id="status" name="status"
                                        @if ($delivery_setting->status == 1) checked @endif>
                                </div>

                                <button type="submit" class="btn btn-primary mr-2">Submit</button>
                                <a href="{{ url('admin/shipping-charges') }}" class="btn btn-light">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
                
                {{-- Explanation and Guide Card on the Right Side --}}
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                        <div class="card-body">
                            <h4 class="card-title" style="color: #25396f; font-weight: 800;">
                                <i class="fas fa-info-circle text-primary mr-2"></i> Field Explanations
                            </h4>
                            <p class="text-muted" style="font-weight: 600; font-size: 0.9rem; border-bottom: 1px solid #f1f3f7; padding-bottom: 12px; margin-bottom: 15px;">
                                Use these settings to control shipping costs and delivery agent payouts.
                            </p>
                            
                            <ul class="list-unstyled" style="font-family: 'Nunito', sans-serif;">
                                <li class="mb-4">
                                    <h6 style="color: #435ebe; font-weight: 800; font-size: 0.95rem; margin-bottom: 6px;">
                                        <i class="fas fa-shopping-basket text-success mr-2"></i> Minimum Order Amount (₹)
                                    </h6>
                                    <p class="text-muted mb-0" style="font-size: 0.85rem; font-weight: 600; padding-left: 24px; line-height: 1.4;">
                                        The minimum order value required for a customer to qualify for free delivery (e.g., ₹499.00). Only applies if the <strong>Is Free Delivery?</strong> checkbox is enabled.
                                    </p>
                                </li>
                                <li class="mb-4">
                                    <h6 style="color: #435ebe; font-weight: 800; font-size: 0.95rem; margin-bottom: 6px;">
                                        <i class="fas fa-coins text-warning mr-2"></i> Delivery Charge (₹)
                                    </h6>
                                    <p class="text-muted mb-0" style="font-size: 0.85rem; font-weight: 600; padding-left: 24px; line-height: 1.4;">
                                        The default shipping fee charged to customers if their total order amount falls below the minimum order threshold (e.g., ₹30.00).
                                    </p>
                                </li>
                                <li class="mb-4">
                                    <h6 style="color: #435ebe; font-weight: 800; font-size: 0.95rem; margin-bottom: 6px;">
                                        <i class="fas fa-route text-info mr-2"></i> Delivery Agent Rate (per KM) (₹)
                                    </h6>
                                    <p class="text-muted mb-0" style="font-size: 0.85rem; font-weight: 600; padding-left: 24px; line-height: 1.4;">
                                        The mileage rate paid to delivery agents per kilometer traveled for order delivery (e.g., ₹10.00). Useful for automatic agent earnings calculation.
                                    </p>
                                </li>
                                <li class="mb-4">
                                    <h6 style="color: #435ebe; font-weight: 800; font-size: 0.95rem; margin-bottom: 6px;">
                                        <i class="fas fa-toggle-on text-primary mr-2"></i> Is Free Delivery? & Status
                                    </h6>
                                    <p class="text-muted mb-0" style="font-size: 0.85rem; font-weight: 600; padding-left: 24px; line-height: 1.4;">
                                        <strong>Is Free Delivery:</strong> If checked, qualifying orders above the threshold get free shipping. If unchecked, standard charges apply to all orders.<br>
                                        <strong>Status:</strong> Enables/Disables this entire shipping charge setting on the website.
                                    </p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.layout.footer')
    </div>
@endsection
