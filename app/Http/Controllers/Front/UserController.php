<?php
namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\BookRequest;
use App\Models\Cart;
use App\Models\Category;
use App\Models\ContactUs;
use App\Models\ContactReply;
use App\Models\Country;
use App\Models\HeaderLogo;
use App\Models\Language;
use App\Models\Product;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // Render User Login/Register page (front/users/login_register.blade.php)
    public function loginRegister(Request $request)
    {
        $condition = session('condition', 'new');
        $sections  = Section::all();
        $logos     = HeaderLogo::all();
        $language  = Language::get();

        if (! in_array($condition, ['new', 'old'])) {
            $condition = 'new';
        }

        $footerProducts = Product::orderBy('id', 'Desc')
            ->where('condition', $condition)
            ->where('status', 1)
            ->take(3)
            ->get()
            ->toArray();
        $category = Category::limit(10)->get();
        return view('front.users.login_register', compact('condition', 'footerProducts', 'category', 'sections', 'language', 'logos'));
    }

    // User Registration (in front/users/login_register.blade.php) <form> submission using an AJAX request. Check front/js/custom.js
    public function userRegister(Request $request)
    {
        $data = $request->all();
        // dd($request->all());

        // Validation
        $validator = Validator::make($data, [
            'name'     => 'required|string|max:100',
            'mobile'   => 'required|numeric|digits:10',
            'email'    => 'required|email|max:150|unique:users,email',
            'password' => 'required|min:6',
        ], [
            'accept.required' => 'Please accept our Terms & Conditions',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Save the user
        $user           = new User;
        $user->name     = $data['name'];
        $user->mobile   = $data['mobile'];
        $user->email    = $data['email'];
        $user->password = bcrypt($data['password']);
        $user->status   = 0; // inactive until email confirmed
        $user->save();

        // Send confirmation email
        $email       = $data['email'];
        $messageData = [
            'name'  => $data['name'],
            'email' => $data['email'],
            'code'  => base64_encode($data['email']),
        ];

        // Mail::send('emails.confirmation', $messageData, function ($message) use ($email) {
        //     $message->to($email)->subject('Confirm your Multi-vendor E-commerce Application Account');
        // });

        return redirect(url()->previous())->with('success_message', 'Registration successfully!!');
    }

    // User Login (in front/users/login_register.blade.php) <form> submission using an AJAX request. Check front/js/custom.js
    public function userLogin(Request $request)
    {
        $data = $request->all();

        // Validate the form input
        $validator = Validator::make($data, [
            'login'    => 'required|string|max:150',
            'password' => 'required|min:6',
        ], [
            'login.required' => 'Email or mobile number is required.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $loginInput = $data['login'];
        $credentials = ['password' => $data['password']];
        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $credentials['email'] = $loginInput;
        } elseif (preg_match('/^\d{10}$/', $loginInput)) {
            $credentials['mobile'] = $loginInput;
        } else {
            return redirect()->back()->withErrors([
                'login' => 'Enter a valid email or 10-digit mobile number.',
            ])->withInput();
        }

        // Attempt login
        if (Auth::attempt($credentials)) {
            // Check if user is inactive
            if (Auth::user()->status == 0) {
                Auth::logout();
                return redirect()->back()->with('error_message', 'Your account is not activated! Please confirm your account to activate it.');
            }

            // Update cart
            if (! empty(Session::get('session_id'))) {
                $user_id    = Auth::user()->id;
                $session_id = Session::get('session_id');
                Cart::where('session_id', $session_id)->update(['user_id' => $user_id]);
            }

            // Redirect to cart or dashboard
            return redirect(url()->previous());
        } else {
            // Incorrect credentials
            return redirect()->back()->with('error_message', 'Incorrect login credentials!');
        }
    }

    // User logout (This route is accessed from Logout tab in the drop-down menu in the header (in front/layout/header.blade.php))
    public function userLogout()
    {
        Auth::logout(); // Logging Out: https://laravel.com/docs/9.x/authentication#logging-out

                          // Emptying the Session to empty the Cart when the user logs out
        Session::flush(); // Deleting Data: https://laravel.com/docs/9.x/session#deleting-data

        return redirect('/');
    }

                                            // User account Confirmation E-mail which contains the 'Activation Link' to activate the user account (in resources/views/emails/confirmation.blade.php, using Mailtrap)
    public function confirmAccount($code)
    { // {code} is the base64 encoded user's 'Activation Code' sent to the user in the Confirmation E-mail with which they have registered, which is received as a Route Parameters/URL Paramters in the 'Activation Link': https://laravel.com/docs/9.x/routing#required-parameters    // this route is requested (accessed/opened) from inside the mail sent to user (in resources/views/emails/confirmation.blade.php)
        $email = base64_decode($code);          // $code is the encoded $email (check userRegister() method in UserController.php)    // we use the opposite (base64_decode()) of what we used in the userRegister() (base_64encode)
                                                // dd($email);

        // For Security Reasons, check if that decoded user's $email exists in the `users` database table
        $userCount = User::where('email', $email)->count();
        if ($userCount > 0) { // if the user's email exists in `users` table
                                  // Check if the user is alreay active
            $userDetails = User::where('email', $email)->first();
            if ($userDetails->status == 1) { // if the user's account is already activated
                                                 // Redirect the user to the User Login/Register page with an 'error' message
                return redirect('user/login-register')->with('error_message', 'Your account is already activated. You can login now.');
            } else { // if the user's account is not yet activated, activate it (update `status` to 1) and send a 'Welcome' Email
                User::where('email', $email)->update([
                    'status' => 1,
                ]);

                // Send a Welcome Email to user after confirmation (clicking on the 'Activation Link' inside the Confirmation Email)    // HELO / Mailtrap / MailHog: https://laravel.com/docs/9.x/mail#mailtrap

                // The email message data/variables that will be passed in to the email view
                $messageData = [
                    'name'   => $userDetails->name,   // the user's name that they entered while submitting the registration form
                    'mobile' => $userDetails->mobile, // the user's mobile that they entered while submitting the registration form
                    'email'  => $email,               // the user's email that they entered while submitting the registration form
                                                      // 'code'   => base64_encode($data['email']) // We base64 code the user's $email and send it as a Route Parameter from user_confirmation.blade.php to the 'user/confirm/{code}' route in web.php, then it gets base64 decoded again in confirmUser() method in Front/UserController.php    // we will use the opposite: base64_decode() in the confirmAccount() method (encode X decode)
                ];
                \Illuminate\Support\Facades\Mail::send('emails.register', $messageData, function ($message) use ($email) { // Sending Mail: https://laravel.com/docs/9.x/mail#sending-mail    // 'emails.register' is the register.blade.php file inside the 'resources/views/emails' folder that will be sent as an email    // We pass in all the variables that register.blade.php will use    // https://www.php.net/manual/en/functions.anonymous.php
                    $message->to($email)->subject('Welcome to Multi-vendor E-commerce Application');
                });

                // Note: Here, we have TWO options, either redirect user with a success message or Log the user In IMMDEIATELY, AUTOMATICALLY and DIRECTLY

                // Redirect the user to the User Login/Register page with a 'success' message
                return redirect('user/login-register')->with('success_message', 'Your account is activated. You can login now.');
            }
        } else { // if the user's email doesn't exist (hacking or cyber attack!!)
            abort(404);
        }
    }

                                                       // User Forgot Password Functionality (this route is accessed from the <a> tag in front/users/login_register.blade.php through a 'GET' request, and through a 'POST' request when the HTML Form is submitted in front/users/forgot_password.blade.php))
    public function forgotPassword(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->all();
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'email' => 'required|email|max:150|exists:users',
            ], [
                'email.exists' => 'Email does not exist',
            ]);

            if ($validator->passes()) { // if validation passes (is successful), generate a new password for the user
                $new_password = \Illuminate\Support\Str::random(16);


                User::where('email', $data['email'])->update([
                    'password' => bcrypt($new_password),
                ]);

                // Get user details
                $userDetails = User::where('email', $data['email'])->first()->toArray();
                $email = $data['email'];
                $messageData = [
                    'name'     => $userDetails['name'],
                    'email'    => $email,
                    'password' => $new_password,
                ];
                \Illuminate\Support\Facades\Mail::send('emails.user_forgot_password', $messageData, function ($message) use ($email) {
                    $message->to($email)->subject('New Password - Multi-vendor E-commerce Application');
                });


                return response()->json([ // JSON Responses: https://laravel.com/docs/9.x/responses#json-responses
                    'type'    => 'success',
                    'message' => 'New Password sent to your registered email.',
                ]);
            } else {                  // if validation fails (is unsuccessful), send the Validation Error Messages
                                          // Here, we return a JSON response because the request is ORIGINALLY submitting an HTML <form> data using an AJAX request
                return response()->json([ // JSON Responses: https://laravel.com/docs/9.x/responses#json-responses
                    'type'   => 'error',
                    'errors' => $validator->messages(), // we'll loop over the Validation Errors Messages array using jQuery to show them in the frontend (check front/js/custom.js)    // Working With Error Messages: https://laravel.com/docs/9.x/validation#working-with-error-messages
                ]);
            }
        } else { // if the 'GET' request is coming from the <a> tag in front/users/login_register.blade.php, render the front/users/forgot_password.blade.php page
            return view('front.users.forgot_password');
        }
    }

    public function userAccount(Request $request)
    {
        $condition = session('condition', 'new');

        // Always fetch footer products (for both GET and POST)
        $footerProducts = Product::orderBy('id', 'desc')
            ->where('condition', $condition)
            ->where('status', 1)
            ->take(3)
            ->get()
            ->toArray();

        $category       = Category::limit(10)->get();
        $sections       = Section::all();
        $logos          = HeaderLogo::all();
        $language       = Language::get();
        $requestedBooks = BookRequest::where('requested_by_user', Auth::id())->orderBy('created_at', 'desc')->get();
        $contactQueries = ContactUs::with('replies')->where('email', Auth::user()->email)->orderBy('created_at', 'desc')->get();
        $countries = Country::all();
        $user           = Auth::user();

        // ----- THIS HANDLES POST from the FORM -----
        if ($request->isMethod('post')) {
            $data = $request->all();

            // Validation
            $request->validate([
                'email'   => 'required|email|max:100|unique:users,email,' . Auth::id(),
                'name'    => 'required|string|max:100',
                'city'    => 'required|string|max:100',
                'state'   => 'required|string|max:100',
                'address' => 'required|string|max:100',
                'country' => 'required|string|max:100',
                'mobile'  => 'required|numeric|digits:10',
                'pincode' => 'required|digits:6',
            ]);

            User::where('id', Auth::id())->update([
                'email'   => $data['email'],
                'name'    => $data['name'],
                'mobile'  => $data['mobile'],
                'city'    => $data['city'],
                'state'   => $data['state'],
                'country' => $data['country'],
                'pincode' => $data['pincode'],
                'address' => $data['address'],
            ]);

            // Redirect back with success message
            return redirect()->back()->with('success_message', 'Account details updated successfully!');
        }

        // For GET request: Show the form
        return view('front.users.user_account')->with(compact(
            'user', 'countries', 'condition', 'footerProducts', 'category', 'logos', 'sections', 'language', 'requestedBooks', 'contactQueries'
        ));
    }

    // User Account Update Password HTML Form submission via AJAX. Check front/js/custom.js
    public function userUpdatePassword(Request $request)
    {
        if ($request->isMethod('post')) {

            $request->validate([
                'current_password' => 'required',
                'new_password'     => 'required|min:6',
                'confirm_password' => 'required|min:6|same:new_password',
            ]);

            /** @var \App\Models\User $user */
            $user = Auth::user();

            if (Hash::check($request->input('current_password'), $user->password)) {
                $user->password = bcrypt($request->input('new_password'));
                $user->save();

                return redirect()->back()->with('success_message', 'Account password successfully updated!');
            } else {
                return redirect()->back()->withErrors(['current_password' => 'Your current password is incorrect!']);
            }
        }

        // If GET request, just show form (optional)
        return view('front.users.change_password');
    }

    public function replyToQuery(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|min:10',
        ], [
            'message.required' => 'Reply message is required',
            'message.min' => 'Reply must be at least 10 characters',
        ]);

        // Verify the query belongs to the logged-in user
        $query = ContactUs::where('id', $id)
            ->where('email', Auth::user()->email)
            ->first();

        if (!$query) {
            return redirect()->back()->with('error_message', 'Query not found or you do not have permission to reply.');
        }

        // Create reply
        ContactReply::create([
            'contact_us_id' => $id,
            'reply_by' => 'user',
            'message' => $request->message,
        ]);

        // Update query status to in_progress if it was resolved
        if ($query->status == 'resolved') {
            $query->update(['status' => 'in_progress']);
        }

        return redirect()->back()->with('success_message', 'Your reply has been sent successfully!');
    }
    public function about(){
        $condition = session('condition', 'new');
        $sections  = Section::all();
        $logos     = HeaderLogo::all();
        $language  = Language::get();

        return view('front.pages.about', compact('condition', 'sections', 'logos', 'language'));
    }
    public function privacyPolicy(){
        $condition = session('condition', 'new');
        $sections  = Section::all();
        $logos     = HeaderLogo::all();
        $language  = Language::get();

        return view('front.pages.privacypolicy', compact('condition', 'sections', 'logos', 'language'));
    }
    public function services(){
        $condition = session('condition', 'new');
        $sections  = Section::all();
        $logos     = HeaderLogo::all();
        $language  = Language::get();

        return view('front.pages.services', compact('condition', 'sections', 'logos', 'language'));
    }

    public function sales(){
        $condition = session('condition', 'new');
        $sections  = Section::all();
        $logos     = HeaderLogo::all();
        $language  = Language::get();

        return view('front.pages.sales', compact('condition', 'sections', 'logos', 'language'));
    }

}
