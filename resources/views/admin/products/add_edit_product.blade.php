@extends('admin.layout.layout')



@section('content')
    <style>
        /* ===== GLOBAL ===== */
        .card {
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
        }

        .card-body {
            padding: 24px;
        }

        .card-title {
            font-weight: 600;
            color: #111827;
        }

        /* ===== FORM ===== */
        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-control {
            border-radius: 10px;
            padding: 10px 14px;
            border: 1px solid #d1d5db;
            transition: 0.2s;
        }

        .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
        }

        /* ===== RADIO (NEW / OLD) ===== */
        .btn-check+label {
            padding: 6px 16px;
            border-radius: 20px;
            border: 1px solid #d1d5db;
            cursor: pointer;
            margin-left: 6px;
            background: #f9fafb;
        }

        .btn-check:checked+label {
            background: #4f46e5;
            color: #fff;
            border-color: #4f46e5;
        }

        /* ===== SECTION HEADERS ===== */
        .form-section {
            margin-top: 30px;
        }

        .form-section h6 {
            font-size: 14px;
            font-weight: 600;
            color: #4b5563;
            margin-bottom: 14px;
            border-left: 4px solid #6366f1;
            padding-left: 10px;
        }

        /* ===== AUTHOR MULTI SELECT ===== */
        .multi-select-wrapper {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 12px;
            width: 100%;
        }

        .selected-options {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 8px;
        }

        .selected-options span {
            background: #6366f1;
            color: #fff;
            padding: 6px 12px;
            border-radius: 18px;
            font-size: 13px;
        }

        .selected-options span i {
            margin-left: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        .search-input {
            border-radius: 8px;
        }

        /* ===== OPTIONS DROPDOWN ===== */
        .options-list {
            border-radius: 0 0 12px 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .options-list div:hover {
            background: #eef2ff;
        }

        /* ===== IMAGE PREVIEW ===== */
        #isbnImagePreview img {
            margin-top: 10px;
            border-radius: 12px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, .1);
        }

        /* ===== BUTTONS ===== */
        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            border: none;
            border-radius: 10px;
            padding: 10px 22px;
        }

        .btn-light {
            border-radius: 10px;
        }

        /* ===== CHECKBOX ===== */
        input[type="checkbox"] {
            transform: scale(1.15);
            margin-right: 6px;
        }

        .condition-toggle {
            gap: 12px;
        }

        .condition-toggle label {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 20px;
            border: 1.5px solid #d1d5db;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            color: #4b5563;
            transition: all 0.25s ease;
        }

        .condition-toggle label i {
            font-size: 18px;
        }

        /* Checked state (NO background) */
        .condition-toggle .btn-check:checked+label {
            border-color: #4f46e5;
            color: #ffffff;
        }

        /* Hover */
        .condition-toggle label:hover {
            border-color: #6366f1;
            color: #6366f1;
        }
    </style>

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h4 class="card-title">Books</h4>
                        </div>
                        <div class="col-12 col-xl-4">
                            <div class="justify-content-end d-flex">
                                <div class="dropdown flex-md-grow-1 flex-xl-grow-0">
                                    <button class="btn btn-sm btn-light bg-white dropdown-toggle" type="button"
                                        id="dropdownMenuDate2" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="true">
                                        <i class="mdi mdi-calendar"></i> Today (10 Jan 2021)
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuDate2">
                                        <a class="dropdown-item" href="#">January - March</a>
                                        <a class="dropdown-item" href="#">March - June</a>
                                        <a class="dropdown-item" href="#">June - August</a>
                                        <a class="dropdown-item" href="#">August - November</a>
                                    </div><br>



                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            {{-- <form class="forms-sample"> --}}
                            <div class="row">
                                <div class="col">
                                    <h4 class="card-title">{{ $title }}</h4>
                                </div>
                                <div class="col">

                                </div>
                            </div>



                            {{-- Our Bootstrap error code in case of wrong current password or the new password and confirm password are not matching: --}}
                            {{-- Determining If An Item Exists In The Session (using has() method): https://laravel.com/docs/9.x/session#determining-if-an-item-exists-in-the-session --}}
                            @if (Session::has('error_message'))
                                <!-- Check AdminController.php, updateAdminPassword() method -->
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Error:</strong> {{ Session::get('error_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif



                            {{-- Displaying Laravel Validation Errors: https://laravel.com/docs/9.x/validation#quick-displaying-the-validation-errors --}}
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">


                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach

                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
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

                            <form class="forms-sample"
                                @if (empty($product['id'])) action="{{ url('admin/add-edit-product') }}"
                                @else
                                    action="{{ url('admin/add-edit-product/' . $product['id']) }}" @endif
                                method="post" enctype="multipart/form-data">
                                <!-- If the id is not passed in from the route, this measn 'Add a new Product', but if the id is passed in from the route, this means 'Edit the Product' -->
                                <!-- Using the enctype="multipart/form-data" to allow uploading files (images) -->
                                @csrf

                                <div class="d-flex align-items-center condition-toggle" role="group"
                                    aria-label="Condition">
                                    <div>
                                        <input type="radio" class="btn-check" name="condition" id="new"
                                            value="new" autocomplete="off"
                                            {{ old('condition', !empty($product->id) ? ($product->condition ?? 'new') : 'new') === 'new' ? 'checked' : '' }}>
                                        <label for="new">
                                            <i class="mdi mdi-book-open-page-variant"></i> New
                                        </label>
                                    </div>

                                    <div>
                                        <input type="radio" class="btn-check" name="condition" id="old"
                                            value="old" autocomplete="off"
                                            {{ old('condition', !empty($product->id) ? ($product->condition ?? 'new') : 'new') === 'old' ? 'checked' : '' }}>
                                        <label for="old">
                                            <i class="mdi mdi-history"></i> Old
                                        </label>
                                    </div>
                                </div>



                                <div class="form-group">
                                    <div class="form-group">
                                        <label for="product_isbn">ISBN Number <small class="text-muted">(10-13 digits)</small></label>
                                        <input type="text" class="form-control" id="product_isbn" name="product_isbn"
                                            placeholder="Enter ISBN (10-13 digits)" maxlength="13"
                                            value="{{ old('product_isbn', $product['product_isbn'] ?? '') }}">
                                    </div>

                                    <div class="form-group position-relative">
                                        <label for="product_name">Book Name</label>

                                        <input type="text" class="form-control" id="product_name" name="product_name"
                                            placeholder="Enter Book Name" autocomplete="off"
                                            value="{{ old('product_name', $product['product_name'] ?? '') }}">

                                        <ul id="book_name_suggestion" class="list-group"
                                            style="
            position:absolute;
            top:100%;
            left:0;
            right:0;
            z-index:9999;
            display:none;
            max-height:200px;
            overflow-y:auto;
            background:#fff;
            border:1px solid #ddd;
        ">
                                        </ul>
                                    </div>


                                    <label for="category_id">Select Category</label>
                                    {{-- <input type="text" class="form-control" id="category_id" placeholder="Enter Category Name" name="category_id" @if (!empty($product['name'])) value="{{ $product['category_id'] }}" @else value="{{ old('category_id') }}" @endif>  --}} {{-- Repopulating Forms (using old() method): https://laravel.com/docs/9.x/validation#repopulating-forms --}}
                                    <select name="category_id" id="category_id" class="form-control text-dark">
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $section)
                                            {{-- $categories are ALL the `sections` with their related 'parent' categories (if any (if exist)) and their subcategories or `child` categories (if any (if exist)) --}} {{-- Check ProductsController.php --}}
                                            <optgroup label="{{ $section['name'] }}"> {{-- sections --}}
                                                @foreach ($section['categories'] as $category)
                                                    {{-- parent categories --}} {{-- Check ProductsController.php --}}
                                                    <option value="{{ $category['id'] }}"
                                                        @if (!empty($product['category_id'] == $category['id'])) selected @endif>
                                                        {{ $category['category_name'] }}</option> {{-- parent categories --}}
                                                    @foreach ($category['sub_categories'] as $subcategory)
                                                        {{-- subcategories or child categories --}} {{-- Check ProductsController.php --}}
                                                        <option value="{{ $subcategory['id'] }}"
                                                            @if (!empty($product['category_id'] == $subcategory['id'])) selected @endif>
                                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;{{ $subcategory['category_name'] }}
                                                        </option> {{-- subcategories or child categories --}}
                                                    @endforeach
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                        {{-- <option value="{{ $category['id'] }}" @if (!empty($product['category_id']) && $product['category_id'] == $category['id']) selected @endif >{{ $category['name'] }}</option> --}}
                                    </select>
                                </div>



                                {{-- Including the related filters <select> box of a product DEPENDING ON THE SELECTED CATEGORY of the product --}}
                                <div class="loadFilters">
                                    @include('admin.filters.category_filters')
                                </div>
                                <div class="form-group">
                                    <label for="publisher_id">Publisher (Choose Existing)</label>
                                    <select class="form-control" name="publisher_id" id="publisher_id">
                                        <option value="">Select Publisher</option>
                                        @foreach ($publishers as $pub)
                                            <option value="{{ $pub['id'] }}"
                                                @if (!empty($product['publisher_id']) && $product['publisher_id'] == $pub['id']) selected @endif>
                                                {{ $pub['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="new_publisher">Or Add New Publisher</label>
                                    <div class="input-group">
                                        <input type="text" name="new_publisher" id="new_publisher"
                                            class="form-control" placeholder="Type new publisher name">
                                        <button type="button" id="addPublisherBtn" class="btn btn-primary">Add</button>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="subject_id">Select Subject</label>
                                    <select name="subject_id" id="subject_id" class="form-control text-dark">
                                        <option value="">Select Subject</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject['id'] }}"
                                                @if (!empty($product['subject_id']) && $product['subject_id'] == $subject['id']) selected @endif>
                                                {{ $subject['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="edition_id">Select Edition</label>
                                    <select name="edition_id" id="edition_id" class="form-control text-dark">
                                        <option value="">Select Edition</option>
                                        @foreach ($editions as $edition)
                                            <option value="{{ $edition->id }}"
                                                @if (!empty($product['edition_id']) && $product['edition_id'] == $edition->id) selected @endif>{{ $edition->edition }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="authors">Select Authors</label>
                                    <small class="text-muted">(Search and select multiple authors.)</small>

                                    <div class="multi-select-wrapper">
                                        <div class="selected-options" id="selectedOptions"></div>

                                        <input type="text" id="searchInput" class="search-input form-control mb-2"
                                            placeholder="Search Authors">

                                        <div class="options-list" id="optionsList"></div>
                                    </div>
                                </div>
                                <!-- Hidden Select Field (Just like old structure) -->
                                <select name="author_id[]" id="authors-select" multiple class="d-none">
                                    @foreach ($authors as $author)
                                        <option value="{{ $author->id }}"
                                            @if (!empty($product->id) && $product->authors->contains($author->id)) selected @endif>
                                            {{ $author->name }}
                                        </option>
                                    @endforeach
                                </select>


                                <div class="form-group">
                                    <label for="language_id">Book Language</label>
                                    <select name="language_id" id="language_id" class="form-control text-dark">
                                        <option value="">Select Language</option>
                                        @foreach ($languages as $language)
                                            <option value="{{ $language['id'] }}"
                                                @if (!empty($product['language_id']) && $product['language_id'] == $language['id']) selected @endif>
                                                {{ $language['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="product_price">Price</label>
                                    <input type="text" class="form-control" id="product_price"
                                        placeholder="Enter Book Price" name="product_price"
                                        @if (!empty($product['product_price'])) value="{{ $product['product_price'] }}" @else value="{{ old('product_price') }}" @endif>
                                    {{-- Repopulating Forms (using old() method): https://laravel.com/docs/9.x/validation#repopulating-forms --}}
                                </div>

                                <div class="form-group">
                                    <label for="product_image">Image (Recommended Size: 1000x1000)</label>
                                    {{-- Important Note: There are going to be 3 three sizes for the product image: Admin will upload the image with the recommended size which 1000*1000 which is the 'large' size (will store it in 'large' folder), but then we're going to use 'Intervention' package to get another two sizes: 500*500 which is the 'medium' size (will store it in 'medium' folder) and 250*250 which is the 'small' size (will store it in 'small' folder) --}}
                                    <input type="file" class="form-control" id="product_image" name="product_image">
                                    <div id="isbnImagePreview"></div>
                                    {{-- Show the admin image if exists --}}




                                    {{-- Show the product image, if any (if exits) --}}
                                    @if (!empty($product['product_image']))
                                        <a target="_blank"
                                            href="{{ url('front/images/product_images/large/' . $product['product_image']) }}">View
                                            Book Image</a>&nbsp;|&nbsp; {{-- Showing the 'large' image inside the 'large' folder --}}
                                        <a href="JavaScript:void(0)" class="confirmDelete" module="product-image"
                                            moduleid="{{ $product['id'] }}">Delete Book Image</a>
                                        {{-- Delete the product image from BOTH SERVER (FILESYSTEM) & DATABASE --}} {{-- Check admin/js/custom.js and web.php (routes) --}}
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" class="form-control" rows="3">{{ $product['description'] }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="meta_title">Meta Title</label>
                                    <input type="text" class="form-control" id="meta_title"
                                        placeholder="Enter Meta Title" name="meta_title"
                                        @if (!empty($product['meta_title'])) value="{{ $product['meta_title'] }}" @else value="{{ old('meta_title') }}" @endif>
                                    {{-- Repopulating Forms (using old() method): https://laravel.com/docs/9.x/validation#repopulating-forms --}}
                                </div>
                                <div class="form-group">
                                    <label for="meta_description">Meta Description</label>
                                    <input type="text" class="form-control" id="meta_description"
                                        placeholder="Enter Meta Description" name="meta_description"
                                        @if (!empty($product['meta_description'])) value="{{ $product['meta_description'] }}" @else value="{{ old('meta_description') }}" @endif>
                                    {{-- Repopulating Forms (using old() method): https://laravel.com/docs/9.x/validation#repopulating-forms --}}
                                </div>
                                <div class="form-group">
                                    <label for="meta_keywords">Meta Keywords</label>
                                    <input type="text" class="form-control" id="meta_keywords"
                                        placeholder="Enter Meta Keywords" name="meta_keywords"
                                        @if (!empty($product['meta_keywords'])) value="{{ $product['meta_keywords'] }}" @else value="{{ old('meta_keywords') }}" @endif>
                                    {{-- Repopulating Forms (using old() method): https://laravel.com/docs/9.x/validation#repopulating-forms --}}
                                </div>

                                <button type="submit" class="btn btn-primary mr-2">Submit</button>

                                <a href="{{ url('admin/products') }}" class="btn btn-light">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->
        @include('admin.layout.footer')
        <!-- partial -->
    </div>

    <!-- Modal for Stock and Discount -->
    <div class="modal fade" id="attributesModal" tabindex="-1" role="dialog" aria-labelledby="attributesModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="attributesModalLabel">Add Stock & Discount</h5>
                </div>
                <div class="modal-body">
                    <form id="attributesForm">
                        @csrf
                        <input type="hidden" id="modal_product_id" name="product_id">

                        <div class="form-group">
                            <label for="modal_stock">Stock <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="modal_stock" name="stock" min="0"
                                required>
                            <small class="form-text text-muted">Enter the total stock quantity</small>
                        </div>

                        <div class="form-group">
                            <label for="modal_product_discount">Discount (%)</label>
                            <input type="number" class="form-control" id="modal_product_discount"
                                name="product_discount" min="0" max="100" step="0.01" value="0">
                            <small class="form-text text-muted">Enter discount percentage (0-100)</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="saveAttributesBtn">Save Attributes</button>
                </div>
            </div>
        </div>
    </div>
    <style>.readonly-select {
    pointer-events: none;
    background-color: #e9ecef;
}</style>


    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    <!-- Include Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Include Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {

            let debounceTimer = null;

            $('#product_name').on('keyup', function() {

                let query = $(this).val().trim();

                if (query.length < 2) {
                    $('#book_name_suggestion').hide();
                    return;
                }

                clearTimeout(debounceTimer);

                debounceTimer = setTimeout(function() {

                    $.ajax({
                        url: "{{ url('/admin/book/name-suggestions') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            query: query
                        },
                        success: function(res) {

                            if (!res.data || res.data.length === 0) {
                                $('#book_name_suggestion').hide();
                                return;
                            }

                            let html = '';
                            res.data.forEach(book => {
                                html += `
                        <li class="list-group-item book-title-item"
                            data-isbn="${book.product_isbn}"
                            style="cursor:pointer">
                            ${book.product_name}
                        </li>`;
                            });

                            $('#book_name_suggestion').html(html).show();
                        }
                    });

                }, 300);
            });

            $(document).on('click', '.book-title-item', function() {

                let isbn = $(this).data('isbn');

                $('#product_isbn').val(isbn);
                $('#product_name').val($(this).text().trim());
                $('#book_name_suggestion').hide();

                fetchBookByISBN(isbn);
            });

            $(document).click(function(e) {
                if (!$(e.target).closest('#product_name, #book_name_suggestion').length) {
                    $('#book_name_suggestion').hide();
                }
            });

        });
    </script>

    <script>
        function setReadonly(state) {

            $("#product_name, #product_price, #description")
                .prop("readonly", state);

            $("#category_id, #language_id, #publisher_id, #subject_id, #edition_id")
                .toggleClass("readonly-select", state);

            $("#authors-select")
                .toggleClass("readonly-select", state);
        }

        function fetchBookByISBN(isbn) {

    if (!isbn) return;

    $.ajax({
        url: "{{ route('admin.book.isbnLookup') }}",
        type: "POST",
        data: {
            isbn: isbn,
            _token: "{{ csrf_token() }}"
        },

        success: function (res) {

            if (!res.status) {
                setReadonly(false);
                alert(res.message);
                return;
            }

            let d = res.data || {};

            // 🔓 ENABLE EVERYTHING FIRST
            setReadonly(false);

            // text fields
            $("#product_name").val(d.product_name || '');
            $("#product_price").val(d.product_price || '');
            $("#description").val(d.description || '');

            // dropdowns (NO trigger)
            $("#category_id").val(d.category_id || '');
            $("#language_id").val(d.language_id || '');
            $("#publisher_id").val(d.publisher_id || '');
            $("#subject_id").val(d.subject_id || '');
            $("#edition_id").val(d.edition_id || '');

            // authors (SAFE CHECK)
            if (Array.isArray(d.author_ids) && typeof authors !== 'undefined') {
                selected = [];
                d.author_ids.forEach(id => {
                    const author = authors.find(a => a.id == id);
                    if (author) selected.push(author);
                });

                if (typeof renderSelected === 'function') {
                    renderSelected();
                }
            }

            // image
            if (d.image) {
                $("#isbnImagePreview").html(
                    `<img src="{{ asset('front/images/product_images/small') }}/${d.image}" width="150">`
                );
            } else {
                $("#isbnImagePreview").html('');
            }

            // 🔒 DISABLE AFTER ALL VALUES SET
            setReadonly(true);
        },

        error: function () {

            setReadonly(false);

            $("#product_name, #product_price, #description").val('');
            $("#category_id, #language_id, #publisher_id, #subject_id, #edition_id").val('');
            $("#isbnImagePreview").html('');

            alert("No book found for this ISBN. Please enter all details manually.");
        }
    });
}



        // ✅ MANUAL ISBN CHANGE
        $(document).on("change", "#product_isbn", function() {
            fetchBookByISBN($(this).val().trim());
        });
    </script>

    <script>
        $('#addPublisherBtn').click(function() {
            let publisherName = $('#new_publisher').val().trim();
            if (publisherName === '') {
                alert('Please enter a publisher name.');
                return;
            }

            $.ajax({
                url: '{{ route('admin.addPublisherAjax') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    name: publisherName
                },
                success: function(response) {
                    if (response.status === 'success') {
                        // Add to dropdown
                        $('#publisher_id').append('<option value="' + response.id + '" selected>' +
                            response.name + '</option>');
                        $('#new_publisher').val(''); // Clear input
                        alert('Publisher added!');
                    } else {
                        alert(response.message || 'Something went wrong.');
                    }
                },
                error: function(xhr) {
                    alert('Error occurred. See console.');
                    console.log(xhr.responseText);
                }
            });
        });
    </script>


    <script>
        $(document).ready(function() {
            $('.select2-authors').select2({
                placeholder: "Select authors",
                allowClear: true
            });
        });
    </script>

    <script>
        const authors = @json($authors);
        const oldSelected = @json(!empty($product) ? $product->authors->pluck('id') : []);

        const selectedOptions = document.getElementById('selectedOptions');
        const searchInput = document.getElementById('searchInput');
        const optionsList = document.getElementById('optionsList');
        const hiddenSelect = document.getElementById('authors-select');

        let selected = [];

        function renderOptions(filter = '') {
            optionsList.innerHTML = '';
            const filteredAuthors = authors.filter(author =>
                author.name.toLowerCase().includes(filter.toLowerCase()) &&
                !selected.some(sel => sel.id === author.id)
            );

            if (filteredAuthors.length > 0) {
                filteredAuthors.forEach(author => {
                    const option = document.createElement('div');
                    option.textContent = author.name;
                    option.dataset.id = author.id;
                    option.onclick = () => selectOption(author);
                    optionsList.appendChild(option);
                });
                optionsList.style.display = 'block';
            } else {
                optionsList.style.display = 'none';
            }
        }

        function renderSelected() {
            selectedOptions.innerHTML = '';
            Array.from(hiddenSelect.options).forEach(option => {
                option.selected = false;
            });

            selected.forEach(author => {
                const span = document.createElement('span');
                span.innerHTML = `${author.name} <i onclick="removeOption(${author.id})">&times;</i>`;
                selectedOptions.appendChild(span);

                const option = hiddenSelect.querySelector(`option[value="${author.id}"]`);
                if (option) option.selected = true;
            });
        }

        function selectOption(author) {
            if (!selected.find(item => item.id === author.id)) {
                selected.push(author);
                renderSelected();
                searchInput.value = '';
                renderOptions();
            }
        }

        function removeOption(id) {
            selected = selected.filter(author => author.id !== id);
            renderSelected();
            renderOptions();
        }

        searchInput.addEventListener('input', (e) => {
            renderOptions(e.target.value);
        });

        searchInput.addEventListener('focus', () => {
            renderOptions(searchInput.value);
        });

        document.addEventListener('click', function(event) {
            if (!event.target.closest('.multi-select-wrapper')) {
                optionsList.style.display = 'none';
            }
        });

        // Initialize with old selected authors
        if (oldSelected.length > 0) {
            oldSelected.forEach(id => {
                const author = authors.find(a => a.id === id);
                if (author) selected.push(author);
            });
        }

        renderSelected();
    </script>

    <script>
        $(document).ready(function() {
            // Handle form submission
            $('form').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                const formData = new FormData(form[0]);
                const submitBtn = $('#submitBtn');

                // Disable submit button
                submitBtn.prop('disabled', true).text('Saving...');

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.show_modal && response.product_id) {
                            // Show modal for new products
                            $('#modal_product_id').val(response.product_id);
                            $('#attributesModal').modal('show');
                            submitBtn.prop('disabled', false).text('Submit');
                        } else {
                            // Redirect for existing products or if no modal needed
                            window.location.href = "{{ url('admin/products') }}";
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).text('Submit');

                        // Check if product already exists for this admin/vendor
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.product_exists) {
                            const resp = xhr.responseJSON;

                            // First info message
                            Swal.fire({
                                icon: 'warning',
                                title: 'Product Already Exists',
                                text: resp.message || 'This product has already been added to your account.',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#dc3545'
                            }).then(() => {
                                // Then ask if user wants to update stock/discount
                                Swal.fire({
                                    icon: 'question',
                                    title: 'Update Stock / Discount?',
                                    text: 'Do you want to update stock and discount for this product?',
                                    showCancelButton: true,
                                    confirmButtonText: 'Yes',
                                    cancelButtonText: 'No',
                                    confirmButtonColor: '#28a745',
                                    cancelButtonColor: '#dc3545'
                                }).then((result) => {
                                    if (result.isConfirmed && resp.product_id) {
                                        // Prefill and open the same attributes modal used on products list
                                        $('#modal_product_id').val(resp.product_id);
                                        if (typeof resp.stock !== 'undefined') {
                                            $('#modal_stock').val(resp.stock);
                                        }
                                        if (typeof resp.product_discount !== 'undefined') {
                                            $('#modal_product_discount').val(resp.product_discount);
                                        }
                                        $('#attributesModal').modal('show');
                                    }
                                });
                            });
                            return;
                        }

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorMsg = 'Validation errors:\n';
                            $.each(errors, function(key, value) {
                                errorMsg += value[0] + '\n';
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: errorMsg,
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#dc3545'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred. Please try again.',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    }
                });
            });

            // Handle save attributes button
            $('#saveAttributesBtn').on('click', function() {
                const form = $('#attributesForm');
                const formData = form.serialize();
                const btn = $(this);

                btn.prop('disabled', true).text('Saving...');

                $.ajax({
                    url: "{{ route('admin.products.saveAttributes') }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#attributesModal').modal('hide');
                        window.location.href = "{{ url('admin/products') }}";
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).text('Save Attributes');
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorMsg = 'Validation errors:\n';
                            $.each(errors, function(key, value) {
                                errorMsg += value[0] + '\n';
                            });
                            alert(errorMsg);
                        } else {
                            alert(
                                'An error occurred while saving attributes. Please try again.'
                            );
                        }
                    }
                });
            });

            // Handle skip button
            $('#skipAttributesBtn').on('click', function() {
                $('#attributesModal').modal('hide');
                window.location.href = "{{ url('admin/products') }}";
            });
        });
    </script>


@endsection
