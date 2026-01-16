<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Country;
use App\Models\State;
use App\Models\District;
use App\Models\Block;
use App\Models\Coupon;
use App\Models\HeaderLogo;
use App\Models\Order;
use App\Models\Product;
use App\Models\Publisher;
use App\Models\SalesExecutive;
use App\Models\Section;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorsBankDetail;
use App\Models\VendorsBusinessDetail;
use App\Models\ContactUs;
use App\Models\ContactReply;
use App\Models\ProductsAttribute;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Intervention\Image\Facades\Image;

class AdminController extends Controller
{
    public function dashboard()
    {
        Session::put('page', 'dashboard');

        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();

        $admin = Auth::guard('admin')->user();
        $adminType = $admin->type;
        $vendorId = $admin->vendor_id;

        // Default (Admin counts)
        $vendorsCount = Vendor::count();
        $usersCount = User::count();
        $salesExecutivesCount = SalesExecutive::count();
        $productsCount = Product::where('status', 1)->count();
        $ordersCount = Order::count();
        $couponsCount = Coupon::where('status', 1)->count();

        // Vendor-specific counts
        if ($adminType === 'vendor' && $vendorId) {

            // Vendors should NOT see vendor count
            $vendorsCount = 1;

            // Products added by vendor
            $productsCount = ProductsAttribute::where('vendor_id', $vendorId)
                ->whereHas('product', function ($q) {
                    $q->where('status', 1);
                })
                ->distinct('product_id')
                ->count('product_id');

            // Orders containing vendor products
            $ordersCount = Order::whereHas('order_items', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })->count();

            // Coupons created by vendor (if applicable)
            $couponsCount = Coupon::where('vendor_id', $vendorId)
                ->where('status', 1)
                ->count();

            // Users & sales executives usually remain global
            $usersCount = User::count();
            $salesExecutivesCount = SalesExecutive::count();
        }

        // Vendor plan info
        $vendor = null;
        if ($adminType === 'vendor' && $vendorId) {
            $vendor = Vendor::find($vendorId);
        }

