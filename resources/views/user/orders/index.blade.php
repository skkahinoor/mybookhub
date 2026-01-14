@include('user.layout.header')

<div class="container-fluid page-body-wrapper">
    @include('user.layout.sidebar')

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <!-- Statistics Cards -->
                            <div class="row mb-4">
                                <div class="col-md-3 grid-margin stretch-card">
                                    <div class="card"
                                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">Total Orders</h6>
                                                    <h3 class="mb-0">{{ $totalOrders }}</h3>
                                                </div>
                                                <div style="font-size: 40px; opacity: 0.5;">📦</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 grid-margin stretch-card">
                                    <div class="card"
                                        style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">Total Spent</h6>
                                                    <h3 class="mb-0">₹{{ number_format($totalSpent, 2) }}</h3>
                                                </div>
                                                <div style="font-size: 40px; opacity: 0.5;">💰</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 grid-margin stretch-card">
                                    <div class="card"
                                        style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">Pending</h6>
                                                    <h3 class="mb-0">{{ $pendingOrders }}</h3>
                                                </div>
                                                <div style="font-size: 40px; opacity: 0.5;">⏳</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 grid-margin stretch-card">
                                    <div class="card"
                                        style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">Delivered</h6>
                                                    <h3 class="mb-0">{{ $deliveredOrders }}</h3>
                                                </div>
                                                <div style="font-size: 40px; opacity: 0.5;">✅</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title mb-0">My Orders</h4>
                            </div>

                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table id="ordersTable" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Order ID</th>
                                            <th>Products</th>
                                            <th>Order Status</th>
                                            <th>Payment Method</th>
                                            <th>Grand Total</th>
                                            <th>Order Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- DataTables will populate this via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('user.layout.footer')

    </div>
    <!-- plugins:js -->
    <script src="{{ asset('user/vendors/js/vendor.bundle.base.js') }}"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="{{ asset('user/vendors/datatables.net/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('user/vendors/datatables.net-bs4/dataTables.bootstrap4.js') }}"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{ asset('user/js/off-canvas.js') }}"></script>
    <script src="{{ asset('user/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('user/js/template.js') }}"></script>
    <script src="{{ asset('user/js/settings.js') }}"></script>
    <script src="{{ asset('user/js/todolist.js') }}"></script>
    <!-- endinject -->

    <script>
        $(document).ready(function() {
            // Initialize DataTables with AJAX
            $('#ordersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('user.orders.index') }}",
                    type: "GET"
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    { data: 'order_id', name: 'id' },
                    { data: 'products', name: 'products', orderable: false, searchable: true },
                    { data: 'order_status', name: 'order_status' },
                    { data: 'payment_method', name: 'payment_method' },
                    { data: 'grand_total', name: 'grand_total' },
                    { data: 'order_date', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[6, 'desc']],
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>',
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    },
                    emptyTable: '<div class="text-center py-5"><div style="font-size: 64px; margin-bottom: 20px;">📦</div><h4>No Orders Yet</h4><p class="text-muted">You haven\'t placed any orders yet.</p><a href="{{ url("/") }}" class="btn btn-primary">Start Shopping</a></div>'
                },
                responsive: true,
                drawCallback: function() {
                    // Re-initialize tooltips or other plugins if needed
                }
            });
        });
    </script>
</body>
</html>
