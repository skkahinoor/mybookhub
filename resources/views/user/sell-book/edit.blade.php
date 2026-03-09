@include('user.layout.header')

<div class="container-fluid page-body-wrapper">
    @include('user.layout.sidebar')

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-10 grid-margin stretch-card mx-auto">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Fill Book Details</h4>
                            <p class="card-description">Your request has been approved! Please fill in the book details below.</p>

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form class="forms-sample" action="{{ route('student.sell-book.update', $request->id) }}" 
                                  method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="book_title">Book Title <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="book_title" name="book_title" 
                                                   value="{{ old('book_title', $request->book_title) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="author_name">Author Name</label>
                                            <input type="text" class="form-control" id="author_name" name="author_name" 
                                                   value="{{ old('author_name', $request->author_name) }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="isbn">ISBN <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="isbn" name="isbn" 
                                           placeholder="Enter ISBN number" value="{{ old('isbn') }}" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="publisher">Publisher</label>
                                            <input type="text" class="form-control" id="publisher" name="publisher" 
                                                   placeholder="Enter publisher name" value="{{ old('publisher') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="edition">Edition</label>
                                            <input type="text" class="form-control" id="edition" name="edition" 
                                                   placeholder="e.g., 1st, 2nd, 3rd" value="{{ old('edition') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="category_id">Category</label>
                                            <select class="form-control" id="category_id" name="category_id">
                                                <option value="">Select Category</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->category_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="subject_id">Subject</label>
                                            <select class="form-control" id="subject_id" name="subject_id">
                                                <option value="">Select Subject</option>
                                                @foreach($subjects as $subject)
                                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                                        {{ $subject->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="language_id">Language</label>
                                            <select class="form-control" id="language_id" name="language_id">
                                                <option value="">Select Language</option>
                                                @foreach($languages as $language)
                                                    <option value="{{ $language->id }}" {{ old('language_id') == $language->id ? 'selected' : '' }}>
                                                        {{ $language->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="book_type_id">Book Type</label>
                                            <select class="form-control" id="book_type_id" name="book_type_id">
                                                <option value="">Select Book Type</option>
                                                @foreach($bookTypes as $type)
                                                    <option value="{{ $type->id }}" {{ old('book_type_id') == $type->id ? 'selected' : '' }}>
                                                        {{ $type->book_type }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                   
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="book_condition">Book Condition <span class="text-danger">*</span></label>
                                            <select class="form-control" id="book_condition" name="book_condition" required>
                                                <option value="">Select Condition</option>
                                                <option value="Excellent" {{ old('book_condition') == 'Excellent' ? 'selected' : '' }}>Excellent</option>
                                                <option value="Good" {{ old('book_condition') == 'Good' ? 'selected' : '' }}>Good</option>
                                                <option value="Fair" {{ old('book_condition') == 'Fair' ? 'selected' : '' }}>Fair</option>
                                                <option value="Poor" {{ old('book_condition') == 'Poor' ? 'selected' : '' }}>Poor</option>
                                            </select>
                                        </div>
                                    </div>  
                                    <div class="col-md-6">
                                         <div class="form-group">
                                    <label for="expected_price">Expected Price (₹) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="expected_price" name="expected_price" 
                                           placeholder="Enter expected price" step="0.01" min="0" 
                                           value="{{ old('expected_price') }}" required>
                                     </div>
                                    </div>                                
                                </div>
                                <div class="form-group">
                                    <label for="book_description">Book Description</label>
                                    <textarea class="form-control" id="book_description" name="book_description" 
                                              rows="4" placeholder="Describe the book condition, any notes, etc...">{{ old('book_description') }}</textarea>
                                    <small class="form-text text-muted">Maximum 2000 characters</small>
                                </div>

                                <div class="form-group">
                                    <label for="book_image">Book Image</label>
                                    <input type="file" class="form-control-file" id="book_image" name="book_image" 
                                           accept="image/jpeg,image/jpg,image/png,image/gif">
                                    <small class="form-text text-muted">Upload a clear image of the book (Max: 2MB)</small>
                                </div>

                                <button type="submit" class="btn btn-primary mr-2">Submit Book Details</button>
                                <a href="{{ route('student.sell-book.show', $request->id) }}" class="btn btn-light">Cancel</a>
                            </form>
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
<!-- inject:js -->
<script src="{{ asset('user/js/off-canvas.js') }}"></script>
<script src="{{ asset('user/js/hoverable-collapse.js') }}"></script>
<script src="{{ asset('user/js/template.js') }}"></script>
<script src="{{ asset('user/js/settings.js') }}"></script>
<script src="{{ asset('user/js/todolist.js') }}"></script>
<!-- endinject -->

<script>
// Use standard JS to ensure it runs even if jQuery takes a sec to init
document.addEventListener('DOMContentLoaded', function() {
    let originalPrice = 0;
    let lastCheckedIsbn = '';
    const isbnInput = document.getElementById('isbn');
    const conditionSelect = document.getElementById('book_condition');
    const priceInput = document.getElementById('expected_price');

    if (isbnInput) {
        isbnInput.addEventListener('change', function() { checkIsbn(true); });
        isbnInput.addEventListener('blur', function() { checkIsbn(true); });
        
        // Initial check on load (without alert)
        if (isbnInput.value.length >= 7) {
            checkIsbn(false);
        }
    }

    if (conditionSelect) {
        conditionSelect.addEventListener('change', calculateSuggestedPrice);
    }

    function checkIsbn(showAlert) {
        let isbn = isbnInput.value.trim();
        if (isbn.length >= 7 && isbn !== lastCheckedIsbn) { 
            lastCheckedIsbn = isbn;
            console.log("Checking ISBN:", isbn);
            fetch("{{ url('student/sell-book/check-isbn') }}/" + encodeURIComponent(isbn), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(response => {
                if (response.success) {
                    console.log("Book Found:", response.data);
                    document.getElementById('book_title').value = response.data.product_name;
                    document.getElementById('author_name').value = response.data.author_name;
                    document.getElementById('publisher').value = response.data.publisher;
                    document.getElementById('edition').value = response.data.edition;
                    
                    // New fields auto-fill
                    if (response.data.category_id) document.getElementById('category_id').value = response.data.category_id;
                    if (response.data.subject_id) document.getElementById('subject_id').value = response.data.subject_id;
                    if (response.data.language_id) document.getElementById('language_id').value = response.data.language_id;
                    if (response.data.book_type_id) document.getElementById('book_type_id').value = response.data.book_type_id;

                    originalPrice = parseFloat(response.data.product_price);
                    calculateSuggestedPrice();
                    
                    if (showAlert) {
                        alert('Book found: ' + response.data.product_name + '! Details filled.');
                    }
                } else {
                    console.log("Book not found in DB.");
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }

    function calculateSuggestedPrice() {
        if (originalPrice > 0) {
            let condition = conditionSelect.value;
            let percentage = 0;

            switch(condition) {
                case 'Excellent': percentage = 0.8; break;
                case 'Good':      percentage = 0.7; break;
                case 'Fair':      percentage = 0.6; break;
                case 'Poor':      percentage = 0.5; break;
            }

            if (percentage > 0) {
                let suggestedPrice = (originalPrice * percentage).toFixed(2);
                priceInput.value = suggestedPrice;
                
                let hint = document.getElementById('price_hint');
                if (!hint) {
                    hint = document.createElement('small');
                    hint.id = 'price_hint';
                    hint.className = 'text-success d-block mt-1';
                    priceInput.parentNode.appendChild(hint);
                }
                hint.innerText = 'Suggested price (Admin Rule): ' + suggestedPrice;
            }
        }
    }
});
</script>
</body>
</html>

