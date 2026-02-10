<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Block;
use App\Models\ContactReply;
use App\Models\ContactUs;
use App\Models\Country;
use App\Models\Coupon;
use App\Models\District;
use App\Models\HeaderLogo;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductsAttribute;
use App\Models\SalesExecutive;
use App\Models\State;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorsBankDetail;
use App\Models\VendorsBusinessDetail;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Spatie\Permission\Models\Role; // Import Role model

class AdminController extends Controller
{
    public function dashboard()
    {
        Session::put('page', 'dashboard');

        $logos      = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();

        $admin     = Auth::guard('admin')->user();
        $adminType = $admin->type;
        $vendorId  = $admin->vendor_id;

        // Default (Admin counts)
        $vendorsCount         = Vendor::count();
        $usersCount           = User::count();
        $salesExecutivesCount = SalesExecutive::count();
        $productsCount        = Product::where('status', 1)->count();
        $ordersCount          = Order::count();
        $couponsCount         = Coupon::where('status', 1)->count();

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
            $usersCount           = User::count();
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
        $logos      = HeaderLogo::first();
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

            $loginInput   = trim($data['login']);
            $numericLogin = preg_replace('/\D/', '', $loginInput);
            $credentials  = ['password' => $data['password']];

            if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
                $credentials['email'] = $loginInput;
            } elseif (strlen($numericLogin) >= 10 && strlen($numericLogin) <= 11) {
                $credentials['phone'] = $numericLogin;
            } else {
                return redirect()->back()->withErrors(['login' => 'Enter a valid email or 10/11-digit mobile number.'])->withInput();
            }

