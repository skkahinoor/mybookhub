@include('user.layout.header')

<div class="container-fluid page-body-wrapper">
    @include('user.layout.sidebar')

    <style>
        /* ===== GLOBAL ===== */
        .card {
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.04);
            border: none;
            background: #fff;
        }

        .card-body {
            padding: 40px;
        }

        .card-title {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card-title i {
            color: #6366f1;
            font-size: 28px;
        }

        /* ===== FORM ===== */
        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            font-weight: 600;
            color: #4b5563;
            margin-bottom: 10px;
            font-size: 14px;
            display: block;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 1px solid #e5e7eb;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: #f9fafb;
            font-size: 15px;
            height: auto;
        }

        .form-control:focus {
            border-color: #6366f1;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            outline: none;
        }

        /* ===== SECTION HEADERS ===== */
        .form-section-title {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            margin: 35px 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #f3f4f6;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-section-title i {
            background: #eef2ff;
            color: #6366f1;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 18px;
        }

        .form-section-title:first-of-type {
            margin-top: 0;
        }

        /* ===== AUTHOR MULTI SELECT ===== */
        .multi-select-wrapper {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 15px;
            width: 100%;
        }

        .selected-options {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 12px;
        }

        .selected-options span {
            background: #6366f1;
            color: #fff;
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2);
        }

        /* ===== BUTTONS ===== */
        .btn {
            font-weight: 600;
            padding: 12px 28px;
            border-radius: 12px;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #6366f1;
            border: none;
            box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.4);
        }

        .btn-primary:hover {
            background: #4f46e5;
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.4);
        }

        .btn-light {
            background: #f3f4f6;
            color: #4b5563;
            border: none;
        }

        .btn-light:hover {
            background: #e5e7eb;
        }

        /* ===== SUGGESTIONS ===== */
        #book_name_suggestion {
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            margin-top: 5px;
        }

        .book-title-item {
            padding: 12px 16px;
            border: none !important;
            transition: 0.2s;
        }

        .book-title-item:hover {
            background: #eef2ff;
            color: #6366f1;
        }

        /* ===== IMAGE PREVIEW ===== */
        #isbnImagePreview img {
            border-radius: 16px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border: 4px solid #fff;
        }
    </style>
    
    <div class="main-panel">
        <div class="content-wrapper">
            <!-- Removed redundant header row -->
            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">
                                <i class="mdi mdi-book-plus"></i> {{ $title }}
                            </h4>

                            @if (Session::has('error_message'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Error:</strong> {{ Session::get('error_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if (Session::has('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success:</strong> {{ Session::get('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <form class="forms-sample mt-4"
                                @if (empty($product['id'])) action="{{ route('student.sell-book.store') }}"
                                @else
                                    action="{{ route('student.sell-book.update', $product['id']) }}" @endif
                                method="post" enctype="multipart/form-data">

                                @csrf
                                <input type="hidden" name="condition" value="old">

                                <!-- Section 1: Book Info -->
                                <div class="form-section-title">
                                    <i class="mdi mdi-information-outline"></i> Book Information
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="product_isbn">ISBN Number <small class="text-muted">(10-13 digits)</small></label>
                                            <input type="text" class="form-control" id="product_isbn" name="product_isbn"
                                                placeholder="Enter ISBN" maxlength="13"
                                                value="{{ old('product_isbn', $product['product_isbn'] ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group position-relative">
                                            <label for="product_name">Book Name</label>
                                            <input type="text" class="form-control" id="product_name" name="product_name"
                                                placeholder="Enter Book Name" autocomplete="off"
                                                value="{{ old('product_name', $product['product_name'] ?? '') }}">
                                            <ul id="book_name_suggestion" class="list-group"
                                                style="position:absolute; top:100%; left:0; right:0; z-index:9999; display:none; max-height:200px; overflow-y:auto; background:#fff; border:1px solid #ddd;">
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="old_book_condition_id">Book Condition <span class="text-danger">*</span></label>
                                            <select name="old_book_condition_id" id="old_book_condition_id" class="form-control text-dark">
                                                <option value="">Select Condition</option>
                                                @foreach($conditions as $cond)
                                                    <option value="{{ $cond->id }}" data-percentage="{{ $cond->percentage }}"
                                                        {{ old('old_book_condition_id', $product->firstAttribute->old_book_condition_id ?? '') == $cond->id ? 'selected' : '' }}>
                                                        {{ $cond->name }} ({{ $cond->percentage }}%)
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="product_price">Base Price <small class="text-muted">(Original Price)</small></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-light">₹</span>
                                                </div>
                                                <input type="text" class="form-control" id="product_price"
                                                    placeholder="Enter Price" name="product_price"
                                                    @if (!empty($product['product_price'])) value="{{ $product['product_price'] }}" @else value="{{ old('product_price') }}" @endif>
                                            </div>
                                            <small class="text-muted" id="calculatedPriceMsg">Final Selling Price: ₹--</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section 2: Categorization -->
                                <div class="form-section-title">
                                    <i class="mdi mdi-layers-outline"></i> Categorization
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="section_id">Education Level</label>
                                            <select name="section_id" id="section_id" class="form-control text-dark">
                                                <option value="">Select Level</option>
                                                @foreach ($sections as $section)
                                                    <option value="{{ $section->id }}"
                                                        {{ old('section_id', $product->section_id ?? '') == $section->id ? 'selected' : '' }}>
                                                        {{ $section->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="category_id">Board</label>
                                            <select name="category_id" id="category_id" class="form-control text-dark">
                                                <option value="">Select Board</option>
                                                @foreach ($categories as $cat)
                                                    <option value="{{ $cat->id }}"
                                                        {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                                        {{ $cat->category_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="subcategory_id">Class</label>
                                            <select name="subcategory_id" id="subcategory_id" class="form-control text-dark">
                                                <option value="">Select Class</option>
                                                @foreach ($subcategories as $subcat)
                                                    <option value="{{ $subcat->id }}"
                                                        {{ old('subcategory_id', $product->subcategory_id ?? '') == $subcat->id ? 'selected' : '' }}>
                                                        {{ $subcat->subcategory_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="subject_id">Subject</label>
                                            <select name="subject_id" id="subject_id" class="form-control text-dark">
                                                <option value="">Select Subject</option>
                                                @foreach ($subjects as $subject)
                                                    <option value="{{ $subject->id }}"
                                                        {{ old('subject_id', $product->subject_id ?? '') == $subject->id ? 'selected' : '' }}>
                                                        {{ $subject->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section 3: Additional Details -->
                                <div class="form-section-title">
                                    <i class="mdi mdi-card-text-outline"></i> Additional Details
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="publisher_id">Publisher</label>
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
                                            <label for="new_publisher">Add New Publisher (If not in list)</label>
                                            <div class="input-group">
                                                <input type="text" name="new_publisher" id="new_publisher"
                                                    class="form-control" placeholder="Type new publisher">
                                                <div class="input-group-append">
                                                    <button type="button" id="addPublisherBtn" class="btn btn-primary px-3">Add</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="edition_id">Edition</label>
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
                                            <label for="book_type_id">Book Type</label>
                                            <select name="book_type_id" id="book_type_id" class="form-control text-dark">
                                                <option value="">Select Type</option>
                                                @foreach ($bookTypes as $bt)
                                                    <option value="{{ $bt['id'] }}"
                                                        @if (!empty($product['book_type_id']) && $product['book_type_id'] == $bt['id']) selected @endif>
                                                        {{ $bt['book_type'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
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
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="user_old_book_image">Book Image <small class="text-muted">(Rec: 1000x1000)</small></label>
                                            <input type="file" class="form-control" id="user_old_book_image" name="user_old_book_image">
                                            <div id="isbnImagePreview" class="mt-2 text-center"></div>
                                            @if (!empty($product->firstAttribute->user_old_book_image))
                                                <div class="mt-2">
                                                    <a target="_blank" class="text-primary font-weight-bold"
                                                        href="{{ url('front/images/product_images/large/' . $product->firstAttribute->user_old_book_image) }}">
                                                        <i class="mdi mdi-eye"></i> View Current Image
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="authors">Authors</label>
                                    <div class="multi-select-wrapper">
                                        <div class="selected-options" id="selectedOptions"></div>
                                        <input type="text" id="searchInput" class="search-input form-control"
                                            placeholder="Search and select authors...">
                                        <div class="options-list" id="optionsList"></div>
                                    </div>
                                </div>
                                <select name="author_id[]" id="authors-select" multiple class="d-none">
                                    @foreach ($authors as $author)
                                        <option value="{{ $author->id }}"
                                            @if (!empty($product->id) && $product->authors->contains($author->id)) selected @endif>
                                            {{ $author->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" class="form-control" rows="5" placeholder="Tell us more about the book's condition or content...">{{ $product['description'] }}</textarea>
                                </div>

                                <!-- Section 4: Marketing -->
                                <div class="form-section-title">
                                    <i class="mdi mdi-bullhorn-outline"></i> SEO & Marketing (Optional)
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="meta_title">Meta Title</label>
                                            <input type="text" class="form-control" id="meta_title" name="meta_title"
                                                placeholder="Meta Title" value="{{ old('meta_title', $product['meta_title'] ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="meta_description">Meta Description</label>
                                            <input type="text" class="form-control" id="meta_description" name="meta_description"
                                                placeholder="Meta Description" value="{{ old('meta_description', $product['meta_description'] ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="meta_keywords">Meta Keywords</label>
                                            <input type="text" class="form-control" id="meta_keywords" name="meta_keywords"
                                                placeholder="Meta Keywords" value="{{ old('meta_keywords', $product['meta_keywords'] ?? '') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 pt-3 border-top d-flex gap-3">
                                    <button type="submit" id="submitBtn" class="btn btn-primary mr-2 px-5">
                                        <i class="mdi mdi-check"></i> List Book for Sale
                                    </button>
                                    <a href="{{ route('student.sell-book.index') }}" class="btn btn-light px-5">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->
    </div>
    <!-- main-panel ends -->
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
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveAttributesBtn">Save Attributes</button>
                </div>
            </div>
        </div>
    </div>
    <style>
        .readonly-select {
            pointer-events: none;
            background-color: #e9ecef;
        }
    </style>

    <script>
        const isbnLookupUrl = "{{ route('student.sell-book.check-isbn') }}";
        const nameSuggestion = "{{ route('student.sell-book.name-suggestions') }}"; 

        const boardsUrl = "{{ route('student.sell-book.boards') }}";
        const classesUrl = "{{ route('student.sell-book.classes') }}";
        const subjectsUrl = "{{ route('student.sell-book.subjects') }}";
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


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
                        url: nameSuggestion,
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

            // Calculate price based on condition
            function updatePriceByCondition() {
                let $priceInput = $('#product_price');
                let basePrice = parseFloat($priceInput.data('original-price')) || parseFloat($priceInput.val()) || 0;
                let $msg = $('#calculatedPriceMsg');

                let $selectedOption = $('#old_book_condition_id option:selected');
                let percentage = $selectedOption.data('percentage');
                let conditionName = $selectedOption.text().split('(')[0].trim();

                if (percentage && basePrice > 0) {
                    let finalPrice = (basePrice * parseFloat(percentage)) / 100;
                    $msg.html(`<span class="small font-weight-bold" style="color: green;">Calculated Selling Price: ₹${finalPrice.toFixed(2)} (${percentage}% of Original Price)</span>`);
                } else if (basePrice > 0) {
                    $msg.html(`<span class="small text-muted">Select condition for selling price calculation.</span>`);
                } else {
                    $msg.empty();
                }
            }

            // expose globally so other scripts (ISBN fetch) can call it
            window.updatePriceByCondition = updatePriceByCondition;

            $('#old_book_condition_id').on('change', function() {
                updatePriceByCondition();
            });

            // Store original price when manually entered
            $('#product_price').on('input', function() {
                let val = $(this).val();
                $(this).data('original-price', val);
                updatePriceByCondition(); 
            });

            updatePriceByCondition(); 
        });
    </script>

    <script>
        // Cascading selects REMOVED as per user request for "simple dropdowns"
        $(document).ready(function() {
                                            // Optional: You can still add small filters here if needed, 
                                            // but for now we just allow direct selection.
         });
    </script>

    <script>
        let globalStateReadOnly = false;

        function setReadonly(state) {
            globalStateReadOnly = state;
            $("#product_isbn, #product_name, #product_price, #description, #searchInput")
                .prop("readonly", state);

            $("#section_id, #category_id, #subcategory_id, #language_id, #publisher_id, #subject_id, #edition_id, #book_type_id")
                .css("pointer-events", state ? "none" : "auto")
                .css("background-color", state ? "#e9ecef" : "#fff");

            if (state) {
                $('#searchInput').hide();
                $('.selected-options i').hide();
            } else {
                $('#searchInput').show();
                $('.selected-options i').show();
            }
        }

        function fetchBookByISBN(isbn) {

            if (!isbn) return;

            $.ajax({
                url: isbnLookupUrl,
                type: "POST",
                data: {
                    isbn: isbn,
                    _token: "{{ csrf_token() }}"
                },

                success: function(res) {

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
                    $("#language_id").val(d.language_id || '');
                    $("#publisher_id").val(d.publisher_id || '');
                    $("#edition_id").val(d.edition_id || '');
                    $("#book_type_id").val(d.book_type_id || '');

                    // deterministically set select values
                                        $("#section_id").val(d.section_id || '');
                                        $("#category_id").val(d.category_id || '');
                                        $("#subcategory_id").val(d.subcategory_id || '');
                                        $("#subject_id").val(d.subject_id || '');
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
                    // We don't fetch and populate the image because the user uploads their own old book image.
                    $("#isbnImagePreview").html('');

                    // Save original price for condition calculation
                    if (d.product_price) {
                        $("#product_price").data('original-price', d.product_price);
                    }

                    // update price if it's already set to old
                    updatePriceByCondition();

                    // 🔒 DISABLE AFTER ALL VALUES SET
                    setReadonly(true);
                },

                error: function() {

                    setReadonly(false);

                    $("#product_name, #product_price, #description").val('');
                    $("#section_id, #category_id, #subcategory_id, #language_id, #publisher_id, #subject_id, #edition_id, #book_type_id").val(
                        '');
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
                url: '{{ route('student.sell-book.addPublisherAjax') }}',
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
                const removeIcon = globalStateReadOnly ? '' : `<i onclick="removeOption(${author.id})">&times;</i>`;
                span.innerHTML = `${author.name} ${removeIcon}`;
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
                        // Redirect to sell book index
                        window.location.href = "{{ route('student.sell-book.index') }}";
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).text('List Book for Sale');

                        // Check if product already exists for this admin/vendor
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON
                            .product_exists) {
                            const resp = xhr.responseJSON;

                            // First info message
                            Swal.fire({
                                icon: 'warning',
                                title: 'Product Already Exists',
                                text: resp.message ||
                                    'This product has already been added to your account.',
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
                                        $('#modal_product_id').val(resp
                                            .product_id);
                                        if (typeof resp.stock !== 'undefined') {
                                            $('#modal_stock').val(resp.stock);
                                        }
                                        if (typeof resp.product_discount !==
                                            'undefined') {
                                            $('#modal_product_discount').val(
                                                resp.product_discount);
                                        }
                                        $('#attributesModal').modal('show');
                                    }
                                });
                            });
                            return;
                        }

                        if (xhr.status === 422 && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;
                            let errorMsg = '';
                            $.each(errors, function(key, value) {
                                errorMsg += '• ' + value[0] + '<br>';
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                html: '<div class="text-left">' + errorMsg + '</div>',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#dc3545'
                            });
                        } else {
                            let detailedError = xhr.responseJSON ? (xhr.responseJSON.message || xhr.responseJSON.error) : 'An error occurred. Please try again.';
                            console.error('Server Error:', xhr.responseText);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: detailedError,
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


@include('user.layout.footer')
