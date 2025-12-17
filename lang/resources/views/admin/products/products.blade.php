@extends('admin.layout.layout')
@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Books</h4>




                            <a href="{{ url('admin/add-edit-product') }}"
                                style="max-width: 150px; float: right; display: inline-block"
                                class="btn btn-block btn-primary">Add Book</a>

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
                                            <th>Added by</th> {{-- Through the relationship --}}
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($products as $key => $product)
                                            <tr>
                                                <td>{{ __($key + 1) }}</td>
                                                <td>{{ $product['product_name'] }}
                                                    ({{ $product['edition']['edition'] ?? 'not set' }} Edition)</td>
                                                <td>ISBN-{{ $product['product_isbn'] }}</td>

                                                <td>
                                                    @if (!empty($product['product_image']))
                                                        <img style="width:120px; height:100px"
                                                            src="{{ asset('front/images/product_images/small/' . $product['product_image']) }}">
                                                        {{-- Show the 'small' image size from the 'small' folder --}}
                                                    @else
                                                        <img style="width:120px; height:100px"
                                                            src="{{ asset('front/images/product_images/small/no-image.png') }}">
                                                        {{-- Show the 'no-image' Dummy Image: If you have for example a table with an 'images' column (that can exist or not exist), use a 'Dummy Image' in case there's no image. Example: https://dummyimage.com/  --}}
                                                    @endif
                                                </td>
                                                <td>{{ $product['category']['category_name'] ?? 'N/A' }}</td>
                                                {{-- Through the relationship --}}
                                                <td>{{ $product['section']['name'] ?? 'N/A' }}</td> {{-- Through the relationship --}}
                                                <td>
                                                    @if ($product['admin_type'] == 'vendor')
                                                        <a target="_blank"
                                                            href="{{ url('admin/view-vendor-details/' . $product['admin_id']) }}">{{ ucfirst($product['admin_type']) }}</a>
                                                    @else
                                                        {{ ucfirst($product['admin_type']) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($product['status'] == 1)
                                                        <a class="updateProductStatus" id="product-{{ $product['id'] }}"
                                                            product_id="{{ $product['id'] }}" href="javascript:void(0)">
                                                            {{-- Using HTML Custom Attributes. Check admin/js/custom.js --}}
                                                            <i style="font-size: 25px" class="mdi mdi-bookmark-check"
                                                                status="Active"></i> {{-- Icons from Skydash Admin Panel Template --}}
                                                        </a>
                                                    @else
                                                        {{-- if the admin status is inactive --}}
                                                        <a class="updateProductStatus" id="product-{{ $product['id'] }}"
                                                            product_id="{{ $product['id'] }}" href="javascript:void(0)">
                                                            {{-- Using HTML Custom Attributes. Check admin/js/custom.js --}}
                                                            <i style="font-size: 25px" class="mdi mdi-bookmark-outline"
                                                                status="Inactive"></i> {{-- Icons from Skydash Admin Panel Template --}}
                                                        </a>
                                                    @endif
                                                </td>



                                                <td>
                                                    <a title="Edit Book"
                                                        href="{{ url('admin/add-edit-product/' . $product['id']) }}">
                                                        <i style="font-size: 25px" class="mdi mdi-pencil-box"></i>
                                                        {{-- Icons from Skydash Admin Panel Template --}}
                                                    </a>

                                                    <a href="#" title="Add Edition" data-bs-toggle="modal"
                                                        data-bs-target="#addAttributeModal" data-id="{{ $product['id'] }}"
                                                        data-name="{{ $product['product_name'] }}"
                                                        id="openAddAttributeModal">
                                                        <i style="font-size: 25px" class="mdi mdi-plus-box"></i>
                                                    </a>


                                                    <a title="Add Multiple Images"
                                                        href="{{ url('admin/add-images/' . $product['id']) }}">
                                                        <i style="font-size: 25px" class="mdi mdi-library-plus"></i>
                                                        {{-- Icons from Skydash Admin Panel Template --}}
                                                    </a>


                                                    <a href="{{ url('admin/delete-product/' . $product['id']) }}"
                                                        onclick="return confirm('Are you sure you want to delete this product?')">
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
                        <!-- Book name + edition display (read-only) -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted">
                                <i class="fas fa-book me-2"></i>Book Name
                            </label>
                            <div class="input-group">
                               
                                <input type="text" class="form-control bg-light" id="bookNameEdition" readonly>
                            </div>
                        </div>

                        <!-- Edition select -->
                        <div class="mb-4">
                            <label for="bookEdition" class="form-label fw-semibold">
                                <i class="fas fa-tag me-2 text-info"></i>Edition <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                
                                <select id="bookEdition" class="form-control" required>
                                    <option value="" disabled selected>Choose Edition...</option>
                                    <!-- Editions will be populated dynamically -->
                                </select>
                            </div>
                            <div class="form-text">Select the edition for this book</div>
                        </div>

                        <div class="row">
                            <!-- Price input -->
                            <div class="col-md-6 mb-4">
                                <label for="bookPrice" class="form-label fw-semibold">
                                    <i class="fas fa-dollar-sign me-2 text-success"></i>Price <span
                                        class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                   
                                    <input type="number" class="form-control" id="bookPrice" placeholder="0.00"
                                        required min="0" step="0.01">
                                </div>
                                <div class="form-text">Enter price in rupees</div>
                            </div>

                            <!-- Stock input -->
                            <div class="col-md-6 mb-4">
                                <label for="bookStock" class="form-label fw-semibold">
                                    <i class="fas fa-boxes me-2 text-warning"></i>Stock <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    
                                    <input type="number" class="form-control" id="bookStock" placeholder="0" required
                                        min="0" step="1">
                                </div>
                                <div class="form-text">Available quantity</div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-0 p-4">
                        <button type="submit" class="btn btn-primary  px-4 fw-semibold">
                            <i class="fas fa-save me-2"></i>Create Attribute
                        </button>
                        <button type="button" class="btn btn-outline-secondary  px-4" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancel
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
            // Open modal and fill book name, fetch editions
            $(document).on('click', '#openAddAttributeModal', function() {
                var productId = $(this).data('id');
                var productName = $(this).data('name');
                $('#bookNameEdition').val(productName);

                // Store productId in modal for later use
                $('#addAttributeForm').data('product-id', productId);

                // Fetch editions
                $.ajax({
                    url: 'product/' + productId + '/editions',
                    type: 'GET',
                    success: function(data) {
                        var $editionSelect = $('#bookEdition');
                        $editionSelect.empty();
                        $editionSelect.append(
                            '<option value="" disabled selected>Select Edition</option>');
                        $.each(data, function(i, edition) {
                            $editionSelect.append('<option value="' + edition.id +
                                '">' + edition.edition + '</option>');
                        });
                    }
                });
            });

            // Handle form submit
            $('#addAttributeForm').on('submit', function(e) {
                e.preventDefault();
                var productId = $(this).data('product-id');
                var editionId = $('#bookEdition').val();
                var price = $('#bookPrice').val();
                var stock = $('#bookStock').val();

                $.ajax({
                    url: 'book-attribute',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: productId,
                        edition_id: editionId,
                        product_price: price,
                        stock: stock
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            $('#addAttributeModal').modal('hide');
                            location.reload(); // reload to show new product
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        alert('Error: ' + xhr.responseJSON.message);
                    }
                });
            });
        });
    </script>
@endsection
