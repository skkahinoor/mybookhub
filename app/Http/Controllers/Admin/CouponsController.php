<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

use App\Models\Coupon;
use App\Models\HeaderLogo;

class CouponsController extends Controller
{
    public function coupons()
    {
        if (!Auth::guard('admin')->user()->can('view_coupons')) {
            abort(403, 'Unauthorized action.');
        }
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'coupons');
        $adminType = Auth::guard('admin')->user()->type;
        $vendor_id = Auth::guard('admin')->user()->vendor_id;

        if ($adminType == 'vendor') {
            $vendorStatus = Auth::guard('admin')->user()->status;
            if ($vendorStatus == 0) {
                return redirect('admin/update-vendor-details/personal')->with('error_message', 'Your Vendor Account is not approved yet. Please make sure to fill your valid personal, business and bank details');
            }

            $vendor = \App\Models\Vendor::find($vendor_id);
            if ($vendor && $vendor->plan === 'free') {
                return redirect('admin/dashboard')
                    ->with('error_message', 'Coupon management is not available in Free plan. Please upgrade to Pro plan to create and manage coupons.');
            }

            $coupons = Coupon::where('vendor_id', $vendor_id)->get()->toArray();
        } else {
            $coupons = Coupon::get()->toArray();
        }

        return view('admin.coupons.coupons')->with(compact('coupons', 'logos', 'headerLogo', 'adminType'));
    }
    public function updateCouponStatus(Request $request)
    {
        if (!Auth::guard('admin')->user()->can('edit_coupons')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized action.'], 403);
        }
        // Check if vendor is on Free plan
        $adminType = Auth::guard('admin')->user()->type;
        if ($adminType == 'vendor') {
            $vendor = \App\Models\Vendor::find(Auth::guard('admin')->user()->vendor_id);
            if ($vendor && $vendor->plan === 'free') {
                return response()->json([
                    'status' => false,
                    'message' => 'Coupon management is not available in Free plan. Please upgrade to Pro plan.'
                ], 403);
            }
        }

        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        if ($request->ajax()) {
            $data = $request->all();
            if ($data['status'] == 'Active') {
                $status = 0;
            } else {
                $status = 1;
            }


            Coupon::where('id', $data['coupon_id'])->update(['status' => $status]);

            return response()->json([
                'status'   => $status,
                'coupon_id' => $data['coupon_id']
            ]);
        }
        return view('admin.coupons.coupons', compact('coupons', 'logos', 'headerLogo'));
    }

    public function deleteCoupon($id)
    {
        if (!Auth::guard('admin')->user()->can('delete_coupons')) {
            abort(403, 'Unauthorized action.');
        }
        $adminType = Auth::guard('admin')->user()->type;
        if ($adminType == 'vendor') {
            $vendor = \App\Models\Vendor::find(Auth::guard('admin')->user()->vendor_id);
            if ($vendor && $vendor->plan === 'free') {
                return redirect('admin/coupons')
                    ->with('error_message', 'Coupon deletion is not available in Free plan. Please upgrade to Pro plan.');
            }
        }

        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Coupon::where('id', $id)->delete();

        $message = 'Coupon has been deleted successfully!';

        return redirect()->back()->with('success_message', $message);
        return view('admin.coupons.coupons', compact('coupons', 'logos', 'headerLogo'));
    }

    public function addEditCoupon(Request $request, $id = null)
    {
        Session::put('page', 'coupons');
        $adminType = Auth::guard('admin')->user()->type;

        if ($adminType == 'vendor') {
            $vendor = \App\Models\Vendor::find(Auth::guard('admin')->user()->vendor_id);
            if ($vendor && $vendor->plan === 'free') {
                return redirect('admin/coupons')
                    ->with('error_message', 'Coupon creation and editing is not available in Free plan. Please upgrade to Pro plan.');
            }
        }

        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();

        if ($id == '') {
            if (!Auth::guard('admin')->user()->can('add_coupons')) {
                abort(403, 'Unauthorized action.');
            }
            $title = 'Add Coupon';
            $coupon = new Coupon;
            $selCats   = array();
            $selUsers  = array();
            $message = 'Coupon added successfully!';
        } else {
            if (!Auth::guard('admin')->user()->can('edit_coupons')) {
                abort(403, 'Unauthorized action.');
            }
            $title = 'Edit Coupon';
            $coupon = Coupon::find($id);
            $selCats   = explode(',', $coupon['categories']);
            $selUsers  = explode(',', $coupon['users']);
            $message = 'Coupon updated successfully!';
        }

        if ($request->isMethod('post')) {
            $data = $request->all();
            $rules = [
                'categories'    => 'required',
                'coupon_option' => 'required',
                'coupon_type'   => 'required',
                'amount_type'   => 'required',
                'amount'        => 'required|numeric',
                'expiry_date'   => 'nullable'
            ];

            $customMessages = [
                'categories.required'    => 'Select Categories',
                'coupon_option.required' => 'Select Coupon Option',
                'coupon_type.required'   => 'Select Coupon Type',
                'amount_type.required'   => 'Select Amount Type',
                'amount.required'        => 'Enter Amount',
                'amount.numeric'         => 'Enter Valid Amount',
            ];

            $this->validate($request, $rules, $customMessages);

            if (isset($data['categories'])) {
                $categories = implode(',', $data['categories']);
            } else {
                $categories = '';
            }

            if (isset($data['users'])) {
                $users = implode(',', $data['users']);
            } else {
                $users = '';
            }

            if ($data['coupon_option'] == 'Automatic') {
                $coupon_code = \Illuminate\Support\Str::random(8);
            } else {
                $coupon_code = $data['coupon_code'];
            }

            $adminType = Auth::guard('admin')->user()->type;
            if ($adminType == 'vendor') {
                $coupon->vendor_id = Auth::guard('admin')->user()->vendor_id;
            } else {
                $coupon->vendor_id = 0;
            }
            $coupon->coupon_option = $data['coupon_option'];
            $coupon->coupon_code   = $coupon_code;
            $coupon->categories    = $categories;
            $coupon->users         = $users ?? null;
            $coupon->coupon_type   = $data['coupon_type'];
            $coupon->amount_type   = $data['amount_type'];
            $coupon->amount        = $data['amount'];
            $coupon->expiry_date   = $data['expiry_date'] ?? null;
            $coupon->status        = 1;
            $coupon->save();
            return redirect('admin/coupons')->with('success_message', $message, 'logos');
            return view('admin.coupons.coupons', compact('coupons', 'logos'));
        }
        $categories = \App\Models\Section::with('categories')->get()->toArray();
        $users = \App\Models\User::select('email')->where('status', 1)->get();
        return view('admin.coupons.add_edit_coupon')->with(compact('title', 'coupon', 'categories',  'users', 'selCats',  'selUsers', 'logos', 'headerLogo'));
    }
}
