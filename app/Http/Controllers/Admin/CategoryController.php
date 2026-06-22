<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Intervention\Image\Facades\Image;

use App\Models\Category;
use App\Models\HeaderLogo;
use App\Models\Section;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function categories(Request $request)
    {
        if (!Auth::guard('admin')->user()->can('view_categories')) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $data = Category::orderBy('id', 'desc')->with(['section', 'parentCategory']);
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('category_icon', function ($row) {
                    if (!empty($row->category_icon)) {
                        return '<img src="' . asset('admin/images/category_icons/' . $row->category_icon) . '" style="width: 50px; height: 50px;">';
                    }
                    return 'No Icon';
                })
                ->addColumn('parent_category', function ($row) {
                    return $row->section ? $row->section->name : 'N/A';
                })
                ->addColumn('status', function ($row) {
                    $adminType = Auth::guard('admin')->user()->type;
                    $statusIcon = $row->status == 1 ? 'mdi-bookmark-check' : 'mdi-bookmark-outline';
                    $statusText = $row->status == 1 ? 'Active' : 'Inactive';
                    
                    if ($adminType === 'vendor') {
                        return '<a class="updateCategoryStatus" id="category-' . $row->id . '"
                                    category_id="' . $row->id . '"
                                    data-url="' . route('vendor.updatecategorystatus') . '"
                                    href="javascript:void(0)">
                                    <i style="font-size: 25px" class="mdi mdi-bookmark-check"
                                        status="Active"></i>
                                </a>';
                    } else {
                        return '<a class="updateCategoryStatus" id="category-' . $row->id . '"
                                    category_id="' . $row->id . '"
                                    data-url="' . route('admin.updatecategorystatus') . '"
                                    href="javascript:void(0)">
                                    <i style="font-size: 25px" class="mdi ' . $statusIcon . '"
                                        status="' . $statusText . '"></i>
                                </a>';
                    }
                })
                ->addColumn('actions', function ($row) {
                    $adminType = Auth::guard('admin')->user()->type;
                    $prefix = $adminType === 'vendor' ? 'vendor' : 'admin';
                    
                    return '<a href="' . url($prefix . '/add-edit-category/' . $row->id) . '">
                                <i style="font-size: 25px" class="mdi mdi-pencil-box"></i>
                            </a>
                            <a href="javascript:void(0)" class="confirmDelete"
                                data-module="category"
                                data-url="' . url($prefix . '/delete-category/' . $row->id) . '">
                                <i style="font-size: 25px" class="mdi mdi-file-excel-box"></i>
                            </a>';
                })
                ->rawColumns(['category_icon', 'status', 'actions'])
                ->make(true);
        }

        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'categories');
        $adminType = Auth::guard('admin')->user()->type;
        return view('admin.categories.categories')->with(compact('logos', 'headerLogo', 'adminType'));
    }

    public function updateCategoryStatus(Request $request)
    { // Update Category Status using AJAX in categories.blade.php
        if (!Auth::guard('admin')->user()->can('edit_categories')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized action.'], 403);
        }

        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        if ($request->ajax()) { // if the request is coming via an AJAX call
            $data = $request->all(); // Getting the name/value pairs array that are sent from the AJAX request (AJAX call)
            // dd($data);

            if ($data['status'] == 'Active') { // $data['status'] comes from the 'data' object inside the $.ajax() method    // reverse the 'status' from (ative/inactive) 0 to 1 and 1 to 0 (and vice versa)
                $status = 0;
            } else {
                $status = 1;
            }


            Category::where('id', $data['category_id'])->update(['status' => $status]); // $data['category_id'] comes from the 'data' object inside the $.ajax() method in admin/js/custom.js
            // echo '<pre>', var_dump($data), '</pre>';

            return response()->json([ // JSON Responses: https://laravel.com/docs/9.x/responses#json-responses
                'status'      => $status,
                'category_id' => $data['category_id']
            ]);
        }
        return view('admin.categories.categories', compact('categories', 'logos', 'headerLogo'));
    }

    public function addEditCategory(Request $request, $id = null)
    { // If the $id is not passed, this means Add a Category, if not, this means Edit the Category
        // Correcting issues in the Skydash Admin Panel Sidebar using Session
        Session::put('page', 'categories');

        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        if ($id == '') { // if there's no $id is passed in the route/URL parameters, this means Add a new Category
            if (!Auth::guard('admin')->user()->can('add_categories')) {
                abort(403, 'Unauthorized action.');
            }
            $title = 'Add Category';
            $category = new Category();
            // dd($category);

            $getCategories = array(); // An array that contains all the parent categories that are under this education level

            $message = 'Category added successfully!';
        } else { // if the $id is passed in the route/URL parameters, this means Edit the Category
            if (!Auth::guard('admin')->user()->can('edit_categories')) {
                abort(403, 'Unauthorized action.');
            }
            $title = 'Edit Category';
            $category = Category::find($id);
            // dd($category->parentCategory);

            $getCategories = Category::with('subCategories')->where([ // $getCategories are all the parent categories, and their child categories
                // $getCategories is the parent categories (with no parents i.e. parent_id is 0 zero) but having the subCategories (the categories that they're parent to) at the same time
                'parent_id'  => 0, // parent_id is 0 zero BECAUSE IT'S A PARENT CATEGORY
                'section_id' => $category['section_id']
            ])->get();


            $message = 'Category updated successfully!';
        }


        if ($request->isMethod('post')) {
            $data = $request->all();

            $rules = [
                'category_name' => 'required|string|regex:/^[\pL\s\-\&\.]+$/u', // letters, spaces, hyphens, & and .
                'section_id'    => 'required',
            ];

            $customMessages = [ // Specifying A Custom Message For A Given Attribute: https://laravel.com/docs/9.x/validation#specifying-a-custom-message-for-a-given-attribute
                'category_name.required' => 'Category Name is required',
                'category_name.regex'    => 'Valid Category Name is required',
                'section_id.required'    => 'Education Level is required',
                'url.required'           => 'Category URL is required',
            ];

            $this->validate($request, $rules, $customMessages);


            // Uploading Category Icon
            if ($request->hasFile('category_icon')) {
                $icon_tmp = $request->file('category_icon');
                if ($icon_tmp->isValid()) {
                    // Delete old icon if exists
                    if (!empty($category->category_icon)) {
                        $oldIconPath = public_path('admin/images/category_icons/' . $category->category_icon);
                        if (file_exists($oldIconPath)) {
                            unlink($oldIconPath);
                        }
                    }
                    $extension = $icon_tmp->getClientOriginalExtension();
                    $iconName = time() . '_' . rand(111, 99999) . '.' . $extension;
                    $uploadPath = public_path('admin/images/category_icons/');
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }
                    $icon_tmp->move($uploadPath, $iconName);
                    $category->category_icon = $iconName;
                }
            }


            $category->section_id        = $data['section_id'];
            $category->parent_id         = 0;
            $category->category_name     = $data['category_name'];
            $category->description       = $data['description'];
            $category->url               = str_replace(' ', '-', strtolower($data['category_name']));
            $category->meta_title        = $data['meta_title'];
            $category->meta_description  = $data['meta_description'];
            $category->meta_keywords     = $data['meta_keywords'];
            $category->status            = 1;

            $category->save(); // Save all data in the database

            $adminType = Auth::guard('admin')->user()->type;
            $route = $adminType == 'vendor' ? 'vendor.categories' : 'admin.categories';
            return redirect()->route($route)->with('success_message', $message);
        }


        // Get all education levels
        $getSections = Section::get()->toArray();
        // dd($getSections);


        return view('admin.categories.add_edit_category')->with(compact('title', 'category', 'getSections', 'getCategories', 'logos', 'headerLogo'));
    }

    public function appendCategoryLevel(Request $request)
    { // (AJAX) Show Categories <select> <option> depending on the chosen Education Level (show the relevant categories of the chosen education level) using AJAX in admin/js/custom.js in append_categories_level.blade.php page
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        // Note: We created the <div> in a separate file in order for the appendCategoryLevel() method inside the CategoryController to be able to return the whole file as a response to the AJAX call in admin/js/custom.js to show the proper/relevant categories <select> box <option> depending on the selected (chosen) Education Level
        if ($request->ajax()) { // if the request is coming via an AJAX call
            // if ($request->isMethod('get')) {
            $data = $request->all();
            // dd($data);

            $getCategories = Category::with('subCategories')->where([ // 'subCategories' is the relationship method inside the Category.php model    // $getCategories are all the parent categories, and their child categories
                'parent_id'  => 0,
                'section_id' => $data['section_id'] // $data['section_id'] comes from the 'data' object inside the $.ajax() method in admin/js/custom.js
            ])->get();
            // }

            return view('admin.categories.append_categories_level')->with(compact('getCategories')); // return-ing the WHOLE append_categories_level.blade.php page
        }
        return view('admin.categories.append_categories_level', compact('getCategories', 'logos', 'headerLogo'));
    }

    public function deleteCategory($id)
    {
        if (!Auth::guard('admin')->user()->can('delete_categories')) {
            return redirect()->back()->with('error_message', 'Unauthorized action.');
        }

        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        Category::where('id', $id)->delete();

        $message = 'Category has been deleted successfully!';

        $adminType = Auth::guard('admin')->user()->type;
        $route = $adminType === 'vendor' ? 'vendor.categories' : 'admin.categories';

        return redirect()->route($route)->with('success_message', $message);
        return view('admin.categories.categories', compact('categories', 'logos', 'headerLogo'));
    }

    public function deleteCategoryImage($id)
    { // AJAX call from admin/js/custom.js    // Delete the category image from BOTH SERVER (FILESYSTEM) & DATABASE    // $id is passed as a Route Parameter
        if (!Auth::guard('admin')->user()->can('edit_categories')) {
            return redirect()->back()->with('error_message', 'Unauthorized action.');
        }

        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        // Category image record in the database
        $categoryImage = Category::select('category_image')->where('id', $id)->first();
        // dd($categoryImage);

        // Category image path on the server (filesystem)
        $category_image_path = 'front/images/category_images/';

        // Delete the category image on server (filesystem) (from the 'category_images' folder)
        if (file_exists($category_image_path . $categoryImage->category_image)) {
            unlink($category_image_path . $categoryImage->category_image);
        }

        // Delete the category image name from the `categories` database table (Note: We won't use delete() method because we're not deleting a complete record (entry) (we're just deleting a one column `category_image` value), we will just use update() method to update the `category_image` name to an empty string value '')
        Category::where('id', $id)->update(['category_image' => '']);

        $message = 'Category Image has been deleted successfully!';

        return redirect()->back()->with('success_message', $message);
        return view('admin.categories.categories', compact('categories', 'logos', 'headerLogo'));
    }
}
