@include('user.layout.header')

<div class="container-fluid page-body-wrapper">
    @include('user.layout.sidebar')

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-8 grid-margin stretch-card mx-auto">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Request to Sell Old Book</h4>
                            <p class="card-description">Fill in the details below to request approval for selling your book</p>

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form class="forms-sample" action="{{ route('user.sell-book.store') }}" method="POST">
                                @csrf
                                
                                <div class="form-group">
                                    <label for="book_title">Book Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="book_title" name="book_title" 
                                           placeholder="Enter book title" value="{{ old('book_title') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="author_name">Author Name</label>
                                    <input type="text" class="form-control" id="author_name" name="author_name" 
                                           placeholder="Enter author name" value="{{ old('author_name') }}">
                                </div>

                                <div class="form-group">
                                    <label for="request_message">Request Message</label>
                                    <textarea class="form-control" id="request_message" name="request_message" 
                                              rows="4" placeholder="Tell us about the book you want to sell...">{{ old('request_message') }}</textarea>
                                    <small class="form-text text-muted">Maximum 1000 characters</small>
                                </div>

                                <button type="submit" class="btn btn-primary mr-2">Submit Request</button>
                                <a href="{{ route('user.sell-book.index') }}" class="btn btn-light">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('user.layout.footer')

