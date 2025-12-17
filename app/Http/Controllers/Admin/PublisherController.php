<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use App\Models\Publisher;
use App\Models\HeaderLogo;


class PublisherController extends Controller
{
    public function publisher() {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        // Correcting issues in the Skydash Admin Panel Sidebar using Session
        Session::put('page', 'publisher');


        $publishers = Publisher::orderBy('id', 'desc')->get()->toArray();
 // Plain PHP array
        // dd($publisher);

        return view('admin.publisher.publisher')->with(compact('publishers', 'logos', 'headerLogo'));
    }

    public function updatePublisherStatus(Request $request) { // Update publisher Status using AJAX in publisher.blade.php
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        if ($request->ajax()) { // if the request is coming via an AJAX call
            $data = $request->all(); // Getting the name/value pairs array that are sent from the AJAX request (AJAX call)
            // dd($data);

            if ($data['status'] == 'Active') { // $data['status'] comes from the 'data' object inside the $.ajax() method    // reverse the 'status' from (ative/inactive) 0 to 1 and 1 to 0 (and vice versa)
                $status = 0;
            } else {
                $status = 1;
            }


            Publisher::where('id', $data['publisher_id'])->update(['status' => $status]); // $data['publisher_id'] comes from the 'data' object inside the $.ajax() method
            // echo '<pre>', var_dump($data), '</pre>';

            return response()->json([ // JSON Responses: https://laravel.com/docs/9.x/responses#json-responses
                'status'   => $status,
                'publisher_id' => $data['publisher_id']
            ]);
        }
        return view('admin.publisher.publisher', compact('publishers', 'logos', 'headerLogo'));
    }


    public function deletePublisher($id) {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Publisher::where('id', $id)->delete();

        $message = 'Publisher has been deleted successfully!';

        return redirect()->back()->with('success_message', $message);
        return view('admin.publisher.publisher', compact('publishers', 'logos', 'headerLogo'));
        }


    public function addPublisherAjax(Request $request)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        if ($request->ajax()) {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            // Check if already exists
            $existing = Publisher::where('name', $request->name)->first();
            if ($existing) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Publisher already exists.'
                ]);
            }

            $publisher = new Publisher();
            $publisher->name = $request->name;
            $publisher->status = 1; // Or default status
            $publisher->save();

            return response()->json([
                'status' => 'success',
                'id' => $publisher->id,
                'name' => $publisher->name
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid request.'
        ]);
        return view('admin.publisher.publisher', compact('publishers', 'logos', 'headerLogo'));
    }



    public function addEditPublisher(Request $request, $id = null) { // If the $id is not passed, this means Add a publisher, if not, this means Edit the publisher
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        // Correcting issues in the Skydash Admin Panel Sidebar using Session
        Session::put('page', 'publisher');


        if ($id == '') { // if there's no $id is passed in the route/URL parameters, this means Add a new publisher
            $title = 'Add Publisher';
            $publisher = new Publisher();
            // dd($publisher);
            $message = 'publisher added successfully!';
        } else { // if the $id is passed in the route/URL parameters, this means Edit the publisher
            $title = 'Edit Publisher';
            $publisher = Publisher::find($id);
            // dd($publisher);
            $message = 'Publisher updated successfully!';
        }


        if ($request->isMethod('post')) { // WHETHER Add or Update <form> submission!!
            $data = $request->all();
            // dd($data);

            // Laravel's Validation    // Customizing Laravel's Validation Error Messages: https://laravel.com/docs/9.x/validation#customizing-the-error-messages    // Customizing Validation Rules: https://laravel.com/docs/9.x/validation#custom-validation-rules
            $rules = [
                'publisher_name' => 'required|regex:/^[\pL\s\-]+$/u', // only alphabetical characters and spaces
            ];

            $customMessages = [ // Specifying A Custom Message For A Given Attribute: https://laravel.com/docs/9.x/validation#specifying-a-custom-message-for-a-given-attribute
                'publisher_name.required' => 'Publisher Name is required',
                'publisher_name.regex'    => 'Valid Publisher Name is required',
            ];

            $this->validate($request, $rules, $customMessages);


            // Saving inserted/updated data
            $publisher->name   = $data['publisher_name']; // WHETHER ADDING or UPDATING
            $publisher->status = 1;  // WHETHER ADDING or UPDATING
            $publisher->save(); // Save all data in the database


            return redirect('admin/publisher')->with('success_message', $message, 'logos');
            return view('admin.publisher.publisher', compact('publishers', 'logos', 'headerLogo'));
        }


        return view('admin.publisher.add_edit_publisher')->with(compact('title', 'publisher', 'logos', 'headerLogo'));
    }
}
