{{-- Card-Based Modern Layout --}}
@extends('front.layout.layout3')

@section('content')

		<!-- inner page banner -->
		<div class="dz-bnr-inr overlay-secondary-dark dz-bnr-inr-sm" style="background-image:url(images/background/bg3.jpg);">
			<div class="container">
				<div class="dz-bnr-inr-entry">
					<h1>Wishlist</h1>
					<nav aria-label="breadcrumb" class="breadcrumb-row">
						<ul class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{ url('/') }}"> Home</a></li>
							<li class="breadcrumb-item">Wishlist</li>
						</ul>
					</nav>
				</div>
			</div>
		</div>
		<!-- inner page banner End-->
		<div class="content-inner-1">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
                        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                            <h3 class="mb-0">Your Wishlist</h3>
                            <div class="wishlist-actions">
                                <a href="{{ url('/') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-arrow-left"></i> &nbsp;&nbsp;Continue Shopping
                                </a>
                            </div>
                        </div>
						@if(Session::has('success_message'))
							<div class="alert alert-success alert-dismissible fade show" role="alert">
								<strong>Success:</strong> {{ Session::get('success_message') }}
								<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
							</div>
						@endif

						@if(Session::has('error_message'))
							<div class="alert alert-danger alert-dismissible fade show" role="alert">
								<strong>Error:</strong> {{ Session::get('error_message') }}
								<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
							</div>
						@endif

							@if(count($getWishlistItems) > 0)
								<div class="wishlist-meta d-flex align-items-center justify-content-between mb-3">
									<span class="text-muted">Showing {{ count($getWishlistItems) }} {{ count($getWishlistItems) === 1 ? 'item' : 'items' }}</span>
								</div>

								<div class="table-responsive wishlist-table-responsive">
									<table class="table check-tbl wishlist-table align-middle">
									<thead>
										<tr>
												<th class="col-product">Product</th>
												<th class="col-name">Product Name</th>
												<th class="col-price">Unit Price</th>
											{{-- <th>Quantity</th> --}}
												<th class="col-action">Add to Cart</th>
												<th class="col-action">Actions</th>
										</tr>
									</thead>
									<tbody id="wishlistItems">
										@include('front.products.wishlist_items', ['getWishlistItems' => $getWishlistItems])
									</tbody>
								</table>
							</div>

							<div class="row mt-4">
								<div class="col-md-6">
									<div class="card">
										<div class="card-body">
											<h5 class="card-title">Wishlist Summary</h5>
											<p class="card-text">Total Items: {{ count($getWishlistItems) }}</p>
											<p class="card-text">Estimated Total: ₹{{ number_format($total_price, 2) }}</p>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="card">
										<div class="card-body">
											<h5 class="card-title">Quick Actions</h5>
											<a href="{{ url('/cart') }}" class="btn btn-primary btn-sm">
												<i class="fas fa-shopping-cart"></i> &nbsp;&nbsp;View Cart
											</a>
											<a href="{{ url('/') }}" class="btn btn-outline-secondary btn-sm">
												<i class="fas fa-home"></i> &nbsp;&nbsp;Back to Home
											</a>
										</div>
									</div>
								</div>
							</div>
							@else
								<div class="wishlist-empty card shadow-sm border-0">
									<div class="card-body text-center py-5">
										<div class="empty-icon mb-3"><i class="fas fa-heart"></i></div>
										<h3 class="mb-2">Your Wishlist is Empty</h3>
										<p class="text-muted mb-4">Start adding products to your wishlist to save them for later.</p>
										<a href="{{ url('/') }}" class="btn btn-primary">
											<i class="fas fa-shopping-bag"></i> Start Shopping
										</a>
									</div>
								</div>
							@endif
					</div>
				</div>
			</div>
		</div>
@endsection

<style>
.wishlist-table-responsive { border-radius: 12px; overflow: hidden; }
.wishlist-table thead th { background: #f8f9fb; color: #495057; font-weight: 600; border-bottom: 1px solid #e9ecef; }
.wishlist-table tbody tr { transition: background-color .2s ease; }
.wishlist-table tbody tr:hover { background: #fcfcfd; }
.wishlist-table .col-product { width: 120px; }
.wishlist-table .col-name { min-width: 240px; }
.wishlist-table .col-price { width: 140px; }
.wishlist-table .col-action { width: 140px; }
.wishlist-empty .empty-icon { width: 64px; height: 64px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: #f3f6ff; color: #3b82f6; font-size: 24px; }

@media (max-width: 767.98px) {
	.wishlist-table .col-product { width: 90px; }
	.wishlist-actions .btn { width: 100%; }
}
</style>
