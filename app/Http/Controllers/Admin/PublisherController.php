<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use App\Models\Publisher;
use App\Models\HeaderLogo;
use App\Exports\PublishersExport;
use App\Imports\PublishersImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class PublisherController extends Controller
{
    public function publisher(Request $request)
    {
        if (!Auth::guard('admin')->user()->can('view_publishers')) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $data = Publisher::orderBy('id', 'desc');
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    $adminType = Auth::guard('admin')->user()->type;
                    $statusIcon = $row->status == 1 ? 'mdi-bookmark-check' : 'mdi-bookmark-outline';
                    $statusText = $row->status == 1 ? 'Active' : 'Inactive';
                    
                    if ($adminType === 'vendor') {
                        return '<a class="updatePublisherStatus"
                                    id="publisher-' . $row->id . '"
                                    publisher_id="' . $row->id . '"
                                    data-url="' . route('vendor.updatepublisherstatus') . '"
                                    href="javascript:void(0)">
                                    <i style="font-size: 25px" class="mdi ' . $statusIcon . '"
                                        status="' . $statusText . '"></i>
                                </a>';
                    } else {
                        return '<a class="updatePublisherStatus"
                                    id="publisher-' . $row->id . '"
                                    publisher_id="' . $row->id . '"
                                    data-url="' . route('admin.updatepublisherstatus') . '"
                                    href="javascript:void(0)">
                                    <i style="font-size: 25px" class="mdi ' . $statusIcon . '"
                                        status="' . $statusText . '"></i>
                                </a>';
                    }
                })
                ->addColumn('actions', function ($row) {
                    $adminType = Auth::guard('admin')->user()->type;
                    $prefix = $adminType === 'vendor' ? 'vendor' : 'admin';
                    
                    $editUrl = url($prefix . '/add-edit-publisher/' . $row->id);
                    $deleteUrl = url($prefix . '/delete-publisher/' . $row->id);
                    
                    return '<a href="' . $editUrl . '">
                                <i style="font-size: 25px" class="mdi mdi-pencil-box"></i>
                            </a>
                            <a href="javascript:void(0)" class="confirmDelete"
                                data-module="publisher"
                                data-url="' . $deleteUrl . '">
                                <i style="font-size: 25px" class="mdi mdi-file-excel-box"></i>
                            </a>';
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }

        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        // Correcting issues in the Skydash Admin Panel Sidebar using Session
        Session::put('page', 'publisher');

        $adminType = Auth::guard('admin')->user()->type;
        return view('admin.publisher.publisher')->with(compact('logos', 'headerLogo', 'adminType'));
    }

    public function updatePublisherStatus(Request $request)
    { // Update publisher Status using AJAX in publisher.blade.php
        if (!Auth::guard('admin')->user()->can('edit_publishers')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized action.'], 403);
        }
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


    public function deletePublisher($id)
    {
        if (!Auth::guard('admin')->user()->can('delete_publishers')) {
            return redirect()->back()->with('error_message', 'Unauthorized action.');
        }
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Publisher::where('id', $id)->delete();

        $message = 'Publisher has been deleted successfully!';

        $adminType = Auth::guard('admin')->user()->type;
        $route = $adminType === 'vendor' ? 'vendor.publisher' : 'admin.publisher';
        return redirect()->route($route)->with('success_message', $message);
    }


    public function addPublisherAjax(Request $request)
    {
        if (!Auth::guard('admin')->user()->can('add_publishers')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized action.'], 403);
        }
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        if ($request->ajax()) {
            $request->validate([
                'name' => 'required|string|max:255|regex:/^[\pL\s\-]+$/u',
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



    public function addEditPublisher(Request $request, $id = null)
    { // If the $id is not passed, this means Add a publisher, if not, this means Edit the publisher
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        // Correcting issues in the Skydash Admin Panel Sidebar using Session
        Session::put('page', 'publisher');


        if ($id == '') { // if there's no $id is passed in the route/URL parameters, this means Add a new publisher
            if (!Auth::guard('admin')->user()->can('add_publishers')) {
                abort(403, 'Unauthorized action.');
            }
            $title = 'Add Publisher';
            $publisher = new Publisher();
            // dd($publisher);
            $message = 'publisher added successfully!';
        } else { // if the $id is passed in the route/URL parameters, this means Edit the publisher
            if (!Auth::guard('admin')->user()->can('edit_publishers')) {
                abort(403, 'Unauthorized action.');
            }
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


            $adminType = Auth::guard('admin')->user()->type;
            $route = $adminType === 'vendor' ? 'vendor.publisher' : 'admin.publisher';
            return redirect()->route($route)->with('success_message', $message);
        }


        return view('admin.publisher.add_edit_publisher')->with(compact('title', 'publisher', 'logos', 'headerLogo'));
    }

    public function exportPublishers()
    {
        return Excel::download(new PublishersExport, 'publishers.xlsx');
    }

    public function importPublishers(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'file' => 'required|mimes:xlsx,xls,csv'
            ]);

            try {
                Excel::import(new PublishersImport, $request->file('file'));
                return redirect()->back()->with('success_message', 'Publishers imported successfully!');
            } catch (\Exception $e) {
                return redirect()->back()->with('error_message', $e->getMessage());
            }
        }
    }
}
