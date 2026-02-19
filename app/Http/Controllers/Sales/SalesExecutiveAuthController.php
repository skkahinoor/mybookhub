<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\SalesExecutive;
use App\Models\InstitutionManagement;
use App\Models\User;
use App\Models\InstitutionClass;
use App\Models\Language;
use App\Models\Notification;
use App\Models\Section;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail; // ✅ IMPORTANT
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class SalesExecutiveAuthController extends Controller
{
    // LOGIN ---------------------
    public function showLogin()
    {
        $logos    = HeaderLogo::first();
        $language = Language::get();
        $sections = Section::all();
        $condition      = session('condition', 'new');
        $headerLogo = HeaderLogo::first();

        return view('sales.login', compact('logos', 'language', 'sections', 'condition', 'headerLogo'));
    }

    public function login(Request $request)
    {

        $data = $request->validate([
            'login'    => ['required', 'string', 'max:150'],
            'password' => ['required'],
        ]);

        $loginInput   = trim($data['login']);
        $numericLogin = preg_replace('/\D/', '', $loginInput);

        // Identify user by email or phone in the User table
        $userQuery = User::query()->role('sales', 'web');

        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $user = $userQuery->where('email', $loginInput)->first();
        } elseif (strlen($numericLogin) >= 10 && strlen($numericLogin) <= 11) {
            // User model uses 'phone'
            $user = $userQuery->where('phone', $numericLogin)->first();
        } else {
            return back()
                ->withErrors(['login' => 'Enter a valid email or 10/11-digit mobile number.'])
                ->withInput();
        }

        if (!$user) {
            return back()->withErrors([
                'login' => 'The provided credentials do not match our records or you do not have permission.',
            ])->onlyInput('login');
        }

        if ($user->status == 0) {
            return back()->withErrors([
                'login' => 'Your account is not activated yet. Please contact admin.',
            ])->onlyInput('login');
        }

        // Use the default 'web' guard or 'sales' guard if it's pointing to User model
        if (Auth::guard('sales')->attempt(
            [
                'email' => $user->email,
                'password' => $data['password']
            ],
            $request->boolean('remember')
        )) {
            $request->session()->regenerate();
            return redirect()->intended('/sales/dashboard');
        }

        return back()->withErrors([
            'login' => 'Invalid password.',
        ])->onlyInput('login');
    }


    // REGISTER (SHOW FORM) ---------------------
    public function showRegister()
    {
        $headerLogo = HeaderLogo::first();
        $logos      = HeaderLogo::first();

        return view('sales.register', compact('logos', 'headerLogo'));
    }

    public function sendSMS($phone, $otp)
    {
        $to = '91' . preg_replace('/[^0-9]/', '', $phone);

        try {
            $client = new Client();

            $payload = [
                "template_id" => config('services.msg91.template_id'),
                "recipients"  => [
                    [
                        "mobiles" => $to,
                        "OTP"     => $otp
                    ]
                ]
            ];

            Log::info("MSG91 Payload:", $payload);

            $response = $client->post("https://control.msg91.com/api/v5/flow/", [
                'json' => $payload,
                'headers' => [
                    'accept' => 'application/json',
                    'authkey' => config('services.msg91.key'),
                    'content-type' => 'application/json'
                ],
            ]);

            Log::info("MSG91 Response:", [
                'status' => $response->getStatusCode(),
                'body'   => $response->getBody()->getContents()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("MSG91 ERROR: " . $e->getMessage());
            return false;
        }
    }

    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|digits:10|unique:users,phone',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $otp = rand(100000, 999999);

        DB::table('otps')->updateOrInsert(
            ['phone' => $request->phone],
            ['otp' => $otp, 'created_at' => now(), 'updated_at' => now()]
        );

        session([
            'reg_name'  => $request->name,
            'reg_email' => $request->email,
            'reg_phone' => $request->phone,
        ]);


        $sent = $this->sendSMS($request->phone, $otp);

        if (!$sent) {
            return response()->json([
                'status' => false,
                'message' => 'OTP failed to send. Try again.',
            ], 500);
        }

        return response()->json([
            'status'  => true,
            'message' => 'OTP sent successfully!',
        ]);
    }

    public function register(Request $request)
    {
        $headerLogo = HeaderLogo::first();
        $logos      = HeaderLogo::first();
        $request->validate([
            'otp'                   => 'required',
            'phone'                 => 'required',
            'password'              => 'required|min:6|confirmed',
        ]);

        $otpRecord = DB::table('otps')
            ->where('phone', $request->phone)
            ->where('otp', $request->otp)
            ->first();

        if (!$otpRecord) {
            return back()->with('error', 'Invalid OTP');
        }

        $name  = session('reg_name');
        $email = session('reg_email');
        $phone = session('reg_phone');

        if (!$phone) {
            return back()->with('error', 'Session expired. Please register again.');
        }

        $role = \Spatie\Permission\Models\Role::where('name', 'sales')->first();
        $user = User::create([
            'name'     => $name,
            'email'    => $email,
            'phone'    => $phone,
            'password' => Hash::make($request->password),
            'role_id'  => $role ? $role->id : null,
            'status'   => 0, // initially inactive
        ]);

        if ($role) {
            $user->assignRole($role);
        }

        $sales = SalesExecutive::create([
            'user_id'  => $user->id,
            'status'   => 0,

        ]);

        // Create notification for admin
        Notification::create([
            'type' => 'sales_executive_registration',
            'title' => 'New Sales Executive Registration',
            'message' => "A new sales executive '{$name}' has registered and is waiting for approval.",
            'related_id' => $sales->id,
            'related_type' => 'App\Models\SalesExecutive',
            'is_read' => false,
        ]);

        DB::table('otps')->where('phone', $phone)->delete();
        session()->forget(['reg_name', 'reg_email', 'reg_phone']);

        Auth::guard('sales')->login($user);

        // $headerLogo = HeaderLogo::first();
        // $logos      = $headerLogo;

        return redirect()->route('sales.login')->with('success', 'Registration successful plz wait for admin verification !');
    }


    // DASHBOARD ---------------------
    public function dashboard()
    {
        $headerLogo = HeaderLogo::first();
        $logos      = HeaderLogo::first();

        $salesExecutive = auth('sales')->user();
        $salesExecutiveId = $salesExecutive->id;

        // Get income_per_target from sales executive
        $incomePerTarget = $salesExecutive->income_per_target ?? 0;

        // Calculate total institutions
        $totalInstitutions = InstitutionManagement::where('added_by', $salesExecutiveId)->count();

        // Calculate total students
        $totalStudents = User::where('added_by', $salesExecutiveId)->where('status', 1)->count();

        // Calculate today's students
        $todayStudents = User::where('added_by', $salesExecutiveId)->where('status', 1)
            ->whereDate('created_at', Carbon::today())
            ->count();

        // Calculate total classes (sum of classes from all institutions added by this sales executive)
        $institutionIds = InstitutionManagement::where('added_by', $salesExecutiveId)->pluck('id');


        // Calculate total blocks (distinct blocks from institutions)
        $totalBlocks = InstitutionManagement::where('added_by', $salesExecutiveId)
            ->whereNotNull('block_id')
            ->distinct('block_id')
            ->count('block_id');

        $incomePerTarget = \App\Models\Setting::getValue('default_income_per_target', 10);

        // Calculate earnings from student enrollments (Count * Rate)
        $totalEarning = $totalStudents * $incomePerTarget;
        $todayEarning = $todayStudents * $incomePerTarget;

        // Add other wallet credits (excluding student commissions to avoid double counting)
        $otherCreditsQuery = \App\Models\WalletTransaction::where('user_id', $salesExecutiveId)
            ->where('type', 'credit')
            ->where('description', 'NOT LIKE', 'Commission for Student%')
            ->where('description', 'NOT LIKE', 'Refund%');

        $totalEarning += (clone $otherCreditsQuery)->sum('amount');
        $todayEarning += (clone $otherCreditsQuery)->whereDate('created_at', Carbon::today())->sum('amount');

        // Prepare graph data for last 30 days
        $days = 30;
        $startDate = Carbon::now()->subDays($days - 1)->startOfDay();

        $dates = [];
        $dateKeys = [];
        for ($i = 0; $i < $days; $i++) {
            $date = Carbon::now()->subDays($days - 1 - $i);
            $dates[] = $date->format('d M');
            $dateKeys[] = $date->format('Y-m-d');
        }

        $studentData = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('added_by', $salesExecutiveId)
            ->whereDate('created_at', '>=', $startDate)
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $institutionData = InstitutionManagement::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('added_by', $salesExecutiveId)
            ->whereDate('created_at', '>=', $startDate)
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $studentsCount = [];
        $institutionsCount = [];
        foreach ($dateKeys as $dateKey) {
            $studentsCount[] = $studentData[$dateKey] ?? 0;
            $institutionsCount[] = $institutionData[$dateKey] ?? 0;
        }

        $earningsData = [];
        foreach ($dateKeys as $dateKey) {
            $dailyStudentCount = $studentData[$dateKey] ?? 0;

            $dailyOtherCredits = \App\Models\WalletTransaction::where('user_id', $salesExecutiveId)
                ->where('type', 'credit')
                ->where('description', 'NOT LIKE', 'Commission for Student%')
                ->where('description', 'NOT LIKE', 'Refund%')
                ->whereDate('created_at', $dateKey)
                ->sum('amount');

            $earningsData[] = ($dailyStudentCount * $incomePerTarget) + $dailyOtherCredits;
        }

        return view('sales.dashboard', compact(
            'headerLogo',
            'logos',
            'totalInstitutions',
            'totalStudents',
            'todayStudents',
            'totalEarning',
            'todayEarning',
            'incomePerTarget',
            'dates',
            'studentsCount',
            'institutionsCount',
            'earningsData'
        ), [
            'user' => $salesExecutive
        ]);
    }

    // LOGOUT ---------------------
    public function logout(Request $request)
    {
        Auth::guard('sales')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/sales/login');
    }
}
