<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\HeaderLogo;
use App\Models\Vendor;
use App\Models\Admin;
use App\Models\Language;
use App\Models\Notification;
use App\Models\Section;
use Intervention\Image\Facades\Image;

class VendorController extends Controller
{
    public function loginRegister(Request $request) {
        $condition = session('condition', 'new');
        $logos     = HeaderLogo::all();
        $sections  = Section::all();
        $language  = Language::get();
        if (!in_array($condition, ['new', 'old'])) {
            $condition = 'new';
        }
        return view('front.vendors.login_register',compact('condition', 'logos', 'sections', 'language'));
    }

    public function vendorRegister(Request $request) {
        $condition = session('condition', 'new');

        if (!in_array($condition, ['new', 'old'])) {
            $condition = 'new';
        }
        if ($request->isMethod('post')) {
            $data = $request->all();
            $rules = [

                            'name'          => 'required',
                            'email'         => 'required|email|unique:admins|unique:vendors',
                            'mobile'        => 'required|min:10|numeric|unique:admins|unique:vendors',
                            'accept'        => 'required'
            ];

            $customMessages = [
                                'name.required'             => 'Name is required',
                                'email.required'            => 'Email is required',
                                'email.unique'              => 'Email alreay exists',
                                'mobile.required'           => 'Mobile is required',
                                'mobile.unique'             => 'Mobile alreay exists',
                                'accept.required'           => 'Please accept Terms & Conditions',
            ];

            $validator = Validator::make($data, $rules, $customMessages);
            if ($validator->fails()) {
                return \Illuminate\Support\Facades\Redirect::back()->withErrors($validator);
            }

            DB::beginTransaction();


            $vendor = new Vendor; // Vendor.php model which models (represents) the `vendors` database table

            $vendor->name   = $data['name'];
            $vendor->mobile = $data['mobile'];
            $vendor->email  = $data['email'];
            $vendor->status = 0;
            date_default_timezone_set('Africa/Cairo'); // https://www.php.net/manual/en/timezones.php and https://www.php.net/manual/en/timezones.africa.php
            $vendor->created_at = date('Y-m-d H:i:s'); // enter `created_at` MANUALLY!    // Formatting the date for MySQL: https://www.php.net/manual/en/function.date.php
            $vendor->updated_at = date('Y-m-d H:i:s'); // enter `updated_at` MANUALLY!

            $vendor->save();


            $vendor_id = DB::getPdo()->lastInsertId();
            $admin = new Admin;

            $admin->type      = 'vendor';
            $admin->vendor_id = $vendor_id; // take the generated `id` of the `vendors` table to store it a `vendor_id` in the `admins` table
            $admin->name      = $data['name'];
            $admin->mobile    = $data['mobile'];
            $admin->email     = $data['email'];
            $admin->password  = bcrypt($data['password']);
            $admin->status    = 0;

            date_default_timezone_set('Africa/Cairo');
            $admin->created_at = date('Y-m-d H:i:s');
            $admin->updated_at = date('Y-m-d H:i:s'); // enter `updated_at` MANUALLY!

            $admin->save();


            // Send the Confirmation Email to the new vendor who has just registered
            $email = $data['email']; // the vendor's email

            // The email message data/variables that will be passed in to the email view
            $messageData = [
                'email' => $data['email'],
                'name'  => $data['name'],
                'code'  => base64_encode($data['email'])
            ];

            \Illuminate\Support\Facades\Mail::send('emails.vendor_confirmation', $messageData, function ($message) use ($email) {
                $message->to($email)->subject('Confirm your Vendor Account');
            });


            DB::commit();
            $message = 'Thanks for registering as Vendor. Please confirm your email to activate your account.';
            return redirect()->back()->with('success_message', $message);
        }
    }

    public function confirmVendor($email,Request $request) {
        $condition = session('condition', 'new');

        $email = base64_decode($email);
        $vendorCount = Vendor::where('email', $email)->count();
        if ($vendorCount > 0) { // if the vendor email exists
            // Check if the vendor is alreay active
            $vendorDetails = Vendor::where('email', $email)->first();
            if ($vendorDetails->confirm == 'Yes') { // if the vendor is already confirmed

                // Redirect vendor to vendor Login/Register page with an 'error' message
                $message = 'Your Vendor Account is already confirmed. You can login';
                return redirect('vendor/login-register')->with('error_message', $message);

            } else {

                Admin::where( 'email', $email)->update(['confirm' => 'Yes']);
                Vendor::where('email', $email)->update(['confirm' => 'Yes']);

                $messageData = [
                    'email'  => $email,
                    'name'   => $vendorDetails->name,
                    'mobile' => $vendorDetails->mobile
                ];
                \Illuminate\Support\Facades\Mail::send('emails.vendor_confirmed', $messageData, function ($message) use ($email) {
                    $message->to($email)->subject('You Vendor Account Confirmed');
                });

                $message = 'Your Vendor Email account is confirmed. You can login and add your personal, business and bank details to activate your Vendor Account to add products';
                return redirect('vendor/login-register')->with('success_message', $message);
            }
        } else {
            abort(404);
        }
    }

    public function showRegister()
    {
        $headerLogo = HeaderLogo::first();
        $logos = $headerLogo;

        return view('vendor.register', compact('logos', 'headerLogo'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:150',
            'email'                 => 'required|email|unique:admins,email|unique:vendors,email',
            'mobile'                => 'required|digits:10|unique:admins,mobile|unique:vendors,mobile',
            'password'              => 'required|min:6|confirmed',
            'vendor_image'          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $name   = $request->name;
        $email  = $request->email;
        $mobile = $request->mobile;

        DB::beginTransaction();

        try {
            // Handle image upload
            $imageName = '';
            if ($request->hasFile('vendor_image')) {
                $image_tmp = $request->file('vendor_image');

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
            }

            // Create Vendor record
            $vendor = Vendor::create([
                'name'   => $name,
                'mobile' => $mobile,
                'email'  => $email,
                'status' => 0,
                'confirm' => 'No',
            ]);

            // Create Admin record for vendor
            $admin = Admin::create([
                'type'      => 'vendor',
                'vendor_id' => $vendor->id,
                'name'      => $name,
                'mobile'    => $mobile,
                'email'     => $email,
                'password'  => Hash::make($request->password),
                'status'    => 0,
                'confirm'   => 'No',
                'image'     => $imageName,
            ]);

            // Create notification for admin
            Notification::create([
                'type' => 'vendor_registration',
                'title' => 'New Vendor Registration',
                'message' => "A new vendor '{$name}' has registered and is waiting for approval.",
                'related_id' => $vendor->id,
                'related_type' => 'App\Models\Vendor',
                'is_read' => false,
            ]);

            // Send confirmation email
            $messageData = [
                'email' => $email,
                'name'  => $name,
                'code'  => base64_encode($email)
            ];

            \Illuminate\Support\Facades\Mail::send('emails.vendor_confirmation', $messageData, function ($message) use ($email) {
                $message->to($email)->subject('Confirm your Vendor Account');
            });

            DB::commit();

            return redirect()->route('vendor.login-register')
                ->with('success_message', 'Registration successful! Please confirm your email to activate your account.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Registration failed. Please try again.');
        }
    }
}
