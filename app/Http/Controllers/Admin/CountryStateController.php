<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\State;
use App\Models\HeaderLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CountryStateController extends Controller
{
    public function index()
    {
        Session::put('page', 'countries_states');
        $countries = Country::with('states')->get();
        $states = State::with('country')->get();
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();

        return view('admin.countries_states.index', compact('countries', 'states', 'headerLogo', 'logos'));
    }

    public function addEditCountry(Request $request, $id = null)
    {
        if ($id == "") {
            $title = "Add Country";
            $country = new Country;
            $message = "Country added successfully!";
        } else {
            $title = "Edit Country";
            $country = Country::find($id);
            $message = "Country updated successfully!";
        }

        if ($request->isMethod('post')) {
            $data = $request->all();

            $rules = [
                'country_name' => 'required|string|max:255',
                'country_code' => 'required|string|max:10',
            ];
            $customMessages = [
                'country_name.required' => 'Country Name is required',
                'country_code.required' => 'Country Code is required',
            ];
            $this->validate($request, $rules, $customMessages);

            $country->name = $data['country_name'];
            $country->code = $data['country_code'];
            $country->status = 1;
            $country->save();

            return redirect('admin/countries-states')->with('success_message', $message);
        }

        return response()->json([
            'title' => $title,
            'country' => $country
        ]);
    }

    public function deleteCountry($id)
    {
        Country::where('id', $id)->delete();
        return redirect()->back()->with('success_message', 'Country deleted successfully!');
    }

    public function updateCountryStatus(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->all();
            if ($data['status'] == "Active") {
                $status = 1;
            } else {
                $status = 0;
            }
            Country::where('id', $data['country_id'])->update(['status' => $status]);
            return response()->json(['status' => $status, 'country_id' => $data['country_id']]);
        }
    }

    public function addEditState(Request $request, $id = null)
    {
        if ($id == "") {
            $title = "Add State";
            $state = new State;
            $message = "State added successfully!";
        } else {
            $title = "Edit State";
            $state = State::find($id);
            $message = "State updated successfully!";
        }

        if ($request->isMethod('post')) {
            $data = $request->all();

            $rules = [
                'country_id' => 'required',
                'state_name' => 'required|string|max:255',
                'state_code' => 'required|string|max:10',
            ];
            $customMessages = [
                'country_id.required' => 'Country is required',
                'state_name.required' => 'State Name is required',
                'state_code.required' => 'State Code is required',
            ];
            $this->validate($request, $rules, $customMessages);

            $state->country_id = $data['country_id'];
            $state->name = $data['state_name'];
            $state->code = $data['state_code'];
            $state->status = 1;
            $state->save();

            return redirect('admin/countries-states')->with('success_message', $message);
        }

        return response()->json([
            'title' => $title,
            'state' => $state
        ]);
    }

    public function deleteState($id)
    {
        State::where('id', $id)->delete();
        return redirect()->back()->with('success_message', 'State deleted successfully!');
    }

    public function updateStateStatus(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->all();
            if ($data['status'] == "Active") {
                $status = 1;
            } else {
                $status = 0;
            }
            State::where('id', $data['state_id'])->update(['status' => $status]);
            return response()->json(['status' => $status, 'state_id' => $data['state_id']]);
        }
    }
}
