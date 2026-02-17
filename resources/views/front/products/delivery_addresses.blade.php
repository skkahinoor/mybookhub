<!-- Form-Fields /- -->
<style>
    .delivery-card {
        background: #fff;
        border: 1px solid #f0f0f0;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06)
    }

    .delivery-card h4 {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0 0 12px 0;
        font-weight: 600;
        color: #2f3d4a
    }

    .ship-diff {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px
    }

    .ship-diff .label-text {
        margin: 0;
        color: #495057
    }

    .group-inline {
        display: flex;
        gap: 16px
    }

    .group-inline .group-1,
    .group-inline .group-2 {
        flex: 1
    }

    .text-field,
    .select-box {
        height: 42px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 8px 12px
    }

    .text-area {
        min-height: 90px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 10px 12px
    }

    .astk {
        color: #dc3545
    }

    @media(max-width:768px) {
        .group-inline {
            flex-direction: column
        }
    }
</style>

<div class="delivery-card">
    @php
        $user = \Illuminate\Support\Facades\Auth::user();
        $firstAddress = collect($deliveryAddresses)->first();
        $deliveryId = $firstAddress['id'] ?? '';

        // Pre-population logic for new users
        if (empty($deliveryId)) {
            $preName = $user->name;
            $preAddress = $user->address;
            $preCountryId = $user->country_id;
            $preStateId = $user->state_id;
            $preDistrictId = $user->district_id;
            $preBlockId = $user->block_id;
            $prePincode = $user->pincode;
            $preMobile = $user->phone;
        } else {
            $preName = $firstAddress['name'];
            $preAddress = $firstAddress['address'];
            $preCountryId = $firstAddress['country_id'];
            $preStateId = $firstAddress['state_id'];
            $preDistrictId = $firstAddress['district_id'];
            $preBlockId = $firstAddress['block_id'];
            $prePincode = $firstAddress['pincode'];
            $preMobile = $firstAddress['mobile'];
        }
    @endphp
    <h4 class="section-h4 deliveryText">{{ empty($deliveryId) ? 'Add New Delivery Address' : 'Update Delivery Address' }}
    </h4>
    <div class="u-s-m-b-24 ship-diff">
        <input type="checkbox" class="check-box" id="ship-to-different-address" data-bs-toggle="collapse"
            data-bs-target="#showdifferent" {{ !empty($deliveryId) ? 'checked' : '' }}>

        @if (collect($deliveryAddresses)->count() > 0)
            <label class="label-text newAddress" for="ship-to-different-address">Update existing address?</label>
        @else
            <label class="label-text newAddress" for="ship-to-different-address">Check to add Delivery Address</label>
        @endif

    </div>
    <div class="collapse {{ !empty($deliveryId) ? 'show' : '' }}" id="showdifferent">

        <form id="addressAddEditForm" action="{{ url('/save-delivery-address') }}" method="post">
            @csrf


            <input type="hidden" name="delivery_id" value="{{ $deliveryId }}">
            <div class="group-inline u-s-m-b-13">
                <div class="group-1 u-s-p-r-16">
                    <label for="delivery_name">Name
                        <span class="astk">*</span>
                    </label>
                    <input class="text-field" type="text" id="delivery_name" name="delivery_name"
                        value="{{ $preName }}" placeholder="Full name">
                    <p id="delivery-delivery_name"></p>
                </div>
                <div class="group-2">
                    <label for="delivery_address">Address
                        <span class="astk">*</span>
                    </label>
                    <input class="text-field" type="text" id="delivery_address" name="delivery_address"
                        value="{{ $preAddress }}" placeholder="Street, area, house no.">
                    <p id="delivery-delivery_address"></p>
                </div>
            </div>

            <div class="group-inline u-s-m-b-13">
                <div class="group-1 u-s-p-r-16">
                    <label for="country_id">Country <span class="astk">*</span></label>
                    <select class="select-box" id="country_id" name="country_id">
                        <option value="">Select Country</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country['id'] }}" @if ($country['id'] == $preCountryId) selected @endif>
                                {{ $country['name'] }}</option>
                        @endforeach
                    </select>
                    <p id="delivery-country_id"></p>
                </div>
                <div class="group-2">
                    <label for="state_id">State <span class="astk">*</span></label>
                    <select class="select-box" id="state_id" name="state_id">
                        <option value="">Select State</option>
                        @if (!empty($preCountryId))
                            @php $states = \App\Models\State::where('country_id', $preCountryId)->get(); @endphp
                            @foreach ($states as $state)
                                <option value="{{ $state->id }}" @if ($state->id == $preStateId) selected @endif>
                                    {{ $state->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    <p id="delivery-state_id"></p>
                </div>
            </div>

            <div class="group-inline u-s-m-b-13">
                <div class="group-1 u-s-p-r-16">
                    <label for="district_id">District <span class="astk">*</span></label>
                    <select class="select-box" id="district_id" name="district_id">
                        <option value="">Select District</option>
                        @if (!empty($preStateId))
                            @php $districts = \App\Models\District::where('state_id', $preStateId)->get(); @endphp
                            @foreach ($districts as $district)
                                <option value="{{ $district->id }}" @if ($district->id == $preDistrictId) selected @endif>
                                    {{ $district->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    <p id="delivery-district_id"></p>
                </div>
                <div class="group-2">
                    <label for="block_id">Block <span class="astk">*</span></label>
                    <select class="select-box" id="block_id" name="block_id">
                        <option value="">Select Block</option>
                        @if (!empty($preDistrictId))
                            @php $blocks = \App\Models\Block::where('district_id', $preDistrictId)->get(); @endphp
                            @foreach ($blocks as $block)
                                <option value="{{ $block->id }}" @if ($block->id == $preBlockId) selected @endif>
                                    {{ $block->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    <p id="delivery-block_id"></p>
                </div>
            </div>

            <div class="group-inline u-s-m-b-13">
                <div class="group-1 u-s-p-r-16">
                    <label for="delivery_pincode">Pincode
                        <span class="astk">*</span>
                    </label>
                    <input class="text-field" type="text" id="delivery_pincode" name="delivery_pincode"
                        value="{{ $prePincode }}" placeholder="e.g. 560001">
                    <p id="delivery-delivery_pincode"></p>
                </div>
                <div class="group-2">
                    <label for="delivery_mobile">Mobile
                        <span class="astk">*</span>
                    </label>
                    <input class="text-field" type="text" id="delivery_mobile" name="delivery_mobile"
                        value="{{ $preMobile }}" placeholder="10-digit mobile number">
                    <p id="delivery-delivery_mobile"></p>
                </div>
            </div>

            <div class="u-s-m-b-13">
                <button style="width: 100%" type="submit" class="btn btn-primary btnhover">Save Address</button>
            </div>

        </form>

        <script>
            $(document).ready(function() {
                $('#country_id').on('change', function() {
                    var country_id = $(this).val();
                    if (country_id) {
                        $.ajax({
                            url: "{{ route('user_states') }}",
                            type: "GET",
                            data: {
                                country: country_id
                            },
                            success: function(data) {
                                $('#state_id').empty().append(
                                    '<option value="">Select State</option>');
                                $.each(data, function(key, value) {
                                    $('#state_id').append('<option value="' + key + '">' +
                                        value + '</option>');
                                });
                                $('#district_id').empty().append(
                                    '<option value="">Select District</option>');
                                $('#block_id').empty().append(
                                    '<option value="">Select Block</option>');
                            }
                        });
                    }
                });

                $('#state_id').on('change', function() {
                    var state_id = $(this).val();
                    if (state_id) {
                        $.ajax({
                            url: "{{ route('user_districts') }}",
                            type: "GET",
                            data: {
                                state: state_id
                            },
                            success: function(data) {
                                $('#district_id').empty().append(
                                    '<option value="">Select District</option>');
                                $.each(data, function(key, value) {
                                    $('#district_id').append('<option value="' + key +
                                        '">' + value + '</option>');
                                });
                                $('#block_id').empty().append(
                                    '<option value="">Select Block</option>');
                            }
                        });
                    }
                });

                $('#district_id').on('change', function() {
                    var district_id = $(this).val();
                    if (district_id) {
                        $.ajax({
                            url: "{{ route('user_blocks') }}",
                            type: "GET",
                            data: {
                                district: district_id
                            },
                            success: function(data) {
                                $('#block_id').empty().append(
                                    '<option value="">Select Block</option>');
                                $.each(data, function(key, value) {
                                    $('#block_id').append('<option value="' + key + '">' +
                                        value + '</option>');
                                });
                            }
                        });
                    }
                });
            });
        </script>

        <!-- Form-Fields /- -->



    </div>
</div>
<div class="delivery-card" style="margin-top:16px;">
    <label for="order-notes">Order Notes</label>
    <textarea class="text-area" id="order-notes" placeholder="Notes about your order, e.g. special notes for delivery."></textarea>
</div>