        return view('admin.dashboard', compact(
            'productsCount',
            'ordersCount',
            'couponsCount',
            'vendorsCount',
            'usersCount',
            'salesExecutivesCount',
            'logos',
            'headerLogo',
            'vendor'
        ));
    }

    public function login(Request $request)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        if ($request->isMethod('post')) {
            $data = $request->all();

            $rules = [
                'login'    => 'required|string|max:150',
                'password' => 'required',
            ];

            $customMessages = [
                'login.required'    => 'Email or mobile number is required!',
                'password.required' => 'Password is required!',
            ];

            $this->validate($request, $rules, $customMessages);


            $loginInput = trim($data['login']);
            $numericLogin = preg_replace('/\D/', '', $loginInput);
            $credentials = ['password' => $data['password']];

            if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
                $credentials['email'] = $loginInput;
            } elseif (strlen($numericLogin) >= 10 && strlen($numericLogin) <= 11) {
                $credentials['mobile'] = $numericLogin;
            } else {
                return redirect()->back()->withErrors(['login' => 'Enter a valid email or 10/11-digit mobile number.'])->withInput();
            }

            if (Auth::guard('admin')->attempt($credentials)) {
                if (Auth::guard('admin')->user()->type == 'vendor' && Auth::guard('admin')->user()->confirm == 'No') {
                    return redirect()->back()->with('error_message', 'Please confirm your email to activate your Vendor Account');
                } else if (Auth::guard('admin')->user()->type == 'vendor' && Auth::guard('admin')->user()->status == '0') {
                    return redirect()->back()->with('error_message', 'Your vendor account is not active');
                } else {
                    if (Auth::guard('admin')->user()->type === 'vendor') {
                        return redirect()->route('vendor.dashboard'); // which is /vendor/dashboard
                    }
                    return redirect('/admin/dashboard');
                }
            } else {
                return redirect()->back()->with('error_message', 'Invalid Email or Password');
            }
        }

        return view('admin/login', compact('logos', 'headerLogo'));
    }

    public function logout()
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        Auth::guard('admin')->logout();
        return view('admin/login', compact('logos', 'headerLogo'));
    }

    public function updateAdminPassword(Request $request)
    {

        Session::put('page', 'update_admin_password');
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();


        if ($request->isMethod('post')) {
            $data = $request->all();



            if (Hash::check($data['current_password'], Auth::guard('admin')->user()->password)) {
                if ($data['confirm_password'] == $data['new_password']) {
                    Admin::where('id', Auth::guard('admin')->user()->id)->update([
                        'password' => bcrypt($data['new_password']),
                    ]); // we persist (update) the hashed password (not the password itself)

                    return redirect()->back()->with('success_message', 'Admin Password has been updated successfully!');
                } else { // If new password and confirm password are not matching each other
                    return redirect()->back()->with('error_message', 'New Password and Confirm Password does not match!');
                }
            } else {
                return redirect()->back()->with('error_message', 'Your current admin password is Incorrect!');
            }
        }

        $adminDetails = Admin::where('email', Auth::guard('admin')->user()->email)->first()->toArray();

        return view('admin/settings/update_admin_password', compact('adminDetails', 'logos', 'headerLogo'));
    }

    public function checkAdminPassword(Request $request)
    {
        $data = $request->all();

        if (Hash::check($data['current_password'], Auth::guard('admin')->user()->password)) {
            return 'true';
        } else {
            return 'false';
        }
    }

    public function updateAdminDetails(Request $request)
    {
        Session::put('page', 'update_admin_details');
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        if ($request->isMethod('post')) {
            $data = $request->all();
            $rules = [
                'admin_name'   => 'required|regex:/^[\pL\s\-]+$/u',
                'admin_mobile' => 'required|numeric',
            ];

            $customMessages = [
                'admin_name.required'   => 'Name is required',
                'admin_name.regex'      => 'Valid Name is required',
                'admin_mobile.required' => 'Mobile is required',
                'admin_mobile.numeric'  => 'Valid Mobile is required',
            ];

            $this->validate($request, $rules, $customMessages);

            if ($request->hasFile('admin_image')) {
                $image_tmp = $request->file('admin_image');
                // dd($image_tmp);

                if ($image_tmp->isValid()) {
                    // Get the image extension
                    $extension = $image_tmp->getClientOriginalExtension();

                    // Generate a random name for the uploaded image (to avoid that the image might get overwritten if its name is repeated)
                    $imageName = rand(111, 99999) . '.' . $extension;

                    // Assigning the uploaded images path inside the 'public' folder
                    $imagePath = 'admin/images/photos/' . $imageName;

                    // Upload the image using the Intervention package and save it in our path inside the 'public' folder
                    Image::make($image_tmp)->save($imagePath); // '\Image' is the Intervention package
                }
            } else if (! empty($data['current_admin_image'])) { // In case the admins updates other fields but doesn't update the image itself (doesn't upload a new image), but there's an already existing old image
                $imageName = $data['current_admin_image'];
            } else { // In case the admins updates other fields but doesn't update the image itself (doesn't upload a new image), and originally there wasn't any image uploaded in the first place
                $imageName = '';
            }

            // Update Admin Details
            Admin::where('id', Auth::guard('admin')->user()->id)->update([ // Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances
                'name'   => $data['admin_name'],
                'mobile' => $data['admin_mobile'],
                'image'  => $imageName,
            ]); // Note that the image name is the random image name that we generated

            return redirect()->back()->with('success_message', 'Admin details updated successfully!');
        }

        return view('admin/settings/update_admin_details', compact('logos', 'headerLogo'));
    }

    public function updateVendorDetails($slug, Request $request)
    { // $slug can only be: 'personal', 'business' or 'bank'
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        if ($slug == 'personal') {
            // Correcting issues in the Skydash Admin Panel Sidebar using Session
            Session::put('page', 'update_personal_details');

            // Handling update vendor personal details <form> submission
            if ($request->isMethod('post')) { // if the <form> is submitted
                $data = $request->all();
                // dd($data);

                // Laravel's Validation    // Customizing Laravel's Validation Error Messages: https://laravel.com/docs/9.x/validation#customizing-the-error-messages    // Customizing Validation Rules: https://laravel.com/docs/9.x/validation#custom-validation-rules
                $rules = [
                    'vendor_name'   => 'required|regex:/^[\pL\s\-]+$/u', // only alphabetical characters and spaces
                    'vendor_mobile' => 'required|numeric',
                    'country_id'    => 'nullable|exists:countries,id',
                    'state_id'      => 'nullable|exists:states,id',
                    'district_id'   => 'nullable|exists:districts,id',
                    'block_id'      => 'nullable|exists:blocks,id',
                ];

                $customMessages = [ // Specifying A Custom Message For A Given Attribute: https://laravel.com/docs/9.x/validation#specifying-a-custom-message-for-a-given-attribute
                    'vendor_name.required'   => 'Name is required',
                    'vendor_name.regex'      => 'Valid Name is required',
                    'vendor_mobile.required' => 'Mobile is required',
                    'vendor_mobile.numeric'  => 'Valid Mobile is required',
                ];

                $this->validate($request, $rules, $customMessages);

                // Uploading Admin Photo

                // Using the Intervention package for uploading images
                if ($request->hasFile('vendor_image')) { // the HTML name attribute    name="admin_name"    in update_admin_details.blade.php
                    $image_tmp = $request->file('vendor_image');

                    if ($image_tmp->isValid()) {
                        // Get the image extension
                        $extension = $image_tmp->getClientOriginalExtension();

                        // Generate a random name for the uploaded image (to avoid that the image might get overwritten if its name is repeated)
                        $imageName = rand(111, 99999) . '.' . $extension;

                        // Assigning the uploaded images path inside the 'public' folder
                        $imagePath = 'admin/images/photos/' . $imageName;

                        // Upload the image using the Intervention package and save it in our path inside the 'public' folder
                        Image::make($image_tmp)->save($imagePath); // '\Image' is the Intervention package
                    }
                } else if (! empty($data['current_vendor_image'])) { // In case the admins updates other fields but doesn't update the image itself (doesn't upload a new image), but there's an already existing old image
                    $imageName = $data['current_vendor_image'];
                } else { // In case the admins updates other fields but doesn't update the image itself (doesn't upload a new image), and originally there wasn't any image uploaded in the first place
                    $imageName = '';
                }

                // Vendor details need to be updated in BOTH `admins` and `vendors` tables:
                // Update Vendor Details in 'admins' table
                Admin::where('id', Auth::guard('admin')->user()->id)->update([ // Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances
                    'name'   => $data['vendor_name'],
                    'mobile' => $data['vendor_mobile'],
                    'image'  => $imageName,
                ]); // Note that the image name is the random image name that we generated

                // Update Vendor Details in 'vendors' table
                Vendor::where('id', Auth::guard('admin')->user()->vendor_id)->update([ // Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances
                    'name'        => $data['vendor_name'],
                    'mobile'      => $data['vendor_mobile'],
                    'address'     => $data['vendor_address'] ?? null,
                    'country_id' => $data['country_id'] ?? null,
                    'state_id'    => $data['state_id'] ?? null,
                    'district_id' => $data['district_id'] ?? null,
                    'block_id'    => $data['block_id'] ?? null,
                    'pincode'     => $data['vendor_pincode'] ?? null,
                ]);

                return redirect()->back()->with('success_message', 'Vendor details updated successfully!');
            }

            $vendorDetails = Vendor::where('id', Auth::guard('admin')->user()->vendor_id)->first()->toArray(); // Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances

        } else if ($slug == 'business') {
            // Correcting issues in the Skydash Admin Panel Sidebar using Session
            Session::put('page', 'update_business_details');

            if ($request->isMethod('post')) { // if the <form> is submitted
                $data = $request->all();
                // dd($data);

                // Laravel's Validation    // Customizing Laravel's Validation Error Messages: https://laravel.com/docs/9.x/validation#customizing-the-error-messages    // Customizing Validation Rules: https://laravel.com/docs/9.x/validation#custom-validation-rules
                $rules = [
                    'shop_name'     => 'required|regex:/^[\pL\s\-]+$/u', // only alphabetical characters and spaces
                    'shop_city'     => 'required|regex:/^[\pL\s\-]+$/u', // only alphabetical characters and spaces
                    'shop_mobile'   => 'required|numeric',
                    'address_proof' => 'required',
                ];

                $customMessages = [ // Specifying A Custom Message For A Given Attribute: https://laravel.com/docs/9.x/validation#specifying-a-custom-message-for-a-given-attribute
                    'shop_name.required'   => 'Name is required',
                    'shop_city.required'   => 'City is required',
                    'shop_city.regex'      => 'Valid City alphabetical is required',
                    'shop_name.regex'      => 'Valid Shop Name is required',
                    'shop_mobile.required' => 'Mobile is required',
                    'shop_mobile.numeric'  => 'Valid Mobile is required',
                ];

                $this->validate($request, $rules, $customMessages);

                // Uploading Admin Photo    // Using the Intervention package for uploading images
                if ($request->hasFile('address_proof_image')) { // the HTML name attribute    name="admin_name"    in update_admin_details.blade.php
                    $image_tmp = $request->file('address_proof_image');

                    if ($image_tmp->isValid()) {
                        // Get the image extension
                        $extension = $image_tmp->getClientOriginalExtension();

                        // Generate a random name for the uploaded image (to avoid that the image might get overwritten if its name is repeated)
                        $imageName = rand(111, 99999) . '.' . $extension;

                        // Assigning the uploaded images path inside the 'public' folder
                        $imagePath = 'admin/images/proofs/' . $imageName;

                        // Upload the image using the Intervention package and save it in our path inside the 'public' folder
                        Image::make($image_tmp)->save($imagePath); // '\Image' is the Intervention package
                    }
                } else if (! empty($data['current_address_proof'])) { // In case the admins updates other fields but doesn't update the image itself (doesn't upload a new image), but there's an already existing old image
                    $imageName = $data['current_address_proof'];
                } else { // In case the admins updates other fields but doesn't update the image itself (doesn't upload a new image), and originally there wasn't any image uploaded in the first place
                    $imageName = '';
                }

                $vendorCount = VendorsBusinessDetail::where('vendor_id', Auth::guard('admin')->user()->vendor_id)->count(); // Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances
                if ($vendorCount > 0) {                                                                                     // if there's a vendor already existing, them UPDATE
                    // UPDATE `vendors_business_details` table
                    VendorsBusinessDetail::where('vendor_id', Auth::guard('admin')->user()->vendor_id)->update([                // Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances
                        'shop_name'               => $data['shop_name'],
                        'shop_mobile'             => $data['shop_mobile'],
                        'shop_website'            => $data['shop_website'],
                        'shop_address'            => $data['shop_address'],
                        'shop_city'               => $data['shop_city'],
                        'shop_state'              => $data['shop_state'],
                        'shop_country'            => $data['shop_country'],
                        'shop_pincode'            => $data['shop_pincode'],
                        'business_license_number' => $data['business_license_number'],
                        'gst_number'              => $data['gst_number'],
                        'pan_number'              => $data['pan_number'],
                        'address_proof'           => $data['address_proof'],
                        'address_proof_image'     => $imageName,
                    ]);
                } else { // if there's no vendor already existing, then INSERT
                    // INSERT INTO `vendors_business_details` table
                    VendorsBusinessDetail::insert([
                        'vendor_id'               => Auth::guard('admin')->user()->vendor_id, // Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances
                        'shop_name'               => $data['shop_name'],
                        'shop_mobile'             => $data['shop_mobile'],
                        'shop_website'            => $data['shop_website'],
                        'shop_address'            => $data['shop_address'],
                        'shop_city'               => $data['shop_city'],
                        'shop_state'              => $data['shop_state'],
                        'shop_country'            => $data['shop_country'],
                        'shop_pincode'            => $data['shop_pincode'],
                        'business_license_number' => $data['business_license_number'],
                        'gst_number'              => $data['gst_number'],
                        'pan_number'              => $data['pan_number'],
                        'address_proof'           => $data['address_proof'],
                        'address_proof_image'     => $imageName,
                    ]);
                }

                return redirect()->back()->with('success_message', 'Vendor details updated successfully!');
            }

            $vendorCount = VendorsBusinessDetail::where('vendor_id', Auth::guard('admin')->user()->vendor_id)->count(); // Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances

            if ($vendorCount > 0) {
                $vendorDetails = VendorsBusinessDetail::where('vendor_id', Auth::guard('admin')->user()->vendor_id)->first()->toArray(); // Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances
            } else {
                $vendorDetails = [];
            }
        } else if ($slug == 'bank') {
            // Correcting issues in the Skydash Admin Panel Sidebar using Session
            Session::put('page', 'update_bank_details');

            if ($request->isMethod('post')) { // if the <form> is submitted
                $data = $request->all();
                // dd($data);

                // Laravel's Validation    // Customizing Laravel's Validation Error Messages: https://laravel.com/docs/9.x/validation#customizing-the-error-messages    // Customizing Validation Rules: https://laravel.com/docs/9.x/validation#custom-validation-rules
                $rules = [
                    'account_holder_name' => 'required|regex:/^[\pL\s\-]+$/u', // only alphabetical characters and spaces
                    'bank_name'           => 'required',                       // only alphabetical characters and spaces
                    'account_number'      => 'required|numeric',
                    'bank_ifsc_code'      => 'required',
                ];

                $customMessages = [ // Specifying A Custom Message For A Given Attribute: https://laravel.com/docs/9.x/validation#specifying-a-custom-message-for-a-given-attribute
                    'account_holder_name.required' => 'Account Holder Name is required',
                    'bank_name.required'           => 'Bank Name is required',
                    'account_holder_name.regex'    => 'Valid Account Holder Name is required',
                    'account_number.required'      => 'Account Number is required',
                    'account_number.numeric'       => 'Valid Account Number is required',
                    'bank_ifsc_code.required'      => 'Bank IFSC Code is required',
                ];

                $this->validate($request, $rules, $customMessages);

                $vendorCount = VendorsBankDetail::where('vendor_id', Auth::guard('admin')->user()->vendor_id)->count(); // Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances
                if ($vendorCount > 0) {                                                                                 // if there's a vendor already existing, them UPDATE
                    // UPDATE `vendors_bank_details` table
                    VendorsBankDetail::where('vendor_id', Auth::guard('admin')->user()->vendor_id)->update([                // Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances
                        'account_holder_name' => $data['account_holder_name'],
                        'bank_name'           => $data['bank_name'],
                        'account_number'      => $data['account_number'],
                        'bank_ifsc_code'      => $data['bank_ifsc_code'],
                    ]);
                } else { // if there's no vendor already existing, then INSERT
                    // INSERT INTO `vendors_bank_details` table
                    VendorsBankDetail::insert([
                        'vendor_id'           => Auth::guard('admin')->user()->vendor_id, // Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances
                        'account_holder_name' => $data['account_holder_name'],
                        'bank_name'           => $data['bank_name'],
                        'account_number'      => $data['account_number'],
                        'bank_ifsc_code'      => $data['bank_ifsc_code'],
                    ]);
                }

                return redirect()->back()->with('success_message', 'Vendor details updated successfully!');
            }

            $vendorCount = VendorsBankDetail::where('vendor_id', Auth::guard('admin')->user()->vendor_id)->count();
            if ($vendorCount > 0) {
                $vendorDetails = VendorsBankDetail::where('vendor_id', Auth::guard('admin')->user()->vendor_id)->first()->toArray();
            } else {
                $vendorDetails = [];
            }
        }

        // Fetch all of the world countries from the database table `countries`
        $countries = Country::where('status', true)->get()->toArray(); // get the countries which have `status` = true (to ignore the blacklisted countries, in case)
        // dd($countries);

        // The 'GET' request: to show the update_vendor_details.blade.php page
        // We'll create one view (not 3) for the 3 pages, but parts inside it will change depending on the $slug value
        return view('admin/settings/update_vendor_details', compact('slug', 'vendorDetails', 'countries', 'logos', 'headerLogo'));
    }

    // AJAX methods for cascading location dropdowns (for vendor personal details)
    public function getVendorStates(Request $request)
    {
        $countryId = $request->input('country');

        $states = State::where('country_id', $countryId)
            ->where('status', true)
            ->pluck('name', 'id')
            ->toArray();

        return response()->json($states);
    }

    public function getVendorDistricts(Request $request)
    {
        $stateId = $request->input('state');

        $districts = District::where('state_id', $stateId)
            ->where('status', true)
            ->pluck('name', 'id')
            ->toArray();

        return response()->json($districts);
    }

    public function getVendorBlocks(Request $request)
    {
        $districtId = $request->input('district');

        $blocks = Block::where('district_id', $districtId)
            ->where('status', true)
            ->pluck('name', 'id')
            ->toArray();

        return response()->json($blocks);
    }

    // Update the vendor's commission percentage (by the Admin) in `vendors` table (for every vendor on their own) in the Admin Panel in admin/admins/view_vendor_details.blade.php (Commissions module: Every vendor must pay a certain commission (that may vary from a vendor to another) for the website owner (admin) on every item sold, and it's defined by the website owner (admin))
    public function updateVendorCommission(Request $request)
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        if ($request->isMethod('post')) { // if the HTML Form is submitted (in admin/admins/view_vendor_details.blade.php)
            $data = $request->all();
            // dd($data);

            // UPDATE the `vendors` table with the `commission` percentage requested by the admin from the vendor
            Vendor::where('id', $data['vendor_id'])->update(['commission' => $data['commission']]);

            return redirect()->back()->with('success_message', 'Vendor commission updated successfully!');
        }
    }

    public function admins($type = null)
    { // $type is the `type` column in the `admins` which can only be: superadmin, admin, subadmin or vendor    // A default value of null (to allow not passing a {type} slug, and in this case, the page will view ALL of the superadmin, admins, subadmins and vendors at the same time)
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $admins = Admin::query();

        if (! empty($type)) { // in this case, $type can be: superadmin, admin, subadmin or vendor
            // Map URL parameter to database type
            // If URL says 'admin', it might actually be 'superadmin' in the database
            $typeMapping = [
                'admin' => 'superadmin', // Map 'admin' URL to 'superadmin' database type
            ];

            // Use mapped type if exists, otherwise use the original type
            $dbType = isset($typeMapping[strtolower($type)]) ? $typeMapping[strtolower($type)] : $type;

            // Use case-insensitive comparison to handle any case variations
            $admins = $admins->whereRaw('LOWER(type) = ?', [strtolower($dbType)]);
            $title  = ucfirst($type);

            // Correcting issues in the Skydash Admin Panel Sidebar using Session
            Session::put('page', 'view_' . strtolower($title));
        } else { // if there's no $type is passed, show ALL of the admins, subadmins and vendors
            $title = 'All Admins/Vendors';

            // Correcting issues in the Skydash Admin Panel Sidebar using Session
            Session::put('page', 'view_all');
        }

        // Order by id descending to show newest first, then convert to array
        $admins = $admins->orderBy('id', 'desc')->get()->toArray(); // toArray() method converts the Collection object to a plain PHP array

        return view('admin/admins/admins', compact('admins', 'title', 'logos', 'headerLogo'));
    }

    public function viewVendorDetails($id)
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $vendorDetails = Admin::with('vendorPersonal', 'vendorBusiness', 'vendorBank')->where('id', $id)->first(); // Using the relationship defined in the Admin.php model to be able to get data from `vendors`, `vendors_business_details` and `vendors_bank_details` tables
        $vendorDetails = json_decode(json_encode($vendorDetails), true);                                           // We used json_decode(json_encode($variable), true) to convert $vendorDetails to an array instead of Laravel's toArray() method
        // dd($vendorDetails);

        // Fetch countries for dropdowns
        $countries = Country::where('status', true)->get()->toArray();

        // Get current location IDs from vendor personal details
        $currentCountryId = $vendorDetails['vendor_personal']['country_id'] ?? null;
        $currentStateId = $vendorDetails['vendor_personal']['state_id'] ?? null;
        $currentDistrictId = $vendorDetails['vendor_personal']['district_id'] ?? null;
        $currentBlockId = $vendorDetails['vendor_personal']['block_id'] ?? null;

        return view('admin/admins/view_vendor_details', compact('vendorDetails', 'logos', 'headerLogo', 'countries', 'currentCountryId', 'currentStateId', 'currentDistrictId', 'currentBlockId'));
    }

    public function updateAdminStatus(Request $request)
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        // Update Admin Status using AJAX in admins.blade.php
        if ($request->ajax()) {                               // if the request is coming via an AJAX call
            $data = $request->all();                              // Getting the name/value pairs array that are sent from the AJAX request (AJAX call)
            // dd($data);

            if ($data['status'] == 'Active') { // $data['status'] comes from the 'data' object inside the $.ajax() method    // reverse the 'status' from (ative/inactive) 0 to 1 and 1 to 0 (and vice versa)
                $status = 0;
            } else {
                $status = 1;
            }




            Admin::where('id', $data['admin_id'])->update(['status' => $status]); // $data['admin_id'] comes from the 'data' object inside the $.ajax() method
            // echo '<pre>', var_dump($data), '</pre>';

            // Send a THIRD Approval Email to the vendor when the superadmin or admin approves their account (`status` column in the `admins` table becomes 1 instead of 0) so that they can add their products on the website now
            $adminDetails = Admin::where('id', $data['admin_id'])->first()->toArray(); // get the admin that his `status` has been approved

            if ($adminDetails['type'] == 'vendor' && $status == 1) {                        // if the `type` column value (in `admins` table) is 'vendor', and their `status` became 1 (got approved), send them a THIRD confirmation mail
                Vendor::where('id', $adminDetails['vendor_id'])->update(['status' => $status]); // update the `status` of the vendor in `vendors` table

                // Note: Vendor CONFIRMATION occurs automatically through vendor clicking on the confirmation link sent in the email, but vendor ACTIVATION (active/inactive/disabled) occurs manually where 'superadmin' or 'admin' activates the `status` from the Admin Panel in 'Admin Management' tab, then clicks Status. Also, Vendor CONFIRMATION is related to the `confirm` columns in BOTH `admins` and `vendors` tables, but vendor ACTIVATION (active/inactive/disabled) is related to the `status` columns in BOTH `admins` and `vendors` tables!
                // Note: Vendor receives THREE emails: the first one when they register (please click on the confirmation link mail (in emails/vendor_confirmation.blade.php)), the second one when they click on the confirmation link sent in the first email (telling them that they have been confirmed and asking them to complete filling in their personal, business and bank details to get ACTIVATED/APPROVED (`status gets 1) (in emails/vendor_confirmed.blade.php)), the third email when the 'admin' or 'superadmin' manually activates (`status` becomes 1) the vendor from the Admin Panel from 'Admin Management' tab, then clicks Status (the email tells them they have been approved (activated and `status` became 1) and asks them to add their products on the website (in emails/vendor_approved.blade.php))

                // Send the Approval Success Email to the new vendor
                // $email = $adminDetails['email']; // the vendor's email

                // The email message data/variables that will be passed in to the email view
                // $messageData = [
                //     'email'  => $adminDetails['email'],
                //     'name'   => $adminDetails['name'],
                //     'mobile' => $adminDetails['mobile'],
                // ];

                // \Illuminate\Support\Facades\Mail::send('emails.vendor_approved', $messageData, function ($message) use ($email) { // Sending Mail: https://laravel.com/docs/9.x/mail#sending-mail    // 'emails.vendor_approved' is the vendor_approved.blade.php file inside the 'resources/views/emails' folder that will be sent as an email    // We pass in all the variables that vendor_approved.blade.php will use    // https://www.php.net/manual/en/functions.anonymous.php
                //     $message->to($email)->subject('Vendor Account is Approved');
                // });
            }

            $adminType = Auth::guard('admin')->user()->type; // `type` is the column in `admins` table    // Retrieving The Authenticated User and getting their `type`      column in `admins` table    // https://laravel.com/docs/9.x/authentication#retrieving-the-authenticated-user    // Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances

            return response()->json([ // JSON Responses: https://laravel.com/docs/9.x/responses#json-responses
                'status'   => $status,
                'admin_id' => $data['admin_id'],
            ]);
        }
    }

    public function headerLogo(Request $request)
    {

        Session::put('page', 'logo');
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();

        if ($request->isMethod('post')) {
            $data = $request->all();

            if ($request->hasFile('logo')) {
                $file     = $request->file('logo');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/logos/'), $filename);

                if ($logos) {
                    $logos->update(['logo' => $filename]);
                } else {
                    HeaderLogo::create(['logo' => $filename]);
                }
            }

            return back()->with('success_message', 'Logo updated successfully.');
        }

        return view('admin/settings/header_logo', compact('logos', 'headerLogo'));
    }

    public function favicon(Request $request)
    {
        Session::put('page', 'favicon');

        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        if ($request->isMethod('post')) {
            if ($request->hasFile('favicon')) {
                $file = $request->file('favicon');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('uploads/favicons/');

                if (! file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                $file->move($destinationPath, $filename);

                if ($logos) {
                    $logos->update(['favicon' => $filename]);
                } else {
                    $logos = HeaderLogo::create(['favicon' => $filename]);
                }
            }

            return back()->with('success_message', 'Favicon updated successfully.');
        }

        return view('admin/settings/favicon', compact('logos', 'headerLogo'));
    }

    public function updateFavicon(Request $request)
    {
        return $this->favicon($request);
    }

    public function addEditAdmin(Request $request, $id = null)
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        Session::put('page', 'admins');

        if ($request->isMethod('post')) {
            $data = $request->all();
            // dd($data);

            // Get the admin_id from the form if it exists, otherwise use route parameter
            $adminId = $data['admin_id'] ?? $id ?? null;

            // Get related vendor_id if this admin already exists (for unique email validation on vendors table)
            $vendorId = null;
            if (! empty($adminId)) {
                $vendorId = Admin::where('id', $adminId)->value('vendor_id');
            }

            // Laravel's Validation
            $rules = [
                'name'   => 'required|regex:/^[\pL\s\-]+$/u',
                'email'  => [
                    'required',
                    'email',
                    // Unique in admins table except for current admin (when updating)
                    Rule::unique('admins', 'email')->ignore($adminId),
                    // Also unique in vendors table except for linked vendor (when updating)
                    Rule::unique('vendors', 'email')->ignore($vendorId),
                ],
                'mobile' => 'required|numeric',

            ];

            $customMessages = [
                'name.required'   => 'Name is required',
                'name.regex'      => 'Valid Name is required',
                'email.required'  => 'Email is required',
                'email.email'     => 'Valid Email is required',
                'email.unique'    => 'Email already exists',
                'mobile.required' => 'Mobile is required',
                'mobile.numeric'  => 'Valid Mobile is required',
            ];

            if (empty($adminId)) {
                // Add mode: require password
                $rules['password'] = 'required|min:6|confirmed';
                $rules['password_confirmation'] = 'required';
                $customMessages['password.required'] = 'Password is required';
                $customMessages['password.min'] = 'Password must be at least 6 characters';
                $customMessages['password.confirmed'] = 'Password confirmation does not match';
            }

            $this->validate($request, $rules, $customMessages);

            // Uploading Admin Photo
            if ($request->hasFile('admin_image')) {
                $image_tmp = $request->file('admin_image');

                if ($image_tmp->isValid()) {
                    // Get the image extension
                    $extension = $image_tmp->getClientOriginalExtension();

                    // Generate a random name for the uploaded image
                    $imageName = rand(111, 99999) . '.' . $extension;

                    // Assigning the uploaded images path inside the 'public' folder
                    $imagePath = 'admin/images/photos/' . $imageName;

                    // Upload the image using the Intervention package
                    Image::make($image_tmp)->save($imagePath);
                }
            } else if (!empty($data['current_admin_image'])) {
                // Keep existing image
                $imageName = $data['current_admin_image'];
            } else {
                // No image
                $imageName = '';
            }

            // Prepare admin data (common for add & edit)
            $adminData = [
                'name'   => $data['name'],
                'email'  => $data['email'],
                'mobile' => $data['mobile'],
                'type'   => 'vendor',
                'image'  => $imageName,
            ];

            // Decide between Add vs Edit using resolved $adminId
            if (empty($adminId)) {
                // ADD mode

                // First, insert into vendors table
                $vendorData = [
                    'name'    => $data['name'],
                    'email'   => $data['email'],
                    'mobile'  => $data['mobile'],
                    'address' => null,
                    'city'    => null,
                    'state'   => null,
                    'country' => null,
                    'pincode' => null,
                    'confirm' => 'Yes',
                    'commission' => null,
                    'status'  => isset($data['status']) ? 1 : 0,
                ];

                $vendorId = Vendor::insertGetId($vendorData);

                // Then, insert into admins table with the vendor_id
                $adminData['vendor_id'] = $vendorId;
                $adminData['password'] = Hash::make($data['password']);
                $adminData['confirm'] = 'Yes';
                $adminData['status'] = isset($data['status']) ? 1 : 0;

                Admin::insert($adminData);

                return redirect('admin/admins')->with('success_message', 'Admin added successfully!');
            } else {
                // EDIT mode: update existing admin
                $admin = Admin::where('id', $adminId)->first();
                Admin::where('id', $adminId)->update($adminData);

                // If this admin is a vendor, also update the vendors table
                if ($admin && $admin->type == 'vendor' && !empty($admin->vendor_id)) {
                    $vendorUpdateData = [
                        'name'   => $data['name'],
                        'email'  => $data['email'],
                        'mobile' => $data['mobile'],
                    ];

                    // Update location fields if provided
                    if (isset($data['vendor_address'])) {
                        $vendorUpdateData['address'] = $data['vendor_address'];
                    }
                    if (isset($data['vendor_pincode'])) {
                        $vendorUpdateData['pincode'] = $data['vendor_pincode'];
                    }
                    if (isset($data['vendor_country_id'])) {
                        $vendorUpdateData['country_id'] = $data['vendor_country_id'];
                    }
                    if (isset($data['vendor_state_id'])) {
                        $vendorUpdateData['state_id'] = $data['vendor_state_id'];
                    }
                    if (isset($data['vendor_district_id'])) {
                        $vendorUpdateData['district_id'] = $data['vendor_district_id'];
                    }
                    if (isset($data['vendor_block_id'])) {
                        $vendorUpdateData['block_id'] = $data['vendor_block_id'];
                    }

                    Vendor::where('id', $admin->vendor_id)->update($vendorUpdateData);
                }

                return redirect('admin/admins')->with('success_message', 'Vendor updated successfully!');
            }
        }

        // GET request: show the form
        // Fetch all countries for dropdowns
        $countries = Country::where('status', true)->get()->toArray();

        if (!empty($id)) {
            // Edit mode: get admin data
            $admin = Admin::where('id', $id)->first()->toArray();

            // Get vendor details if admin is a vendor
            $vendorPersonal = [];
            $vendorBusiness = [];
            $vendorBank = [];

            if ($admin['type'] == 'vendor' && !empty($admin['vendor_id'])) {
                $vendorPersonal = Vendor::where('id', $admin['vendor_id'])->first();
                $vendorBusiness = VendorsBusinessDetail::where('vendor_id', $admin['vendor_id'])->first();
                $vendorBank = VendorsBankDetail::where('vendor_id', $admin['vendor_id'])->first();

                $vendorPersonal = $vendorPersonal ? $vendorPersonal->toArray() : [];
                $vendorBusiness = $vendorBusiness ? $vendorBusiness->toArray() : [];
                $vendorBank = $vendorBank ? $vendorBank->toArray() : [];
            }

            return view('admin/admins/edit', compact('admin', 'vendorPersonal', 'vendorBusiness', 'vendorBank', 'countries', 'logos', 'headerLogo'));
        } else {
            // Add mode
            return view('admin/admins/add', compact('logos', 'headerLogo'));
        }
    }

    public function deleteAdmin($id)
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        // Don't allow deleting the currently logged-in admin
        if ($id == Auth::guard('admin')->user()->id) {
            return redirect('admin/admins')->with('error_message', 'You cannot delete yourself!');
        }

        // Get admin details
        $admin = Admin::find($id);

        if (!$admin) {
            return redirect('admin/admins')->with('error_message', 'Admin not found!');
        }

        // Delete admin image if exists
        if (!empty($admin['image'])) {
            $imagePath = public_path('admin/images/photos/' . $admin['image']);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // Delete the admin
        Admin::where('id', $id)->delete();

        return redirect('admin/admins')->with('success_message', 'Admin deleted successfully!');
    }

    public function contactQueries()
    {
        Session::put('page', 'contact_queries');
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();

        $queries = ContactUs::with('replies')->orderBy('created_at', 'desc')->get()->toArray();

        return view('admin/contact_queries/index', compact('queries', 'logos', 'headerLogo'));
    }

    public function updateContactStatus(Request $request)
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        if ($request->ajax()) {
            $data = $request->all();

            ContactUs::where('id', $data['query_id'])->update(['status' => $data['status']]);

            return response()->json([
                'status' => $data['status'],
                'query_id' => $data['query_id'],
            ]);
        }
    }

    public function updateContactReply(Request $request, $id)
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        if ($request->isMethod('post')) {
            $data = $request->all();

            // Get current query status
            $currentQuery = ContactUs::where('id', $id)->first();
            $wasResolved = $currentQuery && $currentQuery->status == 'resolved';

            // If query is already resolved and admin is just changing status, admin_reply is optional
            $rules = [
                'status' => 'required|in:pending,resolved,in_progress',
            ];

            $customMessages = [
                'status.required' => 'Status is required',
            ];

            // Only require admin_reply if query is not already resolved or if status is being changed to resolved
            if (!$wasResolved || ($data['status'] == 'resolved' && !$wasResolved)) {
                $rules['admin_reply'] = 'required';
                $customMessages['admin_reply.required'] = 'Reply is required';
            }

            $this->validate($request, $rules, $customMessages);

            // Update main admin_reply field (for backward compatibility)
            // Only update admin_reply if it's provided
            $updateData = ['status' => $data['status']];
            if (!empty($data['admin_reply'])) {
                $updateData['admin_reply'] = $data['admin_reply'];
            }
            ContactUs::where('id', $id)->update($updateData);

            // Only create a new reply entry if admin is providing a new reply message
            if (!empty($data['admin_reply'])) {
                // Also save to replies table for conversation thread
                ContactReply::create([
                    'contact_us_id' => $id,
                    'reply_by' => 'admin',
                    'message' => $data['admin_reply'],
                ]);
            }

            if ($data['status'] == 'resolved') {
                return redirect('admin/contact-queries')->with('success_message', 'Query resolved successfully!');
            } else {
                return redirect('admin/contact-queries')->with('success_message', 'Reply updated successfully!');
            }
        }

        $query = ContactUs::with('replies')->where('id', $id)->first();
        $query = $query ? $query->toArray() : [];

        return view('admin/contact_queries/reply', compact('query', 'logos', 'headerLogo'));
    }

    public function deleteContactQuery($id)
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        ContactUs::where('id', $id)->delete();

        return redirect('admin/contact-queries')->with('success_message', 'Query deleted successfully!');
    }
}
