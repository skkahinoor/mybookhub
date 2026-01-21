{{-- This file is rendered by shippingCharges() method in Admin/ShippingController.php --}}
@extends('admin.layout.layout')


@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Shipping Charges</h4>



                            {{-- Displaying The Validation Errors: https://laravel.com/docs/9.x/validation#quick-displaying-the-validation-errors AND https://laravel.com/docs/9.x/blade#validation-errors --}}
                            {{-- Determining If An Item Exists In The Session (using has() method): https://laravel.com/docs/9.x/session#determining-if-an-item-exists-in-the-session --}}
                            {{-- Our Bootstrap success message in case of updating admin password is successful: --}}
                            @if (Session::has('success_message'))
                                <!-- Check AdminController.php, updateAdminPassword() method -->
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success:</strong> {{ Session::get('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif


                            <div class="table-responsive pt-3">
                                {{-- DataTable --}}
                                <table id="shipping" class="table table-bordered"> {{-- using the id here for the DataTable --}}
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Country</th>
                                            <th>Rate (0g to 500g)</th>
                                            <th>Rate (501g to 1000g)</th>
                                            <th>Rate (1001g to 2000g)</th>
                                            <th>Rate (2001g to 5000g)</th>
                                            <th>Rate (Above 5000g)</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($shippingCharges as $shipping)
                                            <tr>
                                                <td>{{ $shipping['id'] }}</td>
                                                <td>{{ $shipping['country'] }}</td>
                                                <td>{{ $shipping['0_500g'] }}</td>
                                                <td>{{ $shipping['501g_1000g'] }}</td>
                                                <td>{{ $shipping['1001_2000g'] }}</td>
                                                <td>{{ $shipping['2001g_5000g'] }}</td>
                                                <td>{{ $shipping['above_5000g'] }}</td>
                                                <td>
                                                    @php
                                                        $statusValue = $shipping['status'] == 1 ? 'Active' : 'Inactive';
                                                        $iconClass   = $shipping['status'] == 1 ? 'mdi-bookmark-check' : 'mdi-bookmark-outline';
                                                        $updateUrl   = ($adminType === 'vendor')
                                                                        ? route('vendor.updateshippingstatus')
                                                                        : route('admin.updateshippingstatus');
                                                    @endphp

                                                    <a class="updateShippingStatus"
                                                        id="shipping-{{ $shipping['id'] }}"
                                                        shipping_id="{{ $shipping['id'] }}"
                                                        data-url="{{ $updateUrl }}"
                                                        status="{{ $statusValue }}"
                                                        href="javascript:void(0)">
                                                        <i style="font-size: 25px" class="mdi {{ $iconClass }}"></i>
                                                    </a>
                                                </td>

                                                <td>
                                                    <a href="{{ url('admin/edit-shipping-charges/' . $shipping['id']) }}">
                                                        <i style="font-size: 25px" class="mdi mdi-pencil-box"></i>
                                                        {{-- Icons from Skydash Admin Panel Template --}}
                                                    </a>

                                                    {{-- Confirm Deletion JS alert and Sweet Alert --}}
                                                    {{-- <a title="Shipping" class="confirmDelete" href="{{ url('admin/delete-shipping/' . $shipping['id']) }}"> --}}
                                                    {{-- <i style="font-size: 25px" class="mdi mdi-file-excel-box"></i> --}} {{-- Icons from Skydash Admin Panel Template --}}
                                                    {{-- </a> --}}
                                                    {{-- <a href="JavaScript:void(0)" class="confirmDelete" module="shipping" moduleid="{{ $shipping['id'] }}"> --}}
                                                    {{--  <i style="font-size: 25px" class="mdi mdi-file-excel-box"></i> --}} {{-- Icons from Skydash Admin Panel Template --}}
                                                    {{-- </a> --}}
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
        <!-- content-wrapper ends -->
        <!-- partial:../../partials/_footer.html -->
        <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
                <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2022. All rights
                    reserved.</span>
            </div>
            <script>
                $(document).on("click", ".updateShippingStatus", function () {

                    let status     = $(this).attr("status");
                    let shippingID = $(this).attr("shipping_id");
                    let updateUrl  = $(this).data("url");
                    let element    = $(this);

                    $.ajax({
                        url: updateUrl,
                        type: "POST",
                        data: {
                            shipping_id: shippingID,
                            status: status,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function (resp) {

                            if (resp.status == 1) {
                                // Active
                                element.attr("status", "Active");
                                element.find("i").removeClass("mdi-bookmark-outline")
                                                 .addClass("mdi-bookmark-check");
                            } else {
                                // Inactive
                                element.attr("status", "Inactive");
                                element.find("i").removeClass("mdi-bookmark-check")
                                                 .addClass("mdi-bookmark-outline");
                            }
                        },
                        error: function () {
                            alert("Something went wrong. Try again!");
                        }
                    });

                });
                </script>

        </footer>
        <!-- partial -->
    </div>

@endsection
