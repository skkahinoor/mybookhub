@extends('admin.layout.layout')



@section('content')
    <style>
        .multi-select-wrapper {
            position: relative;
            width: 300px;
        }

        .selected-options {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-bottom: 5px;
        }

        .selected-options span {
            background-color: #007bff;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 14px;
        }

        .selected-options span i {
            margin-left: 5px;
            cursor: pointer;
        }

        .search-input {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .options-list {
            border: 1px solid #ccc;
            border-top: none;
            max-height: 150px;
            overflow-y: auto;
            background: white;
            position: absolute;
            width: 100%;
            z-index: 100;
        }

        .options-list div {
            padding: 8px;
            cursor: pointer;
        }

        .options-list div:hover {
            background: #f1f1f1;
        }

        #searchResults {
            max-height: 200px;
            overflow-y: auto;
            cursor: pointer;
        }

        #searchResults .list-group-item {
            padding: 8px 12px;
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


                                <div class="d-flex align-items-center" role="group" aria-label="Condition"
                                    style="justify-content: flex-end !important;">
                                    <div>
                                        <input type="radio" class="btn-check" name="condition" id="new"
                                            value="new" autocomplete="off"
                                            {{ old('condition', $product->condition ?? '') === 'new' ? 'checked' : '' }}>
                                        <label class="" for="new">New</label>
                                    </div>

                                    <div>
                                        <input type="radio" class="btn-check" name="condition" id="old"
                                            value="old" autocomplete="off"
                                            {{ old('condition', $product->condition ?? '') === 'old' ? 'checked' : '' }}>
                                        <label class="" for="old">Old</label>
                                    </div>
                                </div>
                                <div class="card mb-4">
                                    <div class="card-header text-white" style="background-color: #928e8e">
                                        <i class="mdi mdi-map-marker"></i> Location
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mb-3">
                                            <label for="location">Coordinates (Latitude,Longitude)</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" readonly id="location"
                                                    name="location" placeholder="e.g. 28.6139,77.2090"
                                                    value="{{ old('location', $product->location ?? '') }}">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button"
                                                        id="getLocationBtn">
                                                        <i class="mdi mdi-crosshairs-gps"></i> Get Current Location
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">use the button to fetch the current
                                                location.</small>
                                        </div>

                                        {{-- <div class="form-group mb-3" style="position:relative;">
                                            <label for="searchLocation">Search Location</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                                                </div>
                                                <input type="text" class="form-control" id="searchLocation" placeholder="Type a place, address, or city...">
                                            </div>
                                            <div class="list-group" id="searchResults" style="position:absolute;z-index:1000;width:100%;display:none;"></div>
                                            <small class="form-text text-muted">Start typing to search for a location.</small>
                                        </div> --}}

                                        <div id="map"
                                            style="height: 300px; margin-top: 10px; border-radius: 8px; overflow: hidden;">
                                        </div>
                                    </div>
                                </div>
                                <style>
                                    #searchResults {
                                        max-height: 200px;
                                        overflow-y: auto;
                                        cursor: pointer;
                                        border-radius: 0 0 0.25rem 0.25rem;
                                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                                    }

                                    #searchResults .list-group-item {
                                        padding: 10px 16px;
                                        font-size: 15px;
                                        transition: background 0.2s;
                                    }

                                    #searchResults .list-group-item:hover,
                                    #searchResults .list-group-item.active {
                                        background: #f1f1f1;
                                        color: #007bff;
                                    }
                                </style>

                                <div class="form-group">
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
                                    <label for="product_name">Book Name</label>
                                    <input type="text" class="form-control" id="product_name"
                                        placeholder="Enter Book Name" name="product_name"
                                        @if (!empty($product['product_name'])) value="{{ $product['product_name'] }}" @else value="{{ old('product_name') }}" @endif>
                                    {{-- Repopulating Forms (using old() method): https://laravel.com/docs/9.x/validation#repopulating-forms --}}
                                </div>

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
                                    <label for="total_stock">Total Stock</label>
                                    <input type="number" class="form-control" id="total_stock"
                                        placeholder="Enter Total Stock" name="total_stock"
                                        @if (!empty($products_attributes['total_stock'])) value="{{ $product['total_stock'] }}" @else value="{{ old('total_stock') }}" @endif>
                                </div>

                                <div class="form-group">
                                    <label for="product_isbn">ISBN number</label>
                                    <input type="number" class="form-control" id="product_isbn"
                                        placeholder="Enter ISBN-" name="product_isbn"
                                        @if (!empty($product['product_isbn'])) value="{{ $product['product_isbn'] }}" @else value="{{ old('product_isbn') }}" @endif>
                                    {{-- Repopulating  Forms (using old() method): https://laravel.com/docs/9.x/validation#repopulating-forms --}}
                                </div>

                                <div class="form-group">
                                    <label for="product_price">Price</label>
                                    <input type="text" class="form-control" id="product_price"
                                        placeholder="Enter Book Price" name="product_price"
                                        @if (!empty($product['product_price'])) value="{{ $product['product_price'] }}" @else value="{{ old('product_price') }}" @endif>
                                    {{-- Repopulating Forms (using old() method): https://laravel.com/docs/9.x/validation#repopulating-forms --}}
                                </div>
                                <div class="form-group">
                                    <label for="product_discount">Discount (%)</label>
                                    <input type="number" class="form-control" id="product_discount"
                                        placeholder="Enter Book Discount" name="product_discount"
                                        @if (!empty($product['product_discount'])) value="{{ $product['product_discount'] }}" @else value="{{ old('product_discount') }}" @endif>
                                    {{-- Repopulating Forms (using old() method): https://laravel.com/docs/9.x/validation#repopulating-forms --}}
                                </div>


                                {{-- <div class="form-group">
                                    <label for="product_weight">Weight (in Kg)</label>
                                    <input type="number" class="form-control" id="product_weight"
                                        placeholder="Enter Book Weight" name="product_weight"
                                        @if (!empty($product['product_weight'])) value="{{ $product['product_weight'] }}" @else value="{{ old('product_weight') }}" @endif>
                                </div> --}}


                                <div class="form-group">
                                    <label for="product_image">Image (Recommended Size: 1000x1000)</label>
                                    {{-- Important Note: There are going to be 3 three sizes for the product image: Admin will upload the image with the recommended size which 1000*1000 which is the 'large' size (will store it in 'large' folder), but then we're going to use 'Intervention' package to get another two sizes: 500*500 which is the 'medium' size (will store it in 'medium' folder) and 250*250 which is the 'small' size (will store it in 'small' folder) --}}
                                    <input type="file" class="form-control" id="product_image" name="product_image">
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
                                <div class="form-group">
                                    <label for="is_featured">Featured Item (Yes/No)</label>
                                    <input type="checkbox" name="is_featured" id="is_featured" value="Yes"
                                        @if (!empty($product['is_featured']) && $product['is_featured'] == 'Yes') checked @endif>
                                </div>
                                <div class="form-group">
                                    <label for="is_bestseller">Best Seller Item (Yes/No)</label> {{-- Note: Only 'superadmin' can mark a product as 'bestseller', but 'vendor' can't --}}
                                    <input type="checkbox" name="is_bestseller" id="is_bestseller" value="Yes"
                                        @if (!empty($product['is_bestseller']) && $product['is_bestseller'] == 'Yes') checked @endif>
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

    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    <!-- Include Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Include Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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






    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script>
        // Existing geolocation button
        document.getElementById('getLocationBtn').addEventListener('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('location').value = position.coords.latitude + ',' + position
                        .coords.longitude;
                    updateMapMarker(position.coords.latitude, position.coords.longitude);
                }, function(error) {
                    alert('Unable to retrieve your location.');
                });
            } else {
                alert('Geolocation is not supported by this browser.');
            }
        });

        // Leaflet map integration
        var defaultLatLng = [28.6139, 77.2090]; // Default to New Delhi
        var locationInput = document.getElementById('location');
        var initialLatLng = locationInput.value ? locationInput.value.split(',').map(Number) : defaultLatLng;
        var map = L.map('map').setView(initialLatLng, 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        var marker = L.marker(initialLatLng, {
            draggable: true
        }).addTo(map);

        function updateMapMarker(lat, lng) {
            marker.setLatLng([lat, lng]);
            map.setView([lat, lng], 13);
        }

        marker.on('dragend', function(e) {
            var latlng = marker.getLatLng();
            locationInput.value = latlng.lat.toFixed(6) + ',' + latlng.lng.toFixed(6);
        });

        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            locationInput.value = e.latlng.lat.toFixed(6) + ',' + e.latlng.lng.toFixed(6);
        });

        locationInput.addEventListener('change', function() {
            var val = locationInput.value.split(',');
            if (val.length === 2) {
                var lat = parseFloat(val[0]);
                var lng = parseFloat(val[1]);
                if (!isNaN(lat) && !isNaN(lng)) {
                    updateMapMarker(lat, lng);
                }
            }
        });

        // Search Location Geocoding
        //const searchInput = document.getElementById('searchLocation');
        const searchResults = document.getElementById('searchResults');

        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            if (query.length < 3) {
                searchResults.style.display = 'none';
                searchResults.innerHTML = '';
                return;
            }
            searchResults.innerHTML = '<div class="list-group-item">Searching...</div>';
            searchResults.style.display = 'block';
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    searchResults.innerHTML = '';
                    if (data.length === 0) {
                        searchResults.innerHTML = '<div class="list-group-item">No results found.</div>';
                        return;
                    }
                    data.forEach(place => {
                        const item = document.createElement('a');
                        item.className = 'list-group-item list-group-item-action';
                        item.textContent = place.display_name;
                        item.href = '#';
                        item.addEventListener('click', function(e) {
                            e.preventDefault();
                            updateMapMarker(place.lat, place.lon);
                            locationInput.value =
                                `${parseFloat(place.lat).toFixed(6)},${parseFloat(place.lon).toFixed(6)}`;
                            searchResults.style.display = 'none';
                            searchInput.value = place.display_name;
                        });
                        searchResults.appendChild(item);
                    });
                });
        });
        document.addEventListener('click', function(e) {
            const searchInput = document.getElementById('searchInput');
            const searchResults = document.getElementById('searchResults');

            // Only add logic if both elements exist
            if (searchInput && searchResults) {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.style.display = 'none';
                }
            }
        });
    </script>




@endsection
