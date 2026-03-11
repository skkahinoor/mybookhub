@include('user.layout.header')

<div class="container-fluid page-body-wrapper">
    @include('user.layout.sidebar')

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title mb-0">My Books</h4>
                                <a href="{{ route('student.sell-book.create') }}" class="btn btn-primary">
                                    <i class="mdi mdi-plus"></i> Add New Book
                                </a>
                            </div>

                            @if (session('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if (session('error_message'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if($userProducts->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Image</th>
                                                <th>Book Name</th>
                                                <th>Condition</th>
                                                <th>Category</th>
                                                <th>Price</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($userProducts as $key => $product)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>
                                                        @if(!empty($product->product_image))
                                                            <img src="{{ asset('front/images/product_images/small/'.$product->product_image) }}" alt="image" style="width:50px; height:50px; object-fit: cover; border-radius: 4px;">
                                                        @else
                                                            <img src="{{ asset('front/images/product_images/small/no-image.png') }}" alt="image" style="width:50px; height:50px; object-fit: cover; border-radius: 4px;">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $product->product_name }}
                                                        <br><small class="text-muted">ISBN: {{ $product->product_isbn }}</small>
                                                    </td>
                                                    <td>
                                                        {{ ucfirst($product->condition) }}
                                                    </td>
                                                    <td>
                                                        {{ $product->category->category_name ?? 'N/A' }}
                                                    </td>
                                                    <td>
                                                        {{ $product->product_price ? '₹'.number_format($product->product_price, 2) : 'N/A' }}
                                                    </td>
                                                    <td>
                                                        @if(isset($product->attributes[0]))
                                                            @if($product->attributes[0]->admin_approved == 1)
                                                                <span class="badge badge-success">Approved</span>
                                                            @else
                                                                <span class="badge badge-warning">Pending Review</span>
                                                            @endif
                                                        @else
                                                            <span class="badge badge-secondary">Unknown</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info text-center">
                                    <h5>No books added yet!</h5>
                                    <p>Click "Add New Book" to sell your first book.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('user.layout.footer')
