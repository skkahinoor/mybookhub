

    <!-- Form-Fields /- -->
    <style>
        .delivery-card {background:#fff;border:1px solid #f0f0f0;border-radius:10px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,0.06)}
        .delivery-card h4 {display:flex;align-items:center;gap:10px;margin:0 0 12px 0;font-weight:600;color:#2f3d4a}
        .ship-diff {display:flex;align-items:center;gap:10px;margin-bottom:14px}
        .ship-diff .label-text {margin:0;color:#495057}
        .group-inline {display:flex;gap:16px}
        .group-inline .group-1,.group-inline .group-2{flex:1}
        .text-field, .select-box {height:42px;border:1px solid #dee2e6;border-radius:6px;padding:8px 12px}
        .text-area {min-height:90px;border:1px solid #dee2e6;border-radius:6px;padding:10px 12px}
        .astk {color:#dc3545}
        @media(max-width:768px){.group-inline{flex-direction:column}}
    </style>

    <div class="delivery-card">
    @php 
        $user = \Illuminate\Support\Facades\Auth::user();
        $firstAddress = collect($deliveryAddresses)->first();
        $deliveryId = $firstAddress['id'] ?? '';
    @endphp
    <h4 class="section-h4 deliveryText">{{ empty($deliveryId) ? 'Add New Delivery Address' : 'Update Delivery Address' }}</h4>
    <div class="u-s-m-b-24 ship-diff">
        <input type="checkbox" class="check-box" id="ship-to-different-address" data-bs-toggle="collapse" data-bs-target="#showdifferent" {{ !empty($deliveryId) ? 'checked' : '' }}>

        @if (collect($deliveryAddresses)->count() > 0)
            <label class="label-text newAddress" for="ship-to-different-address">Update existing address?</label>
        @else
            <label class="label-text newAddress" for="ship-to-different-address">Check to add Delivery Address</label>
        @endif

    </div>
    <div class="collapse {{ !empty($deliveryId) ? 'show' : '' }}" id="showdifferent">

        <form action="{{ url('/save-delivery-address') }}" method="post">
            @csrf


            <input type="hidden" name="delivery_id" value="{{ $deliveryId }}">
            <div class="group-inline u-s-m-b-13">
                <div class="group-1 u-s-p-r-16">
                    <label for="delivery_name">Name
                        <span class="astk">*</span>
                    </label>
                    <input class="text-field" type="text" id="delivery_name" name="delivery_name" value="{{ $firstAddress['name'] ?? $user->name }}" placeholder="Full name">
                    <p id="delivery-delivery_name"></p>
                </div>
                <div class="group-2">
                    <label for="delivery_address">Address
                        <span class="astk">*</span>
                    </label>
                    <input class="text-field" type="text" id="delivery_address" name="delivery_address" value="{{ $firstAddress['address'] ?? $user->address }}" placeholder="Street, area, house no.">
                    <p id="delivery-delivery_address"></p>
                </div>
            </div>
            <div class="group-inline u-s-m-b-13">
                <div class="group-1 u-s-p-r-16">
                    <label for="delivery_city">City
                        <span class="astk">*</span>
                    </label>
                    <input class="text-field" type="text" id="delivery_city" name="delivery_city" value="{{ $firstAddress['city'] ?? $user->city }}" placeholder="City">
                    <p id="delivery-delivery_city"></p>
                </div>
                <div class="group-2">
                    <label for="delivery_state">State
                        <span class="astk">*</span>
                    </label>
                    <input class="text-field" type="text" id="delivery_state" name="delivery_state" value="{{ $firstAddress['state'] ?? $user->state }}" placeholder="State">
                    <p id="delivery-delivery_state"></p>
                </div>
            </div>
            <div class="u-s-m-b-13">
                <label for="select-country-extra">Country
                    <span class="astk">*</span>
                </label>
                <div class="select-box-wrapper">
                    <select class="select-box" id="delivery_country" name="delivery_country">
                        <option value="">Select Country</option>
                        @php $currentCountry = $firstAddress['country'] ?? $user->country; @endphp
                        @foreach ($countries as $country)
                            <option value="{{ $country['name'] }}" @if ($country['name'] == $currentCountry) selected @endif>{{ $country['name'] }}</option>
                        @endforeach

                    </select>
                    <p id="delivery-delivery_country"></p>
                </div>
            </div>
            <div class="u-s-m-b-13">
                <label for="delivery_pincode">Pincode
                    <span class="astk">*</span>
                </label>
                <input class="text-field" type="text" id="delivery_pincode" name="delivery_pincode" value="{{ $firstAddress['pincode'] ?? $user->pincode }}" placeholder="e.g. 560001">
                <p id="delivery-delivery_pincode"></p>
            </div>
            <div class="u-s-m-b-13">
                <label for="delivery_mobile">Mobile
                    <span class="astk">*</span>
                </label>
                <input class="text-field" type="text" id="delivery_mobile" name="delivery_mobile" value="{{ $firstAddress['mobile'] ?? $user->mobile }}" placeholder="10-digit mobile number">
                <p id="delivery-delivery_mobile"></p>
            </div>
            <div class="u-s-m-b-13">
                <button style="width: 100%" type="submit" class="btn btn-primary btnhover">Save Address</button>
            </div>

        </form>

        <!-- Form-Fields /- -->



    </div>
    </div>
    <div class="delivery-card" style="margin-top:16px;">
        <label for="order-notes">Order Notes</label>
        <textarea class="text-area" id="order-notes" placeholder="Notes about your order, e.g. special notes for delivery."></textarea>
    </div>
