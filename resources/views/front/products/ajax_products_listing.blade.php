<!-- Row-of-Product-Container -->
<div class="row product-container grid-style">

    @foreach ($categoryProducts as $product)
        @php
            $product_image_path = 'front/images/product_images/small/' . $product->product_image;
            $getDiscountPrice = \App\Models\Product::getDiscountPrice($product->id);
        @endphp
        <div class="product-item col-lg-4 col-md-6 col-sm-6">
            <div class="item">
                <div class="image-container">
                    <a class="item-img-wrapper-link" href="{{ url('product/' . $product->id) }}">
                        @if (!empty($product->product_image) && file_exists($product_image_path))
                            <img class="img-fluid" src="{{ asset($product_image_path) }}" alt="Product">
                        @else
                            <img class="img-fluid" src="{{ asset('front/images/product_images/small/no-image.png') }}" alt="Product">
                        @endif
                    </a>
                    <div class="item-action-behaviors">
                        <a class="item-quick-look" data-toggle="modal" href="#quick-view">Quick Look</a>
                        <a class="item-addwishlist" href="javascript:void(0)" data-product-id="{{ $product->id }}">Add to Wishlist</a>
                    </div>
                </div>
                <div class="item-content">
                    <div class="what-product-is">
                        <ul class="bread-crumb">
                            <li>
                                <a href="#">{{ $product->product_name }}</a>
                            </li>
                        </ul>
                        <h6 class="item-title">
                            <a href="{{ url('product/' . $product->id) }}">{{ $product->product_name }}</a>
                        </h6>
                        <div class="item-description">
                            <p>{{ Str::limit($product->description, 100) }}</p>
                        </div>
                    </div>

                    @if ($getDiscountPrice > 0 && $getDiscountPrice < $product->product_price)
                        <div class="price-template">
                            <div class="item-new-price">
                                ₹{{ $getDiscountPrice }}
                            </div>
                            <div class="item-old-price">
                                ₹{{ $product->product_price }}
                            </div>
                        </div>
                    @else
                        <div class="price-template">
                            <div class="item-new-price">
                                ₹{{ $product->product_price }}
                            </div>
                        </div>
                    @endif
                </div>

                @php
                    $isProductNew = \App\Models\Product::isProductNew($product->id)
                @endphp
                @if ($isProductNew == 'Yes')
                    <div class="tag new">
                        <span>NEW</span>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>
<!-- Row-of-Product-Container /- -->
