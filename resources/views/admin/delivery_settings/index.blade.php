@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Delivery Settings</h4>

                            <a href="{{ url('admin/add-edit-delivery-setting') }}"
                                style="max-width: 200px; float: right; display: inline-block"
                                class="btn btn-block btn-primary"><i class="mdi mdi-plus"></i> Add Delivery Setting</a>

                            @if (Session::has('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success:</strong> {{ Session::get('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="table-responsive pt-3">
                                <table id="delivery_settings" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Min Order Amount (₹)</th>
                                            <th>Delivery Charge (₹)</th>
                                            <th>Free Delivery</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($delivery_settings as $key => $setting)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>₹{{ number_format($setting->min_order_amount, 2) }}</td>
                                                <td>₹{{ number_format($setting->delivery_charge, 2) }}</td>
                                                <td>
                                                    @if($setting->is_free_delivery)
                                                        <span class="badge badge-success">Yes</span>
                                                    @else
                                                        <span class="badge badge-danger">No</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($setting->status == 1)
                                                        <a class="updateDeliverySettingStatus" id="delivery-{{ $setting->id }}"
                                                            delivery_id="{{ $setting->id }}" href="javascript:void(0)">
                                                            <i style="font-size: 25px" class="mdi mdi-bookmark-check"
                                                                status="Active"></i>
                                                        </a>
                                                    @else
                                                        <a class="updateDeliverySettingStatus" id="delivery-{{ $setting->id }}"
                                                            delivery_id="{{ $setting->id }}" href="javascript:void(0)">
                                                            <i style="font-size: 25px" class="mdi mdi-bookmark-outline"
                                                                status="Inactive"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ url('admin/add-edit-delivery-setting/' . $setting->id) }}">
                                                        <i style="font-size: 25px" class="mdi mdi-pencil-box"></i>
                                                    </a>
                                                    <a href="javascript:void(0)" class="confirmDelete"
                                                        data-module="delivery-setting"
                                                        data-url="{{ url('admin/delete-delivery-setting/' . $setting->id) }}">
                                                        <i style="font-size: 25px" class="mdi mdi-file-excel-box"></i>
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
        @include('admin.layout.footer')
    </div>
@endsection