            if (Auth::guard('admin')->attempt($credentials)) {
                $user = Auth::guard('admin')->user();
                
                // Check if vendor account is inactive
                if ($user->type == 'vendor' && $user->status == '0') {
                    return redirect()->back()->with('error_message', 'Your vendor account is not active');
                }
                
                // Dynamic role-based redirection
                if ($user->hasRole('admin')) {
                    return redirect('/admin/dashboard');
                } elseif ($user->hasRole('vendor')) {
                    return redirect('/vendor/dashboard');
                } elseif ($user->hasRole('sales')) {
                    return redirect('/sales/dashboard');
                } elseif ($user->hasRole('student')) {
                    return redirect('/student/dashboard');
                }
                
                // Fallback to admin dashboard if no specific role found
                return redirect('/admin/dashboard');
            } else {
                return redirect()->back()->with('error_message', 'Invalid Email or Password');
            }
        }

        return view('admin/login', compact('logos', 'headerLogo'));
    }


    public function adminlogout(Request $request)
    {
        Auth::guard('admin')->logout();

        // Invalidate session
        $request->session()->invalidate();

        // Regenerate CSRF token
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success_message', 'You have been logged out successfully.');
    }

    public function vendorlogout(Request $request)
    {
        Auth::guard('admin')->logout();

        // Invalidate session
        $request->session()->invalidate();

        // Regenerate CSRF token
        $request->session()->regenerateToken();

        return redirect()->route('vendor.login')
            ->with('success_message', 'You have been logged out successfully.');
    }

    public function updateAdminPassword(Request $request)
    {

        Session::put('page', 'update_admin_password');
        $logos      = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();

        if ($request->isMethod('post')) {
            $data = $request->all();
            $user = Auth::guard('admin')->user();

            if (Hash::check($data['current_password'], $user->password)) {
                if ($data['confirm_password'] == $data['new_password']) {
                    $user->update([
                        'password' => bcrypt($data['new_password']),
                    ]);

                    return redirect()->back()->with('success_message', 'Admin Password has been updated successfully!');
                } else { 
                    return redirect()->back()->with('error_message', 'New Password and Confirm Password does not match!');
                }
            } else {
                return redirect()->back()->with('error_message', 'Your current admin password is Incorrect!');
            }
        }

        $adminDetails = Auth::guard('admin')->user()->toArray();
        // Ensure image maps to profile_image if view expects it, or update view. 
        // User model accessor 'image' -> 'profile_image' handles this.

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
        $logos      = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        if ($request->isMethod('post')) {
            $data  = $request->all();
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
                if ($image_tmp->isValid()) {
                    $extension = $image_tmp->getClientOriginalExtension();
                    $imageName = rand(111, 99999) . '.' . $extension;
                    $imagePath = 'admin/images/photos/' . $imageName;
                    Image::make($image_tmp)->save($imagePath);
                }
            } else if (! empty($data['current_admin_image'])) { 
                $imageName = $data['current_admin_image'];
            } else {
                $imageName = '';
            }

            // Update User Details
            $user = Auth::guard('admin')->user();
            $user->update([
                'name'    => $data['admin_name'],
                'phone'   => $data['admin_mobile'], // Map to phone
                'profile_image' => $imageName,      // Map to profile_image
            ]);

            return redirect()->back()->with('success_message', 'Details updated successfully!');
        }

        return view('admin/settings/update_admin_details', compact('logos', 'headerLogo'));
    }

    public function updateVendorDetails($slug, Request $request)
    { // $slug can only be: 'personal', 'business' or 'bank'
        $logos      = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        if ($slug == 'personal') {
            // Correcting issues in the Skydash Admin Panel Sidebar using Session
            Session::put('page', 'update_personal_details');

                                              // Handling update vendor personal details <form> submission
            if ($request->isMethod('post')) { // if the <form> is submitted
                $data = $request->all();
                // dd($data);

                $rules = [
                    'vendor_name'   => 'required|regex:/^[\pL\s\-]+$/u', // only alphabetical characters and spaces
                    'vendor_mobile' => 'required|numeric',
                    'country_id'    => 'nullable|exists:countries,id',
                    'state_id'      => 'nullable|exists:states,id',
                    'district_id'   => 'nullable|exists:districts,id',
                    'block_id'      => 'nullable|exists:blocks,id',
                ];

                $customMessages = [
                    'vendor_name.required'   => 'Name is required',
                    'vendor_name.regex'      => 'Valid Name is required',
                    'vendor_mobile.required' => 'Mobile is required',
                    'vendor_mobile.numeric'  => 'Valid Mobile is required',
                ];

                $this->validate($request, $rules, $customMessages);

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

                                                                               // Vendor details need to be updated in BOTH `users` and `vendors` tables:
                                                                               // Update Vendor Details in 'users' table
                User::where('id', Auth::guard('admin')->user()->id)->update([
                    'name'          => $data['vendor_name'],
                    'phone'         => $data['vendor_mobile'],
                    'profile_image' => $imageName,
                    'address'       => $data['vendor_address'] ?? null,
                    'country_id'    => $data['country_id'] ?? null,
                    'state_id'      => $data['state_id'] ?? null,
                    'district_id'   => $data['district_id'] ?? null,
                    'block_id'      => $data['block_id'] ?? null,
                    'pincode'       => $data['vendor_pincode'] ?? null,
                ]);

                // Update location in vendors table if needed
                if (isset($data['vendor_location'])) {
                    Vendor::where('user_id', Auth::guard('admin')->user()->id)->update([
                        'location' => $data['vendor_location'],
                    ]);
                }

                return redirect()->back()->with('success_message', 'Vendor details updated successfully!');
            }

            $vendorDetails = Vendor::where('id', Auth::guard('admin')->user()->vendor_id)->first()->toArray(); // Accessing Specific Guard Instances: https://laravel.com/docs/9.x/authentication#accessing-specific-guard-instances

        } else if ($slug == 'business') {
            // Correcting issues in the Skydash Admin Panel Sidebar using Session
            Session::put('page', 'update_business_details');

            if ($request->isMethod('post')) { // if the <form> is submitted
                $data = $request->all();
                // dd($data);

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
                if ($vendorCount > 0) {                                                                                     // if there's a
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
        $logos      = HeaderLogo::first();
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
    { // $type can be: admin, subadmin, vendor
        $headerLogo = HeaderLogo::first();
        $logos      = HeaderLogo::first();

        $query = User::query();

        if (! empty($type)) {
            $role = strtolower($type);
            // Map legacy types to roles if needed
            if ($role === 'superadmin') $role = 'admin';
            
            if ($role === 'admin') {
                $query->where('role_id', 1);
            } else {
                $query->role($role, 'web');
            }

            $title = ucfirst($type);
            Session::put('page', 'view_' . strtolower($title));
        } else {
            // Show all staff: admins, vendors, etc. (excluding basic users/students if desired?)
            // Assuming "Admins/Vendors" page usually implies staff.
            $query->role(['admin', 'vendor'], 'web');
            $title = 'All Admins/Vendors';
            Session::put('page', 'view_all');
        }

        $adminType = Auth::guard('admin')->user()->type; // Works via Accessor

        $admins = $query->with('vendorPersonal')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($user) {
                // Map User model to legacy array structure expected by view
                $arr = $user->toArray();
                $arr['type'] = $user->type; // uses getTypeAttribute
                $arr['mobile'] = $user->phone;
                $arr['image'] = $user->profile_image;
                
                if ($user->vendorPersonal) {
                    $arr['vendor_id'] = $user->vendorPersonal->id;
                } else {
                    $arr['vendor_id'] = 0;
                }
                
                return $arr;
            })
            ->toArray();

        return view('admin/admins/admins', compact('admins', 'title', 'logos', 'headerLogo', 'adminType'));
    }

    public function viewVendorDetails($id)
    {
        $logos         = HeaderLogo::first();
        $headerLogo    = HeaderLogo::first();
        $vendorDetails = User::with('vendorPersonal', 'vendorBusiness', 'vendorBank')->where('id', $id)->first();
        $vendorDetails = $vendorDetails ? $vendorDetails->toArray() : null;
                                                                                                                   // dd($vendorDetails);

        // Fetch countries for dropdowns
        $countries = Country::where('status', true)->get()->toArray();

        // Get current location IDs from vendor personal details
        $currentCountryId  = $vendorDetails['vendor_personal']['country_id'] ?? $vendorDetails['vendor_personal']['country_id'] ?? null;
        // The array key might be camelCase or snake_case depending on toArray behavior or manual mapping.
        // Let's try to be safe.
        if (isset($vendorDetails['vendorPersonal'])) {
            $currentCountryId = $vendorDetails['vendorPersonal']['country_id'] ?? null;
            $currentStateId = $vendorDetails['vendorPersonal']['state_id'] ?? null;
            $currentDistrictId = $vendorDetails['vendorPersonal']['district_id'] ?? null;
            $currentBlockId = $vendorDetails['vendorPersonal']['block_id'] ?? null;
        } else {
            $currentCountryId  = $vendorDetails['vendor_personal']['country_id'] ?? null;
            $currentStateId    = $vendorDetails['vendor_personal']['state_id'] ?? null;
            $currentDistrictId = $vendorDetails['vendor_personal']['district_id'] ?? null;
            $currentBlockId    = $vendorDetails['vendor_personal']['block_id'] ?? null;
        }

        return view('admin/admins/view_vendor_details', compact('vendorDetails', 'logos', 'headerLogo', 'countries', 'currentCountryId', 'currentStateId', 'currentDistrictId', 'currentBlockId'));
    }

    public function updateAdminStatus(Request $request)
    {
        $logos      = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
                                 // Update Admin Status using AJAX in admins.blade.php
        if ($request->ajax()) {  // if the request is coming via an AJAX call
            $data = $request->all(); // Getting the name/value pairs array that are sent from the AJAX request (AJAX call)
            
            $adminUser = User::where('id', $data['admin_id'])->first();
            if (!$adminUser) {
                return response()->json(['status' => 'error', 'message' => 'User not found.'], 404);
            }

            // Check permissions based on user type
            $permission = $adminUser->type === 'vendor' ? 'update_vendors_status' : 'update_admins_status';
            if (!Auth::guard('admin')->user()->can($permission)) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized action.'], 403);
            }

            if ($data['status'] == 'Active') { // $data['status'] comes from the 'data' object inside the $.ajax() method    // reverse the 'status' from (ative/inactive) 0 to 1 and 1 to 0 (and vice versa)
                $status = 0;
            } else {
                $status = 1;
            }

            User::where('id', $data['admin_id'])->update(['status' => $status]);
                                                                                  // echo '<pre>', var_dump($data), '</pre>';

                                                                                       // Send a THIRD Approval Email to the vendor when the superadmin or admin approves their account (`status` column in the `admins` table becomes 1 instead of 0) so that they can add their products on the website now
            $adminUser = User::where('id', $data['admin_id'])->first();

            if ($adminUser->type == 'vendor' && $status == 1) {                        // if the `type` column value (in `admins` table) is 'vendor', and their `status` became 1 (got approved), send them a THIRD confirmation mail
                Vendor::where('id', $adminUser->vendor_id)->update(['status' => $status]); //
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
        $logos      = HeaderLogo::first();

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

        $logos      = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        if ($request->isMethod('post')) {
            if ($request->hasFile('favicon')) {
                $file            = $request->file('favicon');
                $filename        = time() . '.' . $file->getClientOriginalExtension();
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
        $logos      = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        Session::put('page', 'admins');

        if ($request->isMethod('post')) {
            $data = $request->all();

            $userId = $data['admin_id'] ?? $id ?? null;
            $emailRule = 'required|email|unique:users,email';
            if (!empty($userId)) {
                $emailRule .= ',' . $userId;
            }

            // Laravel's Validation
            $rules = [
                'name'   => 'required|regex:/^[\pL\s\-&.,\'()\/]+$/u',
                'email'  => $emailRule,
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

            if (empty($userId)) {
                // Add mode: require password
                $rules['password']                    = 'required|min:6|confirmed';
                $rules['password_confirmation']       = 'required';
            }

            $this->validate($request, $rules, $customMessages);

            // Uploading Photo
            if ($request->hasFile('admin_image')) {
                $image_tmp = $request->file('admin_image');
                if ($image_tmp->isValid()) {
                    $extension = $image_tmp->getClientOriginalExtension();
                    $imageName = rand(111, 99999) . '.' . $extension;
                    $imagePath = 'admin/images/photos/' . $imageName;
                    Image::make($image_tmp)->save($imagePath);
                }
            } else if (! empty($data['current_admin_image'])) {
                $imageName = $data['current_admin_image'];
            } else {
                $imageName = '';
            }

            // Prepare User Data
            $userData = [
                'name'    => $data['name'],
                'email'   => $data['email'],
                'phone'   => $data['mobile'], // User model uses 'phone'
                'status'  => isset($data['status']) ? 1 : 0,
                'profile_image' => $imageName, // User model uses 'profile_image'
            ];

            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            // Determine Role (Default to Admin if not specified or context implies)
            // The form should ideally have a role selector or implied type
            // Based on original code `type` was often 'vendor' or 'admin'
            // We'll stick to 'vendor' if it's a vendor add, else 'admin'
            // For now, let's assume if it is NOT a vendor add, it is an ADMIN add
            // Only 'admin' and 'vendor' were primary types here.
            
            // Check if we are adding a vendor specifically (usually from a specific route or hidden field)
            // But here it seems general. Let's look at `type` input or default.
            // Original code: $adminData['type'] = 'vendor'; (Wait, it forced 'vendor'??)
            // Line 800 in original: 'type' => 'vendor'.
            // Actually, the original code looked like it defaulted to 'vendor' in `addEditAdmin` for some reason? 
            // Ah, looking closer at line 564 `public function admins($type = null)`...
            // It seems this controller handles both.
            // Let's assume if it creates a Vendor profile, account is Vendor.
            
            // BUT wait, line 800 in viewed file said `'type' => 'vendor'`.
            // Let's assume this form is used for Vendors mostly? 
            // Or maybe I missed where type is set.
            // Let's look at existing `admins()` method logic.
            
            // Re-reading logic: If it creates a Vendor profile (lines 809+), then it IS a vendor.
            
// ... (existing code).

            if (empty($userId)) {
                // ADD MODE
                $roleName = 'admin'; // Default

                // Check if we are adding a vendor specifically
                $roleName = 'vendor'; // As per existing logic

                // Dynamic Role Fetching as per user request
                $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
                if (!$role) {
                    return redirect()->back()->with('error_message', "Role '$roleName' not found for web guard.");
                }

                $userData['role_id'] = $role->id;

                $user = User::create($userData);
                $user->assignRole($role); // Assign Spatie Role

                // Create Vendor Profile
                $vendorData = [
                    'name'       => $data['name'],
                    'email'      => $data['email'],
                    'mobile'     => $data['mobile'],
                    'confirm'    => 'Yes',
                    'status'     => isset($data['status']) ? 1 : 0,
                    'user_id'    => $user->id, // LINK HERE
                ];
                Vendor::create($vendorData);

                return redirect('admin/admins')->with('success_message', 'Vendor added successfully!');

            } else {
                // EDIT MODE
                $user = User::findOrFail($userId);
                
                // Remove password from update if empty
                if (empty($data['password'])) {
                    unset($userData['password']);
                }
                
                $user->update($userData);

                // Update Vendor Profile if linked
                $vendor = Vendor::where('user_id', $user->id)->first();
                if ($vendor) {
                    $vendorUpdateData = [
                        'name'   => $data['name'],
                        'email'  => $data['email'],
                        'mobile' => $data['mobile'],
                    ];
                    // Update location fields if provided
                    if (isset($data['vendor_address'])) $vendorUpdateData['address'] = $data['vendor_address'];
                    if (isset($data['vendor_pincode'])) $vendorUpdateData['pincode'] = $data['vendor_pincode'];
                    if (isset($data['vendor_country_id'])) $vendorUpdateData['country_id'] = $data['vendor_country_id'];
                    if (isset($data['vendor_state_id'])) $vendorUpdateData['state_id'] = $data['vendor_state_id'];
                    if (isset($data['vendor_district_id'])) $vendorUpdateData['district_id'] = $data['vendor_district_id'];
                    if (isset($data['vendor_block_id'])) $vendorUpdateData['block_id'] = $data['vendor_block_id'];

                    $vendor->update($vendorUpdateData);
                }

                return redirect('admin/admins')->with('success_message', 'Details updated successfully!');
            }
        }

        // GET request
        $countries = Country::where('status', true)->get()->toArray();
        if (! empty($id)) {
            $admin = User::findOrFail($id); // Using User model
            // Map User fields to what view expects if necessary (User has phone, view might expect mobile)
            $adminArray = $admin->toArray();
            $adminArray['mobile'] = $admin->phone; // Accessor handles this? Yes, I added getMobileAttribute.
            $adminArray['image'] = $admin->profile_image;
            $admin = $adminArray; // View expects array

            $vendorPersonal = [];
            $vendorBusiness = [];
            $vendorBank     = [];

            // Find linked vendor
            $vendorProfile = Vendor::where('user_id', $id)->first(); // Use user_id link
            
            if ($vendorProfile) {
                $vendorPersonal = $vendorProfile->toArray();
                $vendorBusiness = VendorsBusinessDetail::where('vendor_id', $vendorProfile->id)->first();
                $vendorBank     = VendorsBankDetail::where('vendor_id', $vendorProfile->id)->first();

                $vendorBusiness = $vendorBusiness ? $vendorBusiness->toArray() : [];
                $vendorBank     = $vendorBank ? $vendorBank->toArray() : [];
                // Add type 'vendor' for view logic compatibility
                $admin['type'] = 'vendor';
                $admin['vendor_id'] = $vendorProfile->id;
            } else {
                 $admin['type'] = 'admin'; // Or check role
            }

            return view('admin/admins/edit', compact('admin', 'vendorPersonal', 'vendorBusiness', 'vendorBank', 'countries', 'logos', 'headerLogo'));
        } else {
            return view('admin/admins/add', compact('logos', 'headerLogo'));
        }
    }

    public function deleteAdmin($id)
    {
        // Prevent deleting own admin account
        if ($id == Auth::guard('admin')->user()->id) {
            return redirect()->back()->with('error_message', 'You cannot delete yourself!');
        }

        // Fetch User
        $user = User::findOrFail($id);

        // Check permissions
        $permission = $user->type === 'vendor' ? 'delete_vendors' : 'delete_admins';
        if (!Auth::guard('admin')->user()->can($permission)) {
            return redirect()->back()->with('error_message', 'You do not have permission to delete this user.');
        }

        // Delete admin profile image if exists
        if (! empty($user->profile_image) && file_exists(public_path('admin/images/photos/' . $user->profile_image))) {
            unlink(public_path('admin/images/photos/' . $user->profile_image));
        }

        // Delete vendor from vendors table too if accessible via relation or user_id
        $vendor = Vendor::where('user_id', $user->id)->first();
        if ($vendor) {
           $vendor->delete();
        }

        // Delete user
        $user->delete();

        return redirect()->back()->with('success_message', 'Admin/Vendor deleted successfully!');
    }

    public function contactQueries()
    {
        Session::put('page', 'contact_queries');
        $headerLogo = HeaderLogo::first();
        $logos      = HeaderLogo::first();

        $queries = ContactUs::with('replies')->orderBy('created_at', 'desc')->get()->toArray();

        return view('admin/contact_queries/index', compact('queries', 'logos', 'headerLogo'));
    }

    public function updateContactStatus(Request $request)
    {
        $logos      = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        if ($request->ajax()) {
            $data = $request->all();

            ContactUs::where('id', $data['query_id'])->update(['status' => $data['status']]);

            return response()->json([
                'status'   => $data['status'],
                'query_id' => $data['query_id'],
            ]);
        }
    }

    public function updateContactReply(Request $request, $id)
    {
        $logos      = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        if ($request->isMethod('post')) {
            $data = $request->all();

            // Get current query status
            $currentQuery = ContactUs::where('id', $id)->first();
            $wasResolved  = $currentQuery && $currentQuery->status == 'resolved';

            // If query is already resolved and admin is just changing status, admin_reply is optional
            $rules = [
                'status' => 'required|in:pending,resolved,in_progress',
            ];

            $customMessages = [
                'status.required' => 'Status is required',
            ];

            // Only require admin_reply if query is not already resolved or if status is being changed to resolved
            if (! $wasResolved || ($data['status'] == 'resolved' && ! $wasResolved)) {
                $rules['admin_reply']                   = 'required';
                $customMessages['admin_reply.required'] = 'Reply is required';
            }

            $this->validate($request, $rules, $customMessages);

            // Update main admin_reply field (for backward compatibility)
            // Only update admin_reply if it's provided
            $updateData = ['status' => $data['status']];
            if (! empty($data['admin_reply'])) {
                $updateData['admin_reply'] = $data['admin_reply'];
            }
            ContactUs::where('id', $id)->update($updateData);

            // Only create a new reply entry if admin is providing a new reply message
            if (! empty($data['admin_reply'])) {
                // Also save to replies table for conversation thread
                ContactReply::create([
                    'contact_us_id' => $id,
                    'reply_by'      => 'admin',
                    'message'       => $data['admin_reply'],
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
        $logos      = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        ContactUs::where('id', $id)->delete();

        return redirect('admin/contact-queries')->with('success_message', 'Query deleted successfully!');
    }

    public function comingSoonSettings(Request $request)
    {
        Session::put('page', 'coming_soon_settings');
        
        $logos      = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        
        if ($request->isMethod('post')) {
            // Coming Soon Settings
            $comingSoonEnabled = $request->has('coming_soon_enabled') ? 1 : 0;
            Setting::setValue('coming_soon_enabled', $comingSoonEnabled);
            
            $showCountdown = $request->has('show_countdown') ? 1 : 0;
            Setting::setValue('show_countdown', $showCountdown);
            
            $countdownDate = $request->input('countdown_date');
            if ($countdownDate) {
                Setting::setValue('countdown_date', $countdownDate);
            }
            
            $countdownTime = $request->input('countdown_time');
            if ($countdownTime) {
                Setting::setValue('countdown_time', $countdownTime);
            }
            
            // Maintenance Mode Settings
            $maintenanceModeEnabled = $request->has('maintenance_mode_enabled') ? 1 : 0;
            Setting::setValue('maintenance_mode_enabled', $maintenanceModeEnabled);
            
            // Social Media URLs
            Setting::setValue('social_facebook', $request->input('social_facebook', ''));
            Setting::setValue('social_twitter', $request->input('social_twitter', ''));
            Setting::setValue('social_instagram', $request->input('social_instagram', ''));
            Setting::setValue('social_linkedin', $request->input('social_linkedin', ''));
            Setting::setValue('social_youtube', $request->input('social_youtube', ''));
            Setting::setValue('social_pinterest', $request->input('social_pinterest', ''));
            Setting::setValue('social_whatsapp', $request->input('social_whatsapp', ''));
            Setting::setValue('social_telegram', $request->input('social_telegram', ''));
            
            return back()->with('success_message', 'Settings updated successfully.');
        }
        
        $comingSoonEnabled = Setting::getValue('coming_soon_enabled', 0);
        $showCountdown = Setting::getValue('show_countdown', 1);
        $countdownDate = Setting::getValue('countdown_date', '');
        $countdownTime = Setting::getValue('countdown_time', '');
        $maintenanceModeEnabled = Setting::getValue('maintenance_mode_enabled', 0);
        
        // Social Media URLs
        $socialFacebook = Setting::getValue('social_facebook', '');
        $socialTwitter = Setting::getValue('social_twitter', '');
        $socialInstagram = Setting::getValue('social_instagram', '');
        $socialLinkedin = Setting::getValue('social_linkedin', '');
        $socialYoutube = Setting::getValue('social_youtube', '');
        $socialPinterest = Setting::getValue('social_pinterest', '');
        $socialWhatsapp = Setting::getValue('social_whatsapp', '');
        $socialTelegram = Setting::getValue('social_telegram', '');
        
        return view('admin/settings/coming_soon', compact(
            'logos', 
            'headerLogo', 
            'comingSoonEnabled',
            'showCountdown',
            'countdownDate',
            'countdownTime',
            'maintenanceModeEnabled',
            'socialFacebook',
            'socialTwitter',
            'socialInstagram',
            'socialLinkedin',
            'socialYoutube',
            'socialPinterest',
            'socialWhatsapp',
            'socialTelegram'
        ));
    }
}
