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

                            <form class="forms-sample" action="{{ route('user.sell-book.update', $request->id) }}" 
                                  method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="book_title">Book Title</label>
                                            <input type="text" class="form-control" id="book_title" 
                                                   value="{{ $request->book_title }}" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="author_name">Author Name</label>
                                            <input type="text" class="form-control" id="author_name" 
                                                   value="{{ $request->author_name ?? 'N/A' }}" disabled>
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
                                            <label for="year_published">Year Published</label>
                                            <input type="number" class="form-control" id="year_published" name="year_published" 
                                                   placeholder="e.g., 2020" min="1900" max="{{ date('Y') }}" 
                                                   value="{{ old('year_published') }}">
                                        </div>
                                    </div>
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
                                </div>

                                <div class="form-group">
                                    <label for="expected_price">Expected Price (₹) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="expected_price" name="expected_price" 
                                           placeholder="Enter expected price" step="0.01" min="0" 
                                           value="{{ old('expected_price') }}" required>
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
                                <a href="{{ route('user.sell-book.show', $request->id) }}" class="btn btn-light">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('user.layout.footer')

