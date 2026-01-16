@extends('admin.layout.layout')
@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Books</h4>

                            @if ($adminType == 'vendor')
                            <a href="{{ url('vendor/add-edit-product') }}"
                                    style="max-width: 150px; float: right; display: inline-block"
                                    class="btn btn-block btn-primary"><i class="mdi mdi-plus"></i> Add Book</a>
                            @else
                            <a href="{{ url('admin/add-edit-product') }}"
                                style="max-width: 150px; float: right; display: inline-block"
                                class="btn btn-block btn-primary"><i class="mdi mdi-plus"></i> Add Book</a>
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
                                            <th>ID</th>
                                            <th>Book Name</th>
                                            <th>ISBN</th>
                                            <th>Image</th>
                                            <th>Category</th> {{-- Through the relationship --}}
                                            <th>Section</th> {{-- Through the relationship --}}
                                            @if ($adminType == 'admin' || $adminType == 'superadmin')
                                                <th>Stock</th>
                                            @endif
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($products as $key => $product)
                                            <tr>
                                                <td>{{ __($key + 1) }}</td>
                                                
                                                @if ($adminType === 'vendor')
                                                    {{-- Vendor: Display from ProductsAttribute --}}
                                                    <td> {{ $product->product->product_name ?? 'N/A' }}</td>
                                                    <td>ISBN-{{ $product->product->product_isbn ?? 'N/A' }} <span class="text-muted">({{ ucfirst($product->product->condition ?? 'N/A') }})</span></td>
                                                    <td>
                                                        @if (!empty($product->product->product_image))
                                                            <img style="width:120px; height:100px"
                                                                src="{{ asset('front/images/product_images/small/' . $product->product->product_image) }}">
                                                        @else
                                                            <img style="width:120px; height:100px"
                                                                src="{{ asset('front/images/product_images/small/no-image.png') }}">
                                                        @endif
                                                    </td>
                                                    <td>{{ $product->product->category->category_name ?? 'N/A' }}</td>
                                                    <td>{{ $product->product->section->name ?? 'N/A' }}</td>
                                                @else
                                                    {{-- Admin/Superadmin: Display from Product --}}
                                                    <td> {{ $product->product_name ?? 'N/A' }}</td>
                                                    <td>ISBN-{{ $product->product_isbn ?? 'N/A' }} <span class="text-muted">({{ ucfirst($product->condition ?? 'N/A') }})</span></td>
                                                    <td>
                                                        @if (!empty($product->product_image))
                                                            <img style="width:120px; height:100px"
                                                                src="{{ asset('front/images/product_images/small/' . $product->product_image) }}">
                                                        @else
                                                            <img style="width:120px; height:100px"
                                                                src="{{ asset('front/images/product_images/small/no-image.png') }}">
                                                        @endif
                                                    </td>
                                                    <td>{{ $product->category->category_name ?? 'N/A' }}</td>
                                                    <td>{{ $product->section->name ?? 'N/A' }}</td>
                                                    <td>
                                                        <span class="badge badge-info">{{ $product->total_stock ?? 0 }}</span>
                                                    </td>
                                                @endif
                                                {{-- <td>
                                                    @php
                                                        $attr = $product->firstAttribute ?? null;
                                                    @endphp

                                                    @if ($attr)
                                                        @if ($attr->admin_type == 'vendor')
                                                            <a target="_blank"
                                                                href="{{ url('admin/view-vendor-details/' . $attr->vendor_id) }}">
                                                                {{ ucfirst($attr->admin_type) }}
                                                            </a>
                                                        @else
                                                            {{ ucfirst($attr->admin_type) }}
                                                        @endif
                                                    @else
                                                        <span class="text-muted">Not Set</span>
                                                    @endif
                                                </td> --}}

                                                <td>
                                                    @if ($adminType === 'vendor')
                                                        {{-- Vendor: Use attribute status --}}
                                                        @if ($product->status == 1)
                                                            <a class="updateProductStatus" id="product-{{ $product->product_id }}"
                                                                product_id="{{ $product->product_id }}" data-url="{{ route('vendor.updateproductstatus') }}" href="javascript:void(0)">
                                                                <i style="font-size: 25px" class="mdi mdi-bookmark-check"
                                                                    status="Active"></i>
                                                            </a>
                                                        @else
                                                            <a class="updateProductStatus" id="product-{{ $product->product_id }}"
                                                                product_id="{{ $product->product_id }}" data-url="{{ route('vendor.updateproductstatus') }}" href="javascript:void(0)">
                                                                <i style="font-size: 25px" class="mdi mdi-bookmark-outline"
                                                                    status="Inactive"></i>
                                                            </a>
                                                        @endif
                                                    @else
                                                        {{-- Admin/Superadmin: Use product status --}}
                                                        @if ($product->status == 1)
                                                            <a class="updateProductStatus" id="product-{{ $product->id }}"
                                                                product_id="{{ $product->id }}" data-url="{{ route('admin.updateproductstatus') }}" href="javascript:void(0)">
                                                                <i style="font-size: 25px" class="mdi mdi-bookmark-check"
                                                                    status="Active"></i>
                                                            </a>
                                                        @else
                                                            <a class="updateProductStatus" id="product-{{ $product->id }}"
                                                                product_id="{{ $product->id }}" data-url="{{ route('admin.updateproductstatus') }}" href="javascript:void(0)">
                                                                <i style="font-size: 25px" class="mdi mdi-bookmark-outline"
                                                                    status="Inactive"></i>
                                                            </a>
                                                        @endif
                                                    @endif
                                                </td>



                                                <td>
                                                    @if ($adminType === 'vendor')
                                                        {{-- Vendor: Show edit, add stock, delete attribute --}}
                                                        {{-- <a title="Edit Book"
                                                            href="{{ url('vendor/add-edit-product/' . $product->product_id) }}">
                                                            <i style="font-size: 25px" class="mdi mdi-pencil-box"></i>
                                                        </a> --}}

                                                        <a href="#" title="Add Stock" data-bs-toggle="modal"
                                                            data-bs-target="#addAttributeModal" data-id="{{ $product->product_id }}"
                                                            data-name="{{ $product->product->product_name ?? 'N/A' }}"
                                                            data-stock="{{ $product->stock ?? 0 }}"
                                                            data-discount="{{ $product->product_discount ?? 0 }}"
                                                            id="openAddAttributeModal">
                                                            <i style="font-size: 25px" class="mdi mdi-plus-box"></i>
                                                        </a>

                                                        <a title="Add Multiple Images"
                                                            href="{{ url('vendor/add-images/' . $product->product_id) }}">
                                                            <i style="font-size: 25px" class="mdi mdi-library-plus"></i>
                                                        </a>

                                                        <a href="{{ url('vendor/delete-product-attribute/' . $product->id) }}"
                                                            onclick="return confirm('Are you sure you want to delete this product attribute?')"
                                                            title="Delete Product Attribute">
                                                            <i style="font-size: 25px" class="mdi mdi-file-excel-box"></i>
                                                        </a>
                                                    @else
                                                        {{-- Admin/Superadmin: Show edit, add stock, add images --}}
                                                        <a title="Edit Book"
                                                            href="{{ url('admin/add-edit-product/' . $product->id) }}">
                                                            <i style="font-size: 25px" class="mdi mdi-pencil-box"></i>
                                                        </a>

                                                        @php
                                                            // Get the first attribute for this product and current admin to show current stock/discount
                                                            $firstAttribute = \App\Models\ProductsAttribute::where('product_id', $product->id)
                                                                ->where('admin_id', Auth::guard('admin')->user()->id)
                                                                ->where('admin_type', 'admin')
                                                                ->first();
                                                        @endphp

                                                        {{-- <a href="#" title="Add Stock" data-bs-toggle="modal"
                                                            data-bs-target="#addAttributeModal" data-id="{{ $product->id }}"
                                                            data-name="{{ $product->product_name ?? 'N/A' }}"
                                                            data-stock="{{ $firstAttribute->stock ?? 0 }}"
                                                            data-discount="{{ $firstAttribute->product_discount ?? 0 }}"
                                                            id="openAddAttributeModal">
                                                            <i style="font-size: 25px" class="mdi mdi-plus-box"></i>
                                                        </a> --}}

                                                        <a title="Add Multiple Images"
                                                            href="{{ url('admin/add-images/' . $product->id) }}">
                                                            <i style="font-size: 25px" class="mdi mdi-library-plus"></i>
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
                                <div class="form-text">Available quantity: <span id="availableQuantity" class="fw-bold text-primary">0</span></div>
                            </div>

                            <!-- Discount input -->
                            <div class="col-md-6 mb-4">
                                <label for="bookDiscount" class="form-label fw-semibold">
                                    <i class="fas fa-percent me-2 text-info"></i>Discount (%) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="bookDiscount" placeholder="0" required
                                        min="0" max="100" step="0.01">
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
                            location.reload(); // reload to show updated attributes
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
