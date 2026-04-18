@extends('admin.layout.layout')
@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Books</h4>

                            @php
                                $prefix = $adminType === 'vendor' ? 'vendor' : 'admin';
                            @endphp
                            @if ($adminType == 'vendor')
                                <a href="{{ url('vendor/add-edit-product') }}"
                                    style="max-width: 150px; float: right; display: inline-block"
                                    class="btn btn-block btn-primary"><i class="mdi mdi-plus"></i> Add Book</a>
                            @else
                                <div style="display: flex;    justify-content: flex-end; gap: 10px;">
                                    <a href="{{ url('admin/add-edit-product') }}" class="btn btn-primary">
                                        <i class="mdi mdi-plus"></i> Add Book
                                    </a>

                                    <a href="{{ url('admin/import-product') }}" class="btn btn-success">
                                        <i class="mdi mdi-plus"></i> Import Book
                                    </a>

                                    <a href="{{ url('admin/import-images') }}" class="btn btn-warning">
                                        <i class="mdi mdi-camera"></i> Import Images
                                    </a>
                                </div>
                            @endif

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
                                <table id="products" class="table table-bordered"> {{-- using the id here for the DataTable --}}
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>ISBN</th>
                                            <th>Image</th>
                                            <th>Book Name</th>
                                            <th>Price</th>
                                            <th>Condition</th>
                                            <th>Education Level</th>
                                            <th>Category</th>
                                            <th>Edition</th>
                                            <th>Publisher</th>
                                            <th>Language</th>
                                            <th>Stock</th>
                                            @if ($adminType !== 'vendor')
                                                <th>Seller</th>
                                            @endif
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>  </div>
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



    <!-- Modal -->
    <div class="modal fade" id="addAttributeModal" tabindex="-1" aria-labelledby="addAttributeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <form id="addAttributeForm">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold" id="addAttributeModalLabel">
                            <i class="fas fa-plus-circle me-2"></i>Add Book Attribute
                        </h5>

                    </div>

                    <div class="modal-body p-4">
                        <!-- Book name display (read-only) -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted">
                                <i class="fas fa-book me-2"></i>Book Name
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-light" id="bookNameEdition" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Stock input -->
                            <div class="col-md-6 mb-4">
                                <label for="bookStock" class="form-label fw-semibold">
                                    <i class="fas fa-boxes me-2 text-warning"></i>Stock <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="bookStock" placeholder="0" required
                                        min="0" step="1">
                                </div>
                                <div class="form-text">Available quantity: <span id="availableQuantity"
                                        class="fw-bold text-primary">0</span></div>
                            </div>

                            <!-- Discount input -->
                            <div class="col-md-6 mb-4">
                                <label for="bookDiscount" class="form-label fw-semibold">
                                    <i class="fas fa-percent me-2 text-info"></i>Discount (%) <span
                                        class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="bookDiscount" placeholder="0"
                                        required min="0" max="100" step="0.01">
                                </div>
                                <div class="form-text">Enter discount percentage (0-100)</div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-0 p-4">
                        <button type="submit" class="btn btn-primary  px-4 fw-semibold">
                            <i class="fas fa-save me-2"></i> Update
                        </button>
                        <button type="button" class="btn btn-outline-secondary  px-4" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Server-side DataTables
            var table = $('#products').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ url()->current() }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'isbn_condition', name: 'isbn_condition' },
                    { data: 'image', name: 'image', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'price', name: 'price' },
                    { data: 'condition_badge', name: 'condition_badge' },
                    { data: 'section', name: 'section' },
                    { data: 'category', name: 'category' },
                    { data: 'edition', name: 'edition' },
                    { data: 'publisher', name: 'publisher' },
                    { data: 'language', name: 'language' },
                    { data: 'stock', name: 'stock' },
                    @if ($adminType !== 'vendor')
                        { data: 'seller', name: 'seller' },
                    @endif
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[1, 'desc']], // Default sort by ISBN or something relevant
                pageLength: 10
            });

            // Open modal and fill book name
            $(document).on('click', '#openAddAttributeModal', function() {
                var productId = $(this).data('id');
                var productName = $(this).data('name');
                var stock = parseInt($(this).data('stock')) || 0; // current available stock
                var discount = $(this).data('discount') || 0;

                $('#bookNameEdition').val(productName);

                // Store productId in modal for later use
                $('#addAttributeForm').data('product-id', productId);
                // Store current available stock separately for calculations
                $('#addAttributeForm').data('current-stock', stock);

                // Populate form fields with existing values
                $('#bookStock').val('0'); // start from 0 to add more stock
                $('#bookDiscount').val(discount);

                // Display available quantity (current stock)
                $('#availableQuantity').text(stock);
            });

            // Update available quantity in real-time when stock input changes
            $(document).on('input', '#bookStock', function() {
                var newStockValue = parseInt($(this).val()) || 0;
                var currentStock = parseInt($('#addAttributeForm').data('current-stock')) || 0;
                $('#availableQuantity').text(currentStock + newStockValue);
            });

            // Handle form submit
            $('#addAttributeForm').on('submit', function(e) {
                e.preventDefault();
                var productId = $(this).data('product-id');
                var stock = $('#bookStock').val();
                var discount = $('#bookDiscount').val();

                $.ajax({
                    url: 'book-attribute',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: productId,
                        stock: stock,
                        product_discount: discount
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            $('#addAttributeModal').modal('hide');
                            table.ajax.reload(null, false); // reload table without resetting pagination
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            alert('Error: ' + xhr.responseJSON.message);
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = '';
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                errors += value[0] + '\n';
                            });
                            alert('Validation errors:\n' + errors);
                        } else {
                            alert('An error occurred. Please try again.');
                        }
                    }
                });
            });
        });
    </script>
@endsection
