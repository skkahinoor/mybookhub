<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\BookType;

class BookTypeController extends Controller
{
    // ================= DISPLAY TYPES =================
    public function types(Request $request)
    {
        if (!Auth::guard('admin')->user()->can('view_categories')) {
            abort(403, 'Unauthorized action.');
        }

        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();

        Session::put('page', 'types');

        if ($request->ajax()) {
            $query = BookType::query();

            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('icon', function ($row) {
                    if (!empty($row->book_type_icon)) {
                        $url = asset('admin/images/bookType/' . $row->book_type_icon);
                        return '<img src="' . $url . '" width="60" height="60" style="object-fit:cover;border-radius:6px;border:1px solid #ddd;">';
                    } else {
                        $url = asset('admin/images/no-image.png');
                        return '<img src="' . $url . '" width="60" height="60">';
                    }
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        return '<a class="updateTypeStatus" id="type-' . $row->id . '" type_id="' . $row->id . '" href="javascript:void(0)">
                                    <i style="font-size:25px" class="mdi mdi-bookmark-check" status="Active"></i>
                                </a>';
                    } else {
                        return '<a class="updateTypeStatus" id="type-' . $row->id . '" type_id="' . $row->id . '" href="javascript:void(0)">
                                    <i style="font-size:25px" class="mdi mdi-bookmark-outline" status="Inactive"></i>
                                </a>';
                    }
                })
                ->addColumn('actions', function ($row) {
                    $editUrl = url('admin/add-edit-type/' . $row->id);
                    $deleteUrl = url('admin/delete-type/' . $row->id);
                    return '<a href="' . $editUrl . '"><i style="font-size:25px" class="mdi mdi-pencil-box"></i></a>
                            <a href="' . $deleteUrl . '" onclick="return confirm(\'Delete this type?\')"><i style="font-size:25px;color:red" class="mdi mdi-delete"></i></a>';
                })
                ->rawColumns(['icon', 'status', 'actions'])
                ->make(true);
        }

        $adminType = Auth::guard('admin')->user()->type;

        return view('admin.type.type')
            ->with(compact('logos', 'headerLogo', 'adminType'));
    }


    // ================= UPDATE STATUS (AJAX) =================
    public function updateTypeStatus(Request $request)
    {
        if (!Auth::guard('admin')->user()->can('edit_categories')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action.'
            ], 403);
        }

        if ($request->ajax()) {

            $data = $request->all();

            if ($data['status'] == 'Active') {
                $status = 0;
            } else {
                $status = 1;
            }

            BookType::where('id', $data['type_id'])
                ->update(['status' => $status]);

            return response()->json([
                'status'  => $status,
                'type_id' => $data['type_id']
            ]);
        }
    }


    // ================= DELETE TYPE =================
    public function deleteType($id)
    {
        if (!Auth::guard('admin')->user()->can('delete_categories')) {
            return redirect()->back()
                ->with('error_message', 'Unauthorized action.');
        }

        $type = BookType::findOrFail($id);

        // Delete Image
        if (!empty($type->book_type_icon)) {

            $imagePath = public_path('admin/images/bookType/' . $type->book_type_icon);

            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $type->delete();

        return redirect()->back()
            ->with('success_message', 'Book Type has been deleted successfully!');
    }


    // ================= ADD / EDIT TYPE =================
    public function addEditType(Request $request, $id = null)
    {
        Session::put('page', 'types');

        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();

        if ($id == "") {

            if (!Auth::guard('admin')->user()->can('add_categories')) {
                abort(403, 'Unauthorized action.');
            }

            $title = "Add Book Type";
            $type = new BookType();
            $message = "Book Type added successfully!";
        }

        else {

            if (!Auth::guard('admin')->user()->can('edit_categories')) {
                abort(403, 'Unauthorized action.');
            }

            $title = "Edit Book Type";
            $type = BookType::findOrFail($id);
            $message = "Book Type updated successfully!";
        }

        if ($request->isMethod('post')) {

            $rules = [
                'book_type'      => 'required|string|max:255',
                'book_type_icon' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ];

            $customMessages = [
                'book_type.required' => 'Book Type is required',
                'book_type_icon.image' => 'File must be an image',
                'book_type_icon.mimes' => 'Only jpg, jpeg, png, webp allowed',
                'book_type_icon.max'   => 'Image size must be less than 2MB',
            ];

            $this->validate($request, $rules, $customMessages);

            $type->book_type = $request->book_type;
            $type->status = 1;

            // Upload Image
            if ($request->hasFile('book_type_icon')) {

                $image_tmp = $request->file('book_type_icon');

                if ($image_tmp->isValid()) {

                    // Delete old image
                    if (!empty($type->book_type_icon)) {

                        $oldImagePath = public_path('admin/images/bookType/' . $type->book_type_icon);

                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }

                    $extension = $image_tmp->getClientOriginalExtension();
                    $imageName = time() . '_' . rand(111, 99999) . '.' . $extension;

                    $uploadPath = public_path('admin/images/bookType/');

                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }

                    $image_tmp->move($uploadPath, $imageName);

                    $type->book_type_icon = $imageName;
                }
            }

            $type->save();

            return redirect('admin/types')
                ->with('success_message', $message);
        }

        return view('admin.type.add_edit_type')
            ->with(compact('title', 'type', 'logos', 'headerLogo'));
    }
}
