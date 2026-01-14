<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\Section;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Publisher;
use App\Models\Author;
use App\Models\Subject;
use App\Models\Language;
use App\Models\Edition;
use App\Models\Coupon;
use App\Models\BookRequest;

class CatalogueController extends Controller
{
    private function checkAccess($request)
    {
        $admin = $request->user();

        if (!$admin instanceof Admin) {
            return response()->json([
                'status' => false,
                'message' => 'Only Admin or Vendor can access this.'
            ], 403);
        }

        if (!in_array($admin->type, ['superadmin', 'vendor'])) {
            return response()->json([
                'status' => false,
                'message' => 'Only Admin or Vendor can access this.'
            ], 403);
        }

        if ($admin->status != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Your account is inactive.'
            ], 403);
        }

        return null;
    }

    public function getSection(Request $request)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $sections = Section::orderBy('name', 'asc')->get();

        return response()->json([
            'status' => true,
            'message' => 'Sections fetched successfully',
            'data' => $sections
        ]);
    }

    public function storeSection(Request $request)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $request->validate([
            'name' => 'required|string|max:255|unique:sections,name',
        ]);

        $section = Section::create([
            'name' => $request->name,
            'status' => '1',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Section created successfully',
            'data' => $section
        ]);
    }

    public function updateSection(Request $request, $id)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $section = Section::find($id);

        if (!$section) {
            return response()->json([
                'status' => false,
                'message' => 'Section not found'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:sections,name,' . $section->id,
        ]);

        $section->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Section updated successfully',
            'data' => $section
        ]);
    }

    public function destroySection(Request $request, $id)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $section = Section::find($id);

        if (!$section) {
            return response()->json([
                'status' => false,
                'message' => 'Section not found'
            ], 404);
        }

        $section->delete();

        return response()->json([
            'status' => true,
            'message' => 'Section deleted successfully'
        ]);
    }

    public function getCategory(Request $request)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $category = Category::orderBy('category_name', 'asc')->get();

        return response()->json([
            'status' => true,
            'message' => 'Category fetched successfully',
            'data' => $category
        ]);
    }

    public function storeCategory(Request $request)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $validator = Validator::make($request->all(), [
            'category_name'     => 'required|string|max:255|unique:categories,category_name',
            'section_id'        => 'required|exists:sections,id',
            'category_image'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'category_discount' => 'nullable|numeric|min:0|max:100',
            'description'       => 'nullable|string',
            'url'               => 'nullable|unique:categories,url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Image upload
        $imageName = null;
        if ($request->hasFile('category_image')) {
            $path = public_path('front/images/category_images');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            $imageName = time() . '_' . uniqid() . '.' . $request->category_image->extension();
            $request->category_image->move($path, $imageName);
        }

        $slug = $request->filled('url')
            ? Str::slug($request->url)                         // user-edited → text-book
            : strtolower(str_replace(' ', '', $request->category_name)); // auto → textbook

        $category = Category::create([
            'category_name'     => $request->category_name,
            'section_id'        => $request->section_id,
            'parent_id'         => 0,
            'category_image'    => $imageName,
            'category_discount' => $request->category_discount ?? 0,
            'description'       => $request->description,
            'url'               => $slug,
            'status'            => 1,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Category created successfully',
            'data' => $category
        ], 201);
    }

    public function updateCategory(Request $request, $id)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'category_name'     => 'required|string|max:255|unique:categories,category_name,' . $category->id,
            'section_id'        => 'required|exists:sections,id',
            'category_image'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'category_discount' => 'nullable|numeric|min:0|max:100',
            'description'       => 'nullable|string',
            'url'               => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->hasFile('category_image')) {
            $path = public_path('front/images/category_images');

            if ($category->category_image && File::exists($path . '/' . $category->category_image)) {
                File::delete($path . '/' . $category->category_image);
            }

            $imageName = time() . '_' . uniqid() . '.' . $request->category_image->extension();
            $request->category_image->move($path, $imageName);

            $category->category_image = $imageName;
        }

        $slug = $request->filled('url')
            ? Str::slug($request->url)
            : strtolower(str_replace(' ', '', $request->category_name));

        if (
            Category::where('url', $slug)
            ->where('id', '!=', $category->id)
            ->exists()
        ) {
            $slug .= '-' . time();
        }

        $category->update([
            'category_name'     => $request->category_name,
            'section_id'        => $request->section_id,
            'parent_id'         => 0,
            'category_discount' => $request->category_discount ?? 0,
            'description'       => $request->description,
            'url'               => $slug,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Category updated successfully',
            'data' => $category
        ]);
    }

    public function destroyCategory(Request $request, $id)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found'
            ], 404);
        }

        // Delete image
        $path = public_path('front/images/category_images');
        if ($category->category_image && File::exists($path . '/' . $category->category_image)) {
            File::delete($path . '/' . $category->category_image);
        }

        $category->delete();

        return response()->json([
            'status' => true,
            'message' => 'Category deleted successfully'
        ]);
    }

    public function getPublisher(Request $request)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $publisher = Publisher::orderBy('name', 'asc')->get();

        return response()->json([
            'status' => true,
            'message' => 'Publisher fetched successfully',
            'data' => $publisher
        ]);
    }

    public function storePublisher(Request $request)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $request->validate([
            'name' => 'required|string|max:255|unique:publishers,name',
        ]);

        $publisher = Publisher::create([
            'name' => $request->name,
            'status' => '1',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Publishers created successfully',
            'data' => $publisher
        ]);
    }

    public function updatePublisher(Request $request, $id)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $publisher = Publisher::find($id);

        if (!$publisher) {
            return response()->json([
                'status' => false,
                'message' => 'publisher not found'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:publishers,name,' . $publisher->id,
        ]);

        $publisher->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Publisher updated successfully',
            'data' => $publisher
        ]);
    }

    public function destroyPublisher(Request $request, $id)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $publisher = Publisher::find($id);

        if (!$publisher) {
            return response()->json([
                'status' => false,
                'message' => 'publisher not found'
            ], 404);
        }

        $publisher->delete();

        return response()->json([
            'status' => true,
            'message' => 'Publisher deleted successfully'
        ]);
    }

    public function getAuthor(Request $request)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $author = Author::orderBy('name', 'asc')->get();

        return response()->json([
            'status' => true,
            'message' => 'Author fetched successfully',
            'data' => $author
        ]);
    }

    public function storeAuthor(Request $request)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $request->validate([
            'name' => 'required|string|max:255|unique:authors,name',
        ]);

        $author = Author::create([
            'name' => $request->name,
            'status' => '1',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Author created successfully',
            'data' => $author
        ]);
    }

    public function updateAuthor(Request $request, $id)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $author = Author::find($id);

        if (!$author) {
            return response()->json([
                'status' => false,
                'message' => 'Author not found'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:authors,name,' . $author->id,
        ]);

        $author->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Author updated successfully',
            'data' => $author
        ]);
    }

    public function destroyAuthor(Request $request, $id)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $author = Author::find($id);

        if (!$author) {
            return response()->json([
                'status' => false,
                'message' => 'Author not found'
            ], 404);
        }

        $author->delete();

        return response()->json([
            'status' => true,
            'message' => 'Author deleted successfully'
        ]);
    }

    public function getSubject(Request $request)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $subject = Subject::orderBy('name', 'asc')->get();

        return response()->json([
            'status' => true,
            'message' => 'Subject fetched successfully',
            'data' => $subject
        ]);
    }

    public function storeSubject(Request $request)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name',
        ]);

        $subject = Subject::create([
            'name' => $request->name,
            'status' => '1',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Subject created successfully',
            'data' => $subject
        ]);
    }

    public function updateSubject(Request $request, $id)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $subject = Subject::find($id);

        if (!$subject) {
            return response()->json([
                'status' => false,
                'message' => 'Subject not found'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name,' . $subject->id,
        ]);

        $subject->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Subject updated successfully',
            'data' => $subject
        ]);
    }

    public function destroySubject(Request $request, $id)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $subject = Subject::find($id);

        if (!$subject) {
            return response()->json([
                'status' => false,
                'message' => 'Subject not found'
            ], 404);
        }

        $subject->delete();

        return response()->json([
            'status' => true,
            'message' => 'Subject deleted successfully'
        ]);
    }

    public function getLanguage(Request $request)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $language = Language::orderBy('name', 'asc')->get();

        return response()->json([
            'status' => true,
            'message' => 'Language fetched successfully',
            'data' => $language
        ]);
    }

    public function storeLanguage(Request $request)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $request->validate([
            'name' => 'required|string|max:255|unique:languages,name',
        ]);

        $language = Language::create([
            'name' => $request->name,
            'status' => '1',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Language created successfully',
            'data' => $language
        ]);
    }

    public function updateLanguage(Request $request, $id)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $language = Language::find($id);

        if (!$language) {
            return response()->json([
                'status' => false,
                'message' => 'Language not found'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:languages,name,' . $language->id,
        ]);

        $language->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Language updated successfully',
            'data' => $language
        ]);
    }

    public function destroyLanguage(Request $request, $id)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $language = Language::find($id);

        if (!$language) {
            return response()->json([
                'status' => false,
                'message' => 'Language not found'
            ], 404);
        }

        $language->delete();

        return response()->json([
            'status' => true,
            'message' => 'Language deleted successfully'
        ]);
    }

    public function getEdition(Request $request)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $edition = Edition::orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => true,
            'message' => 'Edition fetched successfully',
            'data' => $edition
        ]);
    }

    public function storeEdition(Request $request)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $request->validate([
            'edition' => 'required|string|max:255|unique:editions,edition',
        ]);

        $edition = Edition::create([
            'edition' => $request->edition,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Edition created successfully',
            'data' => $edition
        ]);
    }

    public function updateEdition(Request $request, $id)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $edition = Edition::find($id);

        if (!$edition) {
            return response()->json([
                'status' => false,
                'message' => 'Edition not found'
            ], 404);
        }

        $request->validate([
            'edition' => 'required|string|max:255|unique:editions,edition,' . $edition->id,
        ]);

        $edition->update([
            'edition' => $request->edition,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Edition updated successfully',
            'data' => $edition
        ]);
    }

    public function destroyEdition(Request $request, $id)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $edition = Edition::find($id);

        if (!$edition) {
            return response()->json([
                'status' => false,
                'message' => 'Edition not found'
            ], 404);
        }

        $edition->delete();

        return response()->json([
            'status' => true,
            'message' => 'Edition deleted successfully'
        ]);
    }

    public function getCoupon(Request $request)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $admin = $request->user();

        $coupons = ($admin->type === 'vendor')
            ? Coupon::where('vendor_id', $admin->vendor_id)->get()
            : Coupon::all();

        return response()->json([
            'status' => true,
            'data' => $coupons
        ]);
    }

    public function storeCoupon(Request $request)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $admin = $request->user();

        $validated = $request->validate([
            'categories'    => 'required|array|min:1',
            'coupon_option' => 'required|in:Automatic,Manual',
            'coupon_code'   => 'nullable|required_if:coupon_option,Manual|unique:coupons,coupon_code',
            'coupon_type'   => 'required|in:Single Time,Multiple Times',
            'amount_type'   => 'required|in:Fixed,Percentage',
            'amount'        => 'required|numeric|min:1',
            'expiry_date'   => 'nullable|date',
            'users'         => 'nullable|array'
        ]);

        if ($validated['coupon_option'] === 'Automatic') {
            do {
                $couponCode = Str::upper(Str::random(8));
            } while (Coupon::where('coupon_code', $couponCode)->exists());
        } else {
            $couponCode = $validated['coupon_code'];
        }

        $coupon = Coupon::create([
            'vendor_id'     => ($admin->type === 'vendor') ? $admin->vendor_id : 0,
            'coupon_option' => $validated['coupon_option'],
            'coupon_code'   => $couponCode,
            'categories'    => implode(',', $validated['categories']),
            'users'         => isset($validated['users']) ? implode(',', $validated['users']) : null,
            'coupon_type'   => $validated['coupon_type'],
            'amount_type'   => $validated['amount_type'],
            'amount'        => $validated['amount'],
            'expiry_date'   => $validated['expiry_date'],
            'status'        => 1,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Coupon created successfully',
            'data' => $coupon
        ], 201);
    }

    public function updateCoupon(Request $request, $id)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $admin = $request->user();
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return response()->json([
                'status' => false,
                'message' => 'Coupon not found'
            ], 404);
        }

        if ($admin->type === 'vendor' && $coupon->vendor_id !== $admin->vendor_id) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validated = $request->validate([
            'categories'  => 'required|array|min:1',
            'coupon_type' => 'required|in:Single Time,Multiple Times',
            'amount_type' => 'required|in:Fixed,Percentage',
            'amount'      => 'required|numeric|min:1',
            'expiry_date' => 'nullable|date',
            'users'       => 'nullable|array',
            'coupon_code' => [
                'required',
                Rule::unique('coupons', 'coupon_code')->ignore($coupon->id)
            ],
        ]);

        $coupon->update([
            'coupon_code' => $validated['coupon_code'],
            'categories'  => implode(',', $validated['categories']),
            'users'       => isset($validated['users']) ? implode(',', $validated['users']) : null,
            'coupon_type' => $validated['coupon_type'],
            'amount_type' => $validated['amount_type'],
            'amount'      => $validated['amount'],
            'expiry_date' => $validated['expiry_date'],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Coupon updated successfully',
            'data' => $coupon
        ]);
    }

    public function updateCouponStatus(Request $request, $id)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $admin = $request->user();
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return response()->json([
                'status' => false,
                'message' => 'Coupon not found'
            ], 404);
        }

        if ($admin->type === 'vendor' && $coupon->vendor_id !== $admin->vendor_id) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $coupon->update(['status' => !$coupon->status]);

        return response()->json([
            'status' => true,
            'message' => 'Coupon status updated',
            'coupon_status' => $coupon->status
        ]);
    }

    public function destroyCoupon(Request $request, $id)
    {
        if ($resp = $this->checkAccess($request)) return $resp;

        $admin = $request->user();
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return response()->json([
                'status' => false,
                'message' => 'Coupon not found'
            ], 404);
        }

        if ($admin->type === 'vendor' && $coupon->vendor_id !== $admin->vendor_id) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $coupon->delete();

        return response()->json([
            'status' => true,
            'message' => 'Coupon deleted successfully'
        ]);
    }

    public function getBookRequest(Request $request)
    {
        if ($resp = $this->checkAccess($request)) {
            return $resp;
        }

        $bookRequests = BookRequest::with('user')->latest()->get();

        return response()->json([
            'status' => true,
            'data'   => $bookRequests
        ], 200);
    }

    public function deleteBookRequest(Request $request, $id)
    {
        if ($resp = $this->checkAccess($request)) {
            return $resp;
        }

        $bookRequest = BookRequest::find($id);

        if (!$bookRequest) {
            return response()->json([
                'status' => false,
                'message' => 'Book Request not found'
            ], 404);
        }

        $bookRequest->delete();

        return response()->json([
            'status' => true,
            'message' => 'Book Request deleted successfully'
        ], 200);
    }

    public function updateBookRequestStatus(Request $request, $id)
    {
        if ($resp = $this->checkAccess($request)) {
            return $resp;
        }

        $admin = $request->user();
        $bookRequest = BookRequest::find($id);

        if (!$bookRequest) {
            return response()->json([
                'status' => false,
                'message' => 'Book request not found'
            ], 404);
        }

        // Optional: Vendor ownership check (if book_requests has vendor_id)
        if ($admin->type === 'vendor' && isset($bookRequest->vendor_id)) {
            if ($bookRequest->vendor_id !== $admin->vendor_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
        }

        $bookRequest->update([
            'status' => !$bookRequest->status
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Book request status updated',
            'book_request_status' => $bookRequest->status
        ], 200);
    }
}
