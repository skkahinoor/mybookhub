<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\DeliveryAddress;
use App\Models\Country;


class AddressController extends Controller
{
    // Checkout page Delivery Addresses Controller



    // Edit Delivery Addresses via AJAX (Page refresh and fill in the <input> fields with the authenticated/logged in user Delivery Addresses from the `delivery_addresses` database table when clicking on the Edit button) in front/products/delivery_addresses.blade.php (which is 'include'-ed in front/products/checkout.blade.php) via AJAX, check front/js/custom.js
    public function getDeliveryAddress(Request $request) {
        $condition = $request->query('condition');
        if (!in_array($condition, ['new', 'old'])) {
            $condition = 'new';
        }
        if ($request->ajax()) { // if the request is coming via an AJAX call
            $data = $request->all(); // Getting the name/value pairs array that are sent from the AJAX request (AJAX call)
            // dd($data);


            // Get the Delivery Address of the currently authenticated/logged-in user
            $deliveryAddress = DeliveryAddress::where('id', $data['addressid'])->first()->toArray(); // Get all the delivery addresses of the currently authenticated/logged-in user


            return response()->json([ // JSON Responses: https://laravel.com/docs/9.x/responses#json-responses
                'address' => $deliveryAddress
            ]);
        }
    }


    public function saveDeliveryAddress(Request $request) {
        $condition = $request->query('condition');
        if (!in_array($condition, ['new', 'old'])) {
            $condition = 'new';
        }
        if ($request->ajax()) { // if the request is coming via an AJAX call
            // Validation
            // Manually Creating Validators: https://laravel.com/docs/9.x/validation#manually-creating-validators
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'delivery_name'    => 'required|string|max:100',
                'delivery_address' => 'required|string|max:100',
                'delivery_city'    => 'required|string|max:100',
                'delivery_state'   => 'required|string|max:100',
                'delivery_country' => 'required|string|max:100',
                'delivery_pincode' => 'required|digits:6',
                'delivery_mobile'  => 'required|numeric|digits:10'
            ]);

            if ($validator->passes()) {
                $data = $request->all();
                $address = array();
                $address['user_id'] = Auth::user()->id;
                $address['name']    = $data['delivery_name'];
                $address['address'] = $data['delivery_address'];
                $address['city']    = $data['delivery_city'];
                $address['state']   = $data['delivery_state'];
                $address['country'] = $data['delivery_country'];
                $address['pincode'] = $data['delivery_pincode'];
                $address['mobile']  = $data['delivery_mobile'];

                if (!empty($data['delivery_id'])) {
                    DeliveryAddress::where('id', $data['delivery_id'])->update($address);
                } else {
                    DeliveryAddress::create($address);
                }

                $deliveryAddresses = DeliveryAddress::deliveryAddresses();
                $countries = Country::where('status', 1)->get()->toArray();

                return response()->json([
                    'view' => (string) \Illuminate\Support\Facades\View::make('front.products.delivery_addresses')->with(compact('deliveryAddresses', 'countries'))
                ]);

            } else {
                return response()->json([
                    'type'   => 'error',
                    'errors' => $validator->messages()
                ]);
            }
        } else {
            // Standard form submission
            $request->validate([
                'delivery_name'    => 'required|string|max:100',
                'delivery_address' => 'required|string|max:100',
                'delivery_city'    => 'required|string|max:100',
                'delivery_state'   => 'required|string|max:100',
                'delivery_country' => 'required|string|max:100',
                'delivery_pincode' => 'required|digits:6',
                'delivery_mobile'  => 'required|numeric|digits:10'
            ]);

            $data = $request->all();
            $address = [
                'user_id' => Auth::user()->id,
                'name'    => $data['delivery_name'],
                'address' => $data['delivery_address'],
                'city'    => $data['delivery_city'],
                'state'   => $data['delivery_state'],
                'country' => $data['delivery_country'],
                'pincode' => $data['delivery_pincode'],
                'mobile'  => $data['delivery_mobile'],
            ];

            if (!empty($data['delivery_id'])) {
                DeliveryAddress::where('id', $data['delivery_id'])->update($address);
                $msg = "Address updated successfully!";
            } else {
                DeliveryAddress::create($address);
                $msg = "Address added successfully!";
            }

            return redirect()->back()->with('success_message', $msg);
        }
    }


    public function removeDeliveryAddress(Request $request, $id = null) {
        if ($request->ajax()) {
            $data = $request->all();
            DeliveryAddress::where('id', $data['addressid'])->delete();
            $deliveryAddresses = DeliveryAddress::deliveryAddresses();
            $countries = Country::where('status', 1)->get()->toArray();
            return response()->json([
                'view' => (string) \Illuminate\Support\Facades\View::make('front.products.delivery_addresses')->with(compact('deliveryAddresses', 'countries'))
            ]);
        } else {
            // Standard request
            DeliveryAddress::where('id', $id)->delete();
            return redirect()->back()->with('success_message', 'Address removed successfully!');
        }
    }
}
