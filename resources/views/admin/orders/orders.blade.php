{{-- This page is rendered by orders() method inside Admin/OrderController.php --}}
@extends('admin.layout.layout')


@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Orders</h4>

                            <div class="d-flex justify-content-between align-items-center pt-3 mb-3">
                                <div>
                                    @can('delete_orders')
                                        <button id="deleteSelectedOrders" class="btn btn-danger btn-sm" style="display: none;">
                                            <i class="mdi mdi-delete"></i> Delete Selected
                                        </button>
                                    @endcan
                                </div>
                            </div>

                            <div class="table-responsive pt-3">
                                {{-- DataTable --}}
                                <table id="orders" class="table table-bordered"> {{-- using the id here for the DataTable --}}
                                    <thead>
                                        <tr>
                                            <th>
                                                <div style="display: flex; justify-content: center;">
                                                    <input type="checkbox" id="selectAllOrders" style="transform: scale(1.3); cursor: pointer;">
                                                </div>
                                            </th>
                                            <th>Sl.No</th>
                                            <th>Order ID</th>
                                            <th>Order Date</th>
                                            <th>Customer Name</th>
                                            <th>Customer Email</th>
                                            <th>Ordered Products</th>
                                            <th>Order Amount</th>
                                            <th>Order Status</th>
                                            <th>Payment Method</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
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
        </footer>
        <!-- partial -->
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Destroy existing DataTable instance if it exists
            if ($.fn.DataTable.isDataTable('#orders')) {
                $('#orders').DataTable().destroy();
            }

            // Initialize DataTable
            $('#orders').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ url()->current() }}",
                columns: [
                    {data: 'checkbox', name: 'checkbox', orderable: false, searchable: false},
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'id', name: 'id'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'ordered_products', name: 'ordered_products', orderable: false, searchable: false},
                    {data: 'grand_total', name: 'grand_total'},
                    {data: 'order_status', name: 'order_status'},
                    {data: 'payment_method', name: 'payment_method'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                "order": [
                    [2, "desc"]
                ], // Sort by Order ID (column 2 now due to checkbox) descending - latest first
                "pageLength": 10, // Show 10 entries per page
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ], // Page length options
                "language": {
                    "search": "Search:",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "Showing 0 to 0 of 0 entries",
                    "infoFiltered": "(filtered from _MAX_ total entries)",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    }
                }
            });

            // Handle Select All Checkbox
            $('#selectAllOrders').on('click', function() {
                var isChecked = $(this).prop('checked');
                $('.select-order-checkbox').prop('checked', isChecked);
                toggleDeleteButton();
            });

            // Handle Individual Checkbox
            $(document).on('change', '.select-order-checkbox', function() {
                // Uncheck "Select All" if any individual checkbox is unchecked
                if (!$(this).prop('checked')) {
                    $('#selectAllOrders').prop('checked', false);
                }
                
                // Check "Select All" if all individual checkboxes are checked
                if ($('.select-order-checkbox:checked').length == $('.select-order-checkbox').length && $('.select-order-checkbox').length > 0) {
                    $('#selectAllOrders').prop('checked', true);
                }

                toggleDeleteButton();
            });

            // Handle table pagination/draw to uncheck "Select All"
            $('#orders').on('draw.dt', function() {
                $('#selectAllOrders').prop('checked', false);
                toggleDeleteButton();
            });

            function toggleDeleteButton() {
                var checkedCount = $('.select-order-checkbox:checked').length;
                if (checkedCount > 0) {
                    $('#deleteSelectedOrders').show();
                } else {
                    $('#deleteSelectedOrders').hide();
                }
            }

            // Bulk Delete Action
            $('#deleteSelectedOrders').on('click', function() {
                var selectedIds = [];
                $('.select-order-checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length > 0) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this! Selected orders will be deleted.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete them!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '{{ route('admin.orders.bulkDelete') }}',
                                type: 'POST',
                                data: {
                                    ids: selectedIds,
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    if (response.status) {
                                        Swal.fire(
                                            'Deleted!',
                                            response.message,
                                            'success'
                                        );
                                        $('#orders').DataTable().ajax.reload();
                                        $('#selectAllOrders').prop('checked', false);
                                        $('#deleteSelectedOrders').hide();
                                    } else {
                                        Swal.fire(
                                            'Error!',
                                            response.message,
                                            'error'
                                        );
                                    }
                                },
                                error: function(xhr) {
                                    Swal.fire(
                                        'Error!',
                                        'Something went wrong. Please try again.',
                                        'error'
                                    );
                                }
                            });
                        }
                    });
                }
            });
        });

    </script>
@endpush
