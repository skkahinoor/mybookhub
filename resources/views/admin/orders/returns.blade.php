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
                                        @foreach ($returnItems as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>#{{ $item['order_id'] }}</td>
                                                <td>{{ date('Y-m-d', strtotime($item['created_at'])) }}</td>
                                                <td>{{ $item['order']['name'] ?? 'N/A' }}</td>
                                                <td>
                                                    {{ $item['product_name'] }} ({{ $item['product_qty'] }})
                                                </td>
                                                <td>
                                                    <b class="text-danger">{{ $item['return_reason'] }}</b>
                                                    <br>
                                                    <small>{{ $item['return_comments'] }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge badge-warning">{{ $item['return_status'] }}</span>
                                                    <br>
                                                    Item Status: {{ $item['item_status'] }}
                                                </td>
                                                <td>
                                                    @if ($adminType == 'vendor')
                                                        <a title="View Order Details"
                                                            href="{{ url('vendor/orders/' . $item['order_id']) }}">
                                                            <i style="font-size: 25px" class="mdi mdi-file-document"></i>
                                                        </a>
                                                    @else
                                                        <a title="View Order Details"
                                                            href="{{ url('admin/orders/' . $item['order_id']) }}">
                                                            <i style="font-size: 25px" class="mdi mdi-file-document"></i>
                                                        </a>
                                                    @endif
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
                "order": [[1, "desc"]],
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                "language": {
                    "search": "Search:",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                },
                "columnDefs": [
                    { "orderable": false, "targets": [4, 7] }
                ]
            });
        });
    </script>
@endpush
