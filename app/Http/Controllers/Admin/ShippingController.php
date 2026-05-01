<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use App\Models\ShippingCharge;
use Illuminate\Support\Facades\Auth;

class ShippingController extends Controller
{
    // We got two Shipping Charges modules: Simple one (every country has its own shipping rate (price/cost/charges) based on the Delivery Address in the Checkout page) and Advanced one (every country has its own shipping rate based on the Delivery Address in the Checkout page, plus, other charges are calculated based on shipment weight). We created the `shipping_charges` database table for that matter. Also, the Shipping Charge module will be available in the Admin Panel for 'admin'-s only, not for 'vendor'-s



    // Render the Shipping Charges page (admin/shipping/shipping_charges.blade.php) in the Admin Panel for 'admin'-s only, not for vendors
    public function shippingCharges(Request $request) {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'shipping');

        if ($request->ajax()) {
            $data = ShippingCharge::query();
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addColumn('status', function ($row) {
                    $adminType = Auth::guard('admin')->user()->type;
                    $prefix = $adminType === 'vendor' ? 'vendor' : 'admin';
                    $statusIcon = $row->status == 1 ? 'mdi-bookmark-check' : 'mdi-bookmark-outline';
                    $statusText = $row->status == 1 ? 'Active' : 'Inactive';
                    
                    return '<a class="updateShippingStatus" id="shipping-' . $row->id . '"
                                shipping_id="' . $row->id . '"
                                data-url="' . route($prefix . '.updateshippingstatus') . '"
                                href="javascript:void(0)">
                                <i style="font-size: 25px" class="mdi ' . $statusIcon . '"
                                    status="' . $statusText . '"></i>
                            </a>';
                })
                ->addColumn('actions', function ($row) {
                    return '<a href="' . url('admin/edit-shipping-charges/' . $row->id) . '">
                                <i style="font-size: 25px" class="mdi mdi-pencil-box"></i>
                            </a>';
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }

        $adminType = Auth::guard('admin')->user()->type;
        return view('admin.shipping.shipping_charges')->with(compact('logos', 'headerLogo', 'adminType'));
    }

    // Update Shipping Status (active/inactive) via AJAX in admin/shipping/shipping_charages.blade.php, check admin/js/custom.js
    public function updateShippingStatus(Request $request) {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        if ($request->ajax()) {

            $shipping = ShippingCharge::find($request->shipping_id);

            // Toggle status
            $newStatus = $shipping->status == 1 ? 0 : 1;

            $shipping->update(['status' => $newStatus]);

            return response()->json([
                'status' => $newStatus,  // return 1 or 0
                'shipping_id' => $request->shipping_id
            ]);
        }
        return view('admin.shipping.shipping_charges', compact('shippingCharges', 'logos', 'headerLogo'));
    }

    // Render admin/shipping/edit_shipping_charges.blade.php page in case of HTTP 'GET' request ('Edit/Update Shipping Charges'), or hadle the HTML Form submission in the same page in case of HTTP 'POST' request
    public function editShippingCharges($id, Request $request) { // Route Parameters: Required Parameters: https://laravel.com/docs/9.x/routing#required-parameters
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        // Highlight the 'Shipping Charges' module in the Sidebar on the left in the Admin Panel. Correcting issues in the Skydash Admin Panel Sidebar using Session
        Session::put('page', 'shipping');

        if ($request->isMethod('post')) { // if the HTML Form in edit_shipping_charges.blade.php is submitted (WHETHER Add or Update!)
            $data = $request->all();
            // dd($data);

            ShippingCharge::where('id', $id)->update([
                '0_500g'      => $data['0_500g'],
                '501g_1000g'  => $data['501g_1000g'],
                '1001_2000g'  => $data['1001_2000g'],
                '2001g_5000g' => $data['2001g_5000g'],
                'above_5000g' => $data['above_5000g'],
            ]);
            $message = 'Shipping Charges updated successfully!';


            return redirect()->back()->with('success_message', $message, 'logos');
            return view('admin.shipping.shipping_charges', compact('shippingCharges', 'logos', 'headerLogo'));
        }

        $shippingDetails = ShippingCharge::where('id', $id)->first();
        $title = 'Edit Shipping Charges';


        return view('admin.shipping.edit_shipping_charges')->with(compact('shippingDetails', 'title', 'logos', 'headerLogo'));
    }

}
