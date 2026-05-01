@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Order Returns</h4>

                            <div class="table-responsive pt-3">
                                <table id="returns" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Sl.No</th>
                                            <th>Order ID</th>
                                            <th>Date</th>
                                            <th>Customer Name</th>
                                            <th>Ordered Products</th>
                                            <th>Return Reason</th>
                                            <th>Current Status</th>
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
        <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
                <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2022. All rights reserved.</span>
            </div>
        </footer>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#returns')) {
                $('#returns').DataTable().destroy();
            }

            $('#returns').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ url()->current() }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'order_id', name: 'order_id'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'customer_name', name: 'customer_name'},
                    {data: 'ordered_products', name: 'ordered_products', orderable: false, searchable: false},
                    {data: 'return_reason', name: 'return_reason'},
                    {data: 'current_status', name: 'current_status'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                "order": [[1, "desc"]],
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                "language": {
                    "search": "Search:",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                }
            });
        });
    </script>
@endpush
