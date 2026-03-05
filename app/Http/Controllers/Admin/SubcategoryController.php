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
    public function subcategories()
    {
        if (!Auth::guard('admin')->user()->can('view_categories')) {
            abort(403, 'Unauthorized action.');
        }

        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'subcategories');

        $subcategories = Subcategory::orderBy('id', 'desc')->get()->toArray();

        $adminType = Auth::guard('admin')->user()->type;
        return view('admin.subcategories.subcategories')->with(compact('subcategories', 'logos', 'headerLogo', 'adminType'));
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
                'subcategory_name' => 'required',
                'subcategory_icon' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            ];

            $this->validate($request, $rules);

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

            $redirectUrl = Auth::guard('admin')->user()->type == 'vendor' ? 'vendor/subcategories' : 'admin/subcategories';
            return redirect($redirectUrl)->with('success_message', $message);
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
        return redirect()->back()->with('success_message', 'Subcategory deleted successfully!');
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
