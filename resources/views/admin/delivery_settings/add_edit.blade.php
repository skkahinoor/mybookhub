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
                            <h4 class="card-title">Delivery Configuration</h4>

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
                                @if (empty($delivery_setting->id)) action="{{ url('admin/add-edit-delivery-setting') }}" @else action="{{ url('admin/add-edit-delivery-setting/' . $delivery_setting->id) }}" @endif
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
                                <a href="{{ url('admin/delivery-settings') }}" class="btn btn-light">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.layout.footer')
    </div>
@endsection
