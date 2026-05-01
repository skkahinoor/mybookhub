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

                                    <tbody>
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

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#shipping').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ url()->current() }}",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'country', name: 'country'},
                    {data: '0_500g', name: '0_500g'},
                    {data: '501g_1000g', name: '501g_1000g'},
                    {data: '1001_2000g', name: '1001_2000g'},
                    {data: '2001g_5000g', name: '2001g_5000g'},
                    {data: 'above_5000g', name: 'above_5000g'},
                    {data: 'status', name: 'status', orderable: false, searchable: false},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ]
            });
        });
    </script>
@endpush
