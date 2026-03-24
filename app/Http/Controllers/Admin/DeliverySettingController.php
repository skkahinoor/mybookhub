<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeliverySetting;
use App\Models\HeaderLogo;
use Illuminate\Support\Facades\Session;

class DeliverySettingController extends Controller
{
    public function index()
    {
        Session::put('page', 'delivery_settings');

        $delivery_settings = DeliverySetting::orderBy('id', 'desc')->get();
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();

        return view('admin.delivery_settings.index', compact('delivery_settings', 'logos', 'headerLogo'));
    }

    public function addEdit(Request $request, $id = null)
    {
        Session::put('page', 'delivery_settings');
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();

        if ($id == "") {
            $title = "Add Delivery Setting";
            $delivery_setting = new DeliverySetting();
            $message = "Delivery setting added successfully!";
        } else {
            $title = "Edit Delivery Setting";
            $delivery_setting = DeliverySetting::find($id);
            if (!$delivery_setting) {
                return redirect()->back()->with('error_message', 'Delivery setting not found!');
            }
            $message = "Delivery setting updated successfully!";
        }

        if ($request->isMethod('post')) {
            $data = $request->all();

            $rules = [
                'min_order_amount' => 'required|numeric',
                'delivery_charge' => 'required|numeric',
            ];

            $this->validate($request, $rules);

            $delivery_setting->min_order_amount = $data['min_order_amount'];
            $delivery_setting->delivery_charge = $data['delivery_charge'];
            $delivery_setting->is_free_delivery = isset($data['is_free_delivery']) ? 1 : 0;
            $delivery_setting->status = isset($data['status']) ? 1 : 0;
            $delivery_setting->save();

            return redirect('admin/delivery-settings')->with('success_message', $message);
        }

        return view('admin.delivery_settings.add_edit', compact('title', 'delivery_setting', 'logos', 'headerLogo'));
    }

    public function delete($id)
    {
        DeliverySetting::where('id', $id)->delete();
        $message = "Delivery setting has been deleted successfully!";
        return redirect()->back()->with('success_message', $message);
    }

    public function updateStatus(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->all();
            if ($data['status'] == "Active") {
                $status = 0;
            } else {
                $status = 1;
            }
            DeliverySetting::where('id', $data['delivery_id'])->update(['status' => $status]);
            return response()->json(['status' => $status, 'delivery_id' => $data['delivery_id']]);
        }
    }
}
