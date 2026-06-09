<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Subcategory;
use App\Models\Category;
use App\Models\Section;
use App\Models\HeaderLogo;
use Illuminate\Support\Facades\Auth;

class SubcategoryController extends Controller
{
    public function subcategories(Request $request)
    {
        if (!Auth::guard('admin')->user()->can('view_categories')) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $data = Subcategory::orderBy('id', 'desc');
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('subcategory_icon', function ($row) {
                    if (!empty($row->subcategory_icon)) {
                        return '<img src="' . asset('admin/images/subcategory_icons/' . $row->subcategory_icon) . '" style="width: 50px; height: 50px;">';
                    }
                    return 'No Icon';
                })
                ->addColumn('status', function ($row) {
                    $adminType = Auth::guard('admin')->user()->type;
                    $prefix = $adminType === 'vendor' ? 'vendor' : 'admin';
                    $statusIcon = $row->status == 1 ? 'mdi-bookmark-check' : 'mdi-bookmark-outline';
                    $statusText = $row->status == 1 ? 'Active' : 'Inactive';
                    
                    return '<a class="updateSubcategoryStatus" id="subcategory-' . $row->id . '"
                                subcategory_id="' . $row->id . '"
                                data-url="' . route($prefix . '.updatesubcategorystatus') . '"
                                href="javascript:void(0)">
                                <i style="font-size: 25px" class="mdi ' . $statusIcon . '"
                                    status="' . $statusText . '"></i>
                            </a>';
                })
                ->addColumn('actions', function ($row) {
                    $adminType = Auth::guard('admin')->user()->type;
                    $prefix = $adminType === 'vendor' ? 'vendor' : 'admin';
                    
                    return '<a href="' . url($prefix . '/add-edit-subcategory/' . $row->id) . '">
                                <i style="font-size: 25px" class="mdi mdi-pencil-box"></i>
                            </a>
                            <a href="javascript:void(0)" class="confirmDelete" data-module="Class"
                                data-url="' . url($prefix . '/delete-subcategory/' . $row->id) . '">
                                <i style="font-size: 25px" class="mdi mdi-file-excel-box"></i>
                            </a>';
                })
                ->rawColumns(['subcategory_icon', 'status', 'actions'])
                ->make(true);
        }

        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'subcategories');

        $adminType = Auth::guard('admin')->user()->type;
        return view('admin.subcategories.subcategories')->with(compact('logos', 'headerLogo', 'adminType'));
    }

    public function updateSubcategoryStatus(Request $request)
    {
        if (!Auth::guard('admin')->user()->can('edit_categories')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized action.'], 403);
        }

        if ($request->ajax()) {
            $data = $request->all();
            if ($data['status'] == 'Active') {
                $status = 0;
            } else {
                $status = 1;
            }

            Subcategory::where('id', $data['subcategory_id'])->update(['status' => $status]);

            return response()->json([
                'status' => $status,
                'subcategory_id' => $data['subcategory_id']
            ]);
        }
    }

    public function addEditSubcategory(Request $request, $id = null)
    {
        Session::put('page', 'subcategories');
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();

        if ($id == "") {
            if (!Auth::guard('admin')->user()->can('add_categories')) {
                abort(403, 'Unauthorized action.');
            }
            $title = "Add Subcategory";
            $subcategory = new Subcategory();
            $message = "Subcategory added successfully!";
        } else {
            if (!Auth::guard('admin')->user()->can('edit_categories')) {
                abort(403, 'Unauthorized action.');
            }
            $title = "Edit Subcategory";
            $subcategory = Subcategory::find($id);
            $message = "Subcategory updated successfully!";
        }

        if ($request->isMethod('post')) {
            $data = $request->all();

            $rules = [
                'subcategory_name' => 'required|unique:subcategories,subcategory_name' . ($id ? ',' . $id : ''),
                'subcategory_icon' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            ];

            $messages = [
                'subcategory_name.required' => 'Class Name is required.',
                'subcategory_name.unique' => 'This Class Name already exists.',
            ];

            $this->validate($request, $rules, $messages);

            // Uploading Subcategory Icon
            if ($request->hasFile('subcategory_icon')) {
                $icon_tmp = $request->file('subcategory_icon');
                if ($icon_tmp->isValid()) {
                    // Delete old icon if exists
                    if (!empty($subcategory->subcategory_icon)) {
                        $oldIconPath = public_path('admin/images/subcategory_icons/' . $subcategory->subcategory_icon);
                        if (file_exists($oldIconPath)) {
                            unlink($oldIconPath);
                        }
                    }
                    $extension = $icon_tmp->getClientOriginalExtension();
                    $iconName = time() . '_' . rand(111, 99999) . '.' . $extension;
                    $uploadPath = public_path('admin/images/subcategory_icons/');
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }
                    $icon_tmp->move($uploadPath, $iconName);
                    $subcategory->subcategory_icon = $iconName;
                }
            }

            $subcategory->subcategory_name = $data['subcategory_name'];
            $subcategory->status = 1;
            $subcategory->save();

            $adminType = Auth::guard('admin')->user()->type;
            $route = $adminType == 'vendor' ? 'vendor.subcategories' : 'admin.subcategories';
            return redirect()->route($route)->with('success_message', $message);
        }

        $categories = Category::with('section')->where('status', 1)->get()->toArray();

        return view('admin.subcategories.add_edit_subcategory')->with(compact('title', 'subcategory', 'logos', 'headerLogo'));
    }

    public function deleteSubcategory($id)
    {
        if (!Auth::guard('admin')->user()->can('delete_categories')) {
            return redirect()->back()->with('error_message', 'Unauthorized action.');
        }

        Subcategory::where('id', $id)->delete();

        $adminType = Auth::guard('admin')->user()->type;
        $route = $adminType == 'vendor' ? 'vendor.subcategories' : 'admin.subcategories';

        return redirect()->route($route)->with('success_message', 'Subcategory deleted successfully!');
    }

    public function appendSubcategoryLevel(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->all();
            $selected_id = $data['selected_id'] ?? null;
            // Subcategories are independent (no category_id in DB), 
            // but we fetch them all and pre-select the one requested
            $getSubcategories = Subcategory::where('status', 1)
                ->when($selected_id, function ($q) use ($selected_id) {
                    return $q->orWhere('id', $selected_id);
                })
                ->get()->toArray();
            return view('admin.subcategories.append_subcategories_level')->with(compact('getSubcategories', 'selected_id'));
        }
    }
}
