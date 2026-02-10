<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\State;
use App\Models\HeaderLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DistrictController extends Controller
{
    public function index()
    {
        Session::put('page', 'districts');
        $districts = District::with('state')->get();
        $states = State::where('status', 1)->get();
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();

        return view('admin.districts.index', compact('districts', 'states', 'headerLogo', 'logos'));
    }

    public function addEditDistrict(Request $request, $id = null)
    {
        if ($id == "") {
            $title = "Add District";
            $district = new District;
            $message = "District added successfully!";
        } else {
            $title = "Edit District";
            $district = District::find($id);
            $message = "District updated successfully!";
        }

        if ($request->isMethod('post')) {
            $data = $request->all();

            $rules = [
                'state_id' => 'required',
                'district_name' => 'required|string|max:255',
            ];
            $customMessages = [
                'state_id.required' => 'State is required',
                'district_name.required' => 'District Name is required',
            ];
            $this->validate($request, $rules, $customMessages);

            $district->state_id = $data['state_id'];
            $district->name = $data['district_name'];
            $district->status = 1;
            $district->save();

            return redirect('admin/districts')->with('success_message', $message);
        }

        return response()->json([
            'title' => $title,
            'district' => $district
        ]);
    }

    public function deleteDistrict($id)
    {
        District::where('id', $id)->delete();
        return redirect()->back()->with('success_message', 'District deleted successfully!');
    }

    public function updateDistrictStatus(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->all();
            if ($data['status'] == "Active") {
                $status = 1;
            } else {
                $status = 0;
            }
            District::where('id', $data['district_id'])->update(['status' => $status]);
            return response()->json(['status' => $status, 'district_id' => $data['district_id']]);
        }
    }
}
