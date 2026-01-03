{{-- This file is 'include'-ed in front/layout/header.php. We separated the Mini Cart widget and cut it from front/layout/header.blade.php to here --}}


<!-- Mini Cart -->
<div class="mini-cart-wrapper">
    <div class="mini-cart">
        <div class="mini-cart-header">
            YOUR CART
            <button type="button" class="button ion ion-md-close" id="mini-cart-close"></button>
        </div>
        <ul class="mini-cart-list">


            {{-- We'll place this $total_price inside the foreach loop to calculate the total price of all products in Cart. Check the end of the next foreach loop before @endforeach --}}
            @php $total_price = 0 @endphp

            @php
                $getCartItems = \App\Models\Cart::getCartItems();
            @endphp


            @foreach ($getCartItems as $item)
                {{-- $getCartItems is passed in from cart() method in Front/ProductsController.php --}}
                @php
                    $getDiscountPriceDetails = \App\Models\Product::getDiscountPriceDetails($item['product_id']);
                @endphp
                <li class="clearfix">
                    <a href="{{ url('product/' . $item['product_id']) }}">
                        @if (!empty($item['product']['product_image']))
                            <img src="{{ asset('front/images/product_images/small/' . $item['product']['product_image']) }}"
                                alt="{{ $item['product']['product_name'] ?? 'Product' }}">
                        @else
                            <img src="{{ asset('front/images/product_images/small/no-image.png') }}"
                                alt="No Image">
                        @endif
                        <span class="mini-item-name">{{ $item['product']['product_name'] ?? 'Product' }}</span>
                        <span class="mini-item-price">₹{{ $getDiscountPriceDetails['final_price'] ?? 0 }}</span>
                        <span class="mini-item-quantity"> x {{ $item['quantity'] ?? 1 }} </span>
                    </a>
                </li>
                {{-- This is placed here INSIDE the foreach loop to calculate the total price of all products in Cart --}}
                @php 
                    $itemTotal = ($getDiscountPriceDetails['final_price'] ?? 0) * ($item['quantity'] ?? 1);
                    $total_price = $total_price + $itemTotal;
                @endphp
            @endforeach



        </ul>
        <div class="mini-shop-total clearfix">
            <span class="mini-total-heading float-left">Total:</span>
            <span class="mini-total-price float-right">₹{{ $total_price ?? 0 }}</span>
        </div>
        <div class="mini-action-anchors">
            <a href="{{ url('cart') }}" class="cart-anchor">View Cart</a>
            <a href="{{ url('checkout') }}" class="checkout-anchor">Checkout</a>
        </div>
    </div>
</div>
<!-- Mini Cart /- -->



{{-- Solution of the problem where the X icon of the Mini Cart Widget doesn't work (doesn't close the widget) after Updating the Cart or Deleting items from it (meaning, AFTER MAKING AJAX CALLS). This happens after using AJAX while updating or deleting cart items because the Mini Cart Widget page gets loaded again and return-ed via AJAX but return-ed without its JavaScript! --}}
{{-- <script>
    $('#mini-cart-close').on('click', function () {
        $('.mini-cart-wrapper').removeClass('mini-cart-open');
    });
</script> --}}
