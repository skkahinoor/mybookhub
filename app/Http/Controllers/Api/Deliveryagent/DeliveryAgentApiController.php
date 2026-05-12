<?php

namespace App\Http\Controllers\Api\Deliveryagent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DeliveryAgent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;

class DeliveryAgentApiController extends Controller
{
    public function sendSMS($phone, $otp)
    {
        $to = '91' . preg_replace('/[^0-9]/', '', $phone);

        try {
            $client = new Client();

            // payload must match MSG91 flow template variables
            $payload = [
                "template_id" => env('MSG91_TEMPLATE_ID'),
                "recipients" => [
                    [
                        "mobiles" => $to,
                        "var1"    => $otp
                    ]
                ]
            ];

            Log::info("MSG91 Delivery Agent OTP Payload", $payload);

            $response = $client->post("https://control.msg91.com/api/v5/flow/", [
                'json' => $payload,
                'headers' => [
                    'accept' => 'application/json',
                    'authkey' => env('MSG91_AUTH_KEY'),
                    'content-type' => 'application/json'
                ],
            ]);

            Log::info("MSG91 Delivery Agent OTP Response", [
                'status' => $response->getStatusCode(),
                'body' => $response->getBody()->getContents()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("MSG91 Delivery Agent OTP ERROR: " . $e->getMessage());
            return false;
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|numeric',
            'password' => 'required|min:6|confirmed',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'district_id' => 'required|exists:districts,id',
            'block_id' => 'nullable|exists:blocks,id',
            'vehicle_type' => 'nullable|string',
            'license_number' => 'nullable|string',
            'id_proof' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'license_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $existingUser = User::where('phone', $request->phone)->orWhere('email', $request->email)->first();
        if ($existingUser) {
            if ($existingUser->hasRole('delivery_agent')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation Error',
                    'errors' => ['phone' => ['This mobile number or email is already registered as a Delivery Agent.']]
                ], 422);
            }
        }

        try {
            // Handle File Uploads temporarily
            $idProofName = null;
            if ($request->hasFile('id_proof')) {
                $file = $request->file('id_proof');
                $idProofName = 'temp_id_' . time() . '_' . $request->phone . '.' . $file->getClientOriginalExtension();
                $destPath = public_path('uploads/delivery_agents/docs');
                if (!file_exists($destPath)) {
                    mkdir($destPath, 0777, true);
                }
                $file->move($destPath, $idProofName);
            }

            $licenseImageName = null;
            if ($request->hasFile('license_image')) {
                $file = $request->file('license_image');
                $licenseImageName = 'temp_license_' . time() . '_' . $request->phone . '.' . $file->getClientOriginalExtension();
                $destPath = public_path('uploads/delivery_agents/docs');
                if (!file_exists($destPath)) {
                    mkdir($destPath, 0777, true);
                }
                $file->move($destPath, $licenseImageName);
            }

            // Store registration data in cache
            $regData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'district_id' => $request->district_id,
                'block_id' => $request->block_id,
                'vehicle_type' => $request->vehicle_type,
                'license_number' => $request->license_number,
                'id_proof' => $idProofName,
                'license_image' => $licenseImageName,
            ];

            Cache::put('da_reg_' . $request->phone, $regData, now()->addMinutes(15));

            $otp = rand(100000, 999999);
            DB::table('otps')->updateOrInsert(
                ['phone' => $request->phone],
                ['otp' => $otp, 'created_at' => now(), 'updated_at' => now()]
            );

            $sendStatus = $this->sendSMS($request->phone, $otp);

            if (!$sendStatus) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to send OTP via MSG91. Please try again.'
                ], 500);
            }

            return response()->json([
                'status' => true,
                'message' => 'OTP sent successfully to your mobile number.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'otp' => 'required'
        ]);

        $otpRecord = DB::table('otps')
            ->where('phone', $request->phone)
            ->where('otp', $request->otp)
            ->first();

        if (!$otpRecord) {
            return response()->json([
                "status" => false,
                "message" => "Invalid OTP"
            ], 400);
        }

        $regData = Cache::get('da_reg_' . $request->phone);

        if (!$regData) {
            return response()->json([
                'status' => false,
                'message' => 'Registration session expired. Please register again.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $role = Role::firstOrCreate(
                ['name' => 'delivery_agent', 'guard_name' => 'web']
            );

            $user = User::where('phone', $regData['phone'])->orWhere('email', $regData['email'])->first();

            if ($user) {
                // Update existing user with new location info if they are becoming a delivery agent
                $user->update([
                    'country_id' => $regData['country_id'],
                    'state_id' => $regData['state_id'],
                    'district_id' => $regData['district_id'],
                    'block_id' => $regData['block_id'],
                ]);
            } else {
                $user = User::create([
                    'name' => $regData['name'],
                    'email' => $regData['email'],
                    'phone' => $regData['phone'],
                    'password' => $regData['password'],
                    'country_id' => $regData['country_id'],
                    'state_id' => $regData['state_id'],
                    'district_id' => $regData['district_id'],
                    'block_id' => $regData['block_id'],
                    'role_id' => $role->id,
                    'status' => 1, // Active by default, allowed to login
                ]);
            }

            if (!$user->hasRole('delivery_agent')) {
                $user->assignRole($role);
            }

            // Rename temp files to include user ID
            $docsPath = public_path('uploads/delivery_agents/docs');
            if (!file_exists($docsPath)) {
                mkdir($docsPath, 0777, true);
            }

            $idProofName = $regData['id_proof'];
            if ($idProofName && file_exists($docsPath . '/' . $idProofName)) {
                $newIdName = str_replace('temp_id_', 'id_', $idProofName);
                rename($docsPath . '/' . $idProofName, $docsPath . '/' . $newIdName);
                $idProofName = $newIdName;
            }

            $licenseImageName = $regData['license_image'];
            if ($licenseImageName && file_exists($docsPath . '/' . $licenseImageName)) {
                $newLicenseName = str_replace('temp_license_', 'license_', $licenseImageName);
                rename($docsPath . '/' . $licenseImageName, $docsPath . '/' . $newLicenseName);
                $licenseImageName = $newLicenseName;
            }

            DeliveryAgent::create([
                'user_id' => $user->id,
                'vehicle_type' => $regData['vehicle_type'],
                'license_number' => $regData['license_number'],
                'id_proof' => $idProofName,
                'license_image' => $licenseImageName,
                'status' => 1, // Active, but documents might still be pending
                'document_verify_status' => 0, // Default to pending verification
            ]);

            DB::commit();

            Cache::forget('da_reg_' . $request->phone);
            DB::table('otps')->where('phone', $request->phone)->delete();

            $this->sendRegistrationSuccessSMS($request->phone);

            return response()->json([
                'status' => true,
                'message' => 'Registration successful! Please wait for admin approval.',
                'data' => $user->load('deliveryAgent', 'country', 'state', 'district', 'block')
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'Verification failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sendRegistrationSuccessSMS($phone)
    {
        $to = '91' . preg_replace('/[^0-9]/', '', $phone);

        try {
            $client = new Client();

            $payload = [
                "template_id" => env('MSG91_REG_SUCCESS_TEMPLATE_ID'),
                "recipients" => [
                    [
                        "mobiles" => $to
                    ]
                ]
            ];

            Log::info("DA Registration Success SMS Payload", $payload);

            $client->post("https://control.msg91.com/api/v5/flow/", [
                'json' => $payload,
                'headers' => [
                    'accept' => 'application/json',
                    'authkey' => env('MSG91_AUTH_KEY'),
                    'content-type' => 'application/json'
                ],
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("DA Registration Success SMS ERROR: " . $e->getMessage());
            return false;
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required', // Email or Phone
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $loginInput = $request->login;

        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $loginInput)->first();
        } else {
            $numericLogin = preg_replace('/\D/', '', $loginInput);
            $user = User::where('phone', $numericLogin)->first();
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        if (!$user->hasRole('delivery_agent')) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Not a delivery agent account.'
            ], 403);
        }

        if ($user->status == 0) {
            return response()->json([
                'status' => false,
                'message' => 'Your account is pending approval or deactivated.'
            ], 403);
        }

        $token = $user->createToken('delivery-agent-token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token,
            'data' => $user->load('deliveryAgent', 'country', 'state', 'district', 'block')
        ]);
    }

    public function getProfile(Request $request)
    {
        $user = $request->user()->load('deliveryAgent', 'country', 'state', 'district', 'block');
        
        $stats = [
            'total_trips' => \App\Models\Order::where('delivery_agent_id', $user->id)->where('order_status', 'Delivered')->count(),
            'today_trips' => \App\Models\Order::where('delivery_agent_id', $user->id)->where('order_status', 'Delivered')->whereDate('updated_at', \Carbon\Carbon::today())->count(),
            'total_earnings' => (float)\App\Models\Order::where('delivery_agent_id', $user->id)->where('order_status', 'Delivered')->sum('agent_trip_earning'),
            'rating' => 4.8, // Mocked for now
            'agent_rate_per_km' => \App\Models\DeliverySetting::where('status', 1)->first()->agent_rate_per_km ?? 10.00
        ];

        // Get active trip if exists
        $activeTrip = \App\Models\Order::where('delivery_agent_id', $user->id)
            ->whereNotIn('order_status', ['Delivered', 'Cancelled'])
            ->with('orders_products') // Include products if needed
            ->first();

        // Standardize the response to include pickup/drop details for the app
        if ($activeTrip) {
            // Load same relations as getAvailableOrders
            $activeTrip->load(['orders_products.vendor', 'orders_products.vendor_details.vendorbusinessdetails', 'orders_products.vendor_details.user', 'user']);

            // Drop Location (Buyer)
            $dropLocation = [
                'name' => $activeTrip->name,
                'address' => $activeTrip->address . ', ' . $activeTrip->city . ', ' . $activeTrip->state . ' - ' . $activeTrip->pincode,
                'mobile' => $activeTrip->mobile,
                'latitude' => $activeTrip->latitude ?? ($activeTrip->user ? $activeTrip->user->latitude : null),
                'longitude' => $activeTrip->longitude ?? ($activeTrip->user ? $activeTrip->user->longitude : null),
            ];

            // Pickup Locations (Sellers)
            $pickups = $activeTrip->orders_products->map(function($item) {
                $vendorProfile = $item->vendor_details; 
                $sellerUser = $vendorProfile ? $vendorProfile->user : null;
                $business = $vendorProfile ? $vendorProfile->vendorbusinessdetails : null;

                $lat = null;
                $lng = null;
                if ($vendorProfile && $vendorProfile->location) {
                    $coords = explode(',', $vendorProfile->location);
                    if (count($coords) == 2) {
                        $lat = trim($coords[0]);
                        $lng = trim($coords[1]);
                    }
                }

                // If it is not a vendor product, fetch seller details from ProductsAttribute (for student product)
                if (!$vendorProfile || $item->vendor_id == 0) {
                    $attr = $item->product_attribute;
                    if ($attr && $attr->user_id > 0) {
                        $sellerUser = \App\Models\User::find($attr->user_id);
                    }
                }

                return [
                    'seller_name' => $sellerUser ? $sellerUser->name : 'N/A',
                    'shop_name' => $business ? $business->shop_name : ($sellerUser ? $sellerUser->name : 'Individual Seller'),
                    'address' => $business ? $business->shop_address : ($sellerUser ? $sellerUser->address : 'N/A'),
                    'mobile' => $business ? $business->shop_mobile : ($sellerUser ? $sellerUser->phone : 'N/A'),
                    'latitude' => $lat ?? ($business ? $business->latitude : ($sellerUser ? $sellerUser->latitude : null)),
                    'longitude' => $lng ?? ($business ? $business->longitude : ($sellerUser ? $sellerUser->longitude : null)),
                    'product_name' => $item->product_name,
                    'product_qty' => $item->product_qty,
                    'product_price' => $item->product_price,
                ];
            });

            $activeTripFormatted = [
                'order_id' => $activeTrip->id,
                'grand_total' => $activeTrip->grand_total,
                'payment_method' => $activeTrip->payment_method,
                'order_status' => $activeTrip->order_status,
                'created_at' => $activeTrip->created_at->format('M d, Y h:i A'),
                'buyer_details' => [
                    'id' => $activeTrip->user_id,
                    'name' => $activeTrip->user ? $activeTrip->user->name : $activeTrip->name,
                    'email' => $activeTrip->user ? $activeTrip->user->email : $activeTrip->email,
                ],
                'pickup_points' => $pickups,
                'drop_location' => $dropLocation,
                'agent_start_lat' => $activeTrip->agent_start_lat,
                'agent_start_lng' => $activeTrip->agent_start_lng,
                'agent_rate_at_trip' => $activeTrip->agent_rate_at_trip,
            ];

            $activeTrip = $activeTripFormatted;
        }

        return response()->json([
            'status' => true,
            'data' => $user,
            'stats' => $stats,
            'active_trip' => $activeTrip
        ]);
    }

    public function toggleOnline(Request $request)
    {
        $user = $request->user();
        $profile = $user->deliveryAgent;

        if (!$profile) {
            return response()->json([
                'status' => false,
                'message' => 'Delivery agent profile not found.'
            ], 404);
        }

        // Check if account is active
        if (!$profile->is_online && ($user->status == 0 || $profile->status == 0)) {
            return response()->json([
                'status' => false,
                'error_type' => 'account_deactivated',
                'message' => 'Your account has been deactivated by the admin. You cannot go online.'
            ], 403);
        }

        // Check if documents are verified before allowing to go online
        if (!$profile->is_online && $profile->document_verify_status != 1) {
            return response()->json([
                'status' => false,
                'error_type' => 'documents_pending',
                'message' => 'Your submitted documents are pending review. You may go online after they are verified by the admin.'
            ], 403);
        }

        $newStatus = !$profile->is_online;
        $profile->update(['is_online' => $newStatus]);

        return response()->json([
            'status' => true,
            'message' => $newStatus ? 'You are now Online' : 'You are now Offline',
            'is_online' => $newStatus
        ]);
    }

    public function submitContactQuery(Request $request)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = \App\Models\DeliveryAgentContactQuery::create([
            'user_id' => $user->id,
            'subject' => $request->subject,
            'status' => 'Open',
        ]);

        \App\Models\DeliveryAgentContactQueryMessage::create([
            'query_id' => $query->id,
            'sender_type' => 'agent',
            'message' => $request->message,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Your query has been submitted successfully.'
        ]);
    }

    public function getContactQueries(Request $request)
    {
        $user = $request->user();
        $queries = \App\Models\DeliveryAgentContactQuery::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => true,
            'data' => $queries
        ]);
    }

    public function getQueryMessages(Request $request, $id)
    {
        $user = $request->user();
        $query = \App\Models\DeliveryAgentContactQuery::where('user_id', $user->id)->where('id', $id)->first();
        
        if (!$query) {
            return response()->json(['status' => false, 'message' => 'Query not found'], 404);
        }

        $messages = \App\Models\DeliveryAgentContactQueryMessage::where('query_id', $id)->orderBy('created_at', 'asc')->get();

        return response()->json([
            'status' => true,
            'query' => $query,
            'messages' => $messages
        ]);
    }

    public function replyContactQuery(Request $request, $id)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Message is required'], 422);
        }

        $query = \App\Models\DeliveryAgentContactQuery::where('user_id', $user->id)->where('id', $id)->first();
        
        if (!$query) {
            return response()->json(['status' => false, 'message' => 'Query not found'], 404);
        }

        if ($query->status === 'Closed' || $query->status === 'Solved') {
            return response()->json(['status' => false, 'message' => 'Cannot reply to a closed/solved query'], 400);
        }

        $message = \App\Models\DeliveryAgentContactQueryMessage::create([
            'query_id' => $query->id,
            'sender_type' => 'agent',
            'message' => $request->message,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Reply sent successfully.',
            'data' => $message
        ]);
    }

    public function closeContactQuery(Request $request, $id)
    {
        $user = $request->user();
        $query = \App\Models\DeliveryAgentContactQuery::where('user_id', $user->id)->where('id', $id)->first();
        
        if (!$query) {
            return response()->json(['status' => false, 'message' => 'Query not found'], 404);
        }

        $query->update(['status' => 'Closed']);

        return response()->json([
            'status' => true,
            'message' => 'Query closed successfully.',
            'query' => $query
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $profile = $user->deliveryAgent;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|numeric|unique:users,phone,' . $user->id,
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'district_id' => 'nullable|exists:districts,id',
            'block_id' => 'nullable|exists:blocks,id',
            'address' => 'nullable|string|max:500',
            'pincode' => 'nullable|string|max:10',
            'vehicle_type' => 'nullable|string',
            'license_number' => 'nullable|string',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Update User Table
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'district_id' => $request->district_id,
                'block_id' => $request->block_id,
                'address' => $request->address,
                'pincode' => $request->pincode,
            ];

            if ($request->hasFile('profile_image')) {
                $file = $request->file('profile_image');
                $imageName = 'profile_' . time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
                $destPath = public_path('uploads/delivery_agents/profiles');
                if (!file_exists($destPath)) {
                    mkdir($destPath, 0777, true);
                }
                $file->move($destPath, $imageName);
                $userData['profile_image'] = $imageName;
            }

            $user->update($userData);

            // Update Delivery Agent Details
            if ($profile) {
                $profile->update([
                    'vehicle_type' => $request->vehicle_type,
                    'license_number' => $request->license_number,
                    'account_holder_name' => $request->account_holder_name,
                    'bank_name' => $request->bank_name,
                    'account_number' => $request->account_number,
                    'ifsc_code' => $request->ifsc_code,
                    'upi_id' => $request->upi_id,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Profile updated successfully!',
                'data' => $user->load('deliveryAgent', 'country', 'state', 'district', 'block')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function acceptOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Invalid Order ID'], 422);
        }

        $user = $request->user();
        
        // 1. Check if the agent already has an active trip (Not Delivered)
        $activeOrder = \App\Models\Order::where('delivery_agent_id', $user->id)
            ->whereNotIn('order_status', ['Delivered', 'Cancelled', 'Returned'])
            ->exists();

        if ($activeOrder) {
            return response()->json([
                'status' => false, 
                'message' => 'You already have an active delivery. Please complete it first.'
            ], 400);
        }

        $order = \App\Models\Order::find($request->order_id);

        // 2. Check if order is already assigned to someone else
        if ($order->delivery_agent_id != null) {
            return response()->json(['status' => false, 'message' => 'Order already accepted by another agent'], 400);
        }

        $rate = \App\Models\DeliverySetting::where('status', 1)->first()->agent_rate_per_km ?? 10.00;

        DB::beginTransaction();
        try {
            // Assign agent to order and save start details
            $order->update([
                'delivery_agent_id' => $user->id,
                'order_status' => 'Accepted',
                'agent_start_lat' => $request->latitude,
                'agent_start_lng' => $request->longitude,
                'agent_rate_at_trip' => $rate
            ]);

            // Reset agent's trip progress columns
            $user->deliveryAgent->update([
                'pickup_status' => 0,
                'drop_status' => 0
            ]);

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Order accepted successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function rejectOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Invalid Order ID'], 422);
        }

        $user = $request->user();
        $agent = $user->deliveryAgent;
        
        $rejectedIds = $agent->rejected_order_ids ?? [];
        if (!in_array($request->order_id, $rejectedIds)) {
            $rejectedIds[] = $request->order_id;
            $agent->update(['rejected_order_ids' => $rejectedIds]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Order rejected and hidden.'
        ]);
    }

    public function updateOrderStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'status' => 'required|in:Picked Up,Out for Delivery,Delivered',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Invalid status provided'], 422);
        }

        $user = $request->user();
        $order = \App\Models\Order::where('id', $request->order_id)
                                  ->where('delivery_agent_id', $user->id)
                                  ->first();

        if (!$order) {
            return response()->json(['status' => false, 'message' => 'Order not found or not assigned to you'], 404);
        }

        try {
            if ($request->status == 'Picked Up') {
                $user->deliveryAgent->update(['pickup_status' => 1]);
                $order->update(['order_status' => 'Picked Up']);
            } elseif ($request->status == 'Delivered') {
                $user->deliveryAgent->update(['drop_status' => 1]);
                $order->update(['order_status' => 'Delivered']);
                
                // Calculate Earning based on Distance
                if ($order->agent_start_lat && $order->agent_start_lng) {
                    $pickup = \App\Models\OrdersProduct::where('order_id', $order->id)->first(); 
                    $pickupLat = null;
                    $pickupLng = null;

                    if ($pickup->vendor_id > 0) {
                        $business = \App\Models\VendorsBusinessDetail::where('vendor_id', $pickup->vendor_id)->first();
                        if ($business) {
                            $pickupLat = $business->latitude;
                            $pickupLng = $business->longitude;
                        }
                    } else if ($pickup->admin_id > 0) {
                        $seller = \App\Models\User::find($pickup->admin_id);
                        if ($seller) {
                            $pickupLat = $seller->latitude;
                            $pickupLng = $seller->longitude;
                        }
                    }
                    
                    if ($pickupLat && $pickupLng) {
                        $d1 = $this->calculateDistancePHP(
                            (float)$order->agent_start_lat, (float)$order->agent_start_lng,
                            (float)$pickupLat, (float)$pickupLng
                        );
                        $d2 = $this->calculateDistancePHP(
                            (float)$pickupLat, (float)$pickupLng,
                            (float)$order->latitude, (float)$order->longitude
                        );
                        
                        $totalDistance = $d1 + $d2;
                        $earning = $totalDistance * (float)$order->agent_rate_at_trip;
                        
                        $order->update([
                            'total_trip_distance' => $totalDistance,
                            'agent_trip_earning' => $earning
                        ]);
                    }
                }
            } else {
                $order->update(['order_status' => $request->status]);
            }

            return response()->json(['status' => true, 'message' => 'Status updated to ' . $request->status]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    private function calculateDistancePHP($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    public function getHistory(Request $request)
    {
        $user = $request->user();
        $orders = \App\Models\Order::where('delivery_agent_id', $user->id)
            ->whereIn('order_status', ['Delivered', 'Cancelled'])
            ->with(['orders_products'])
            ->orderBy('id', 'desc')
            ->get();

        foreach ($orders as $order) {
            $order->pickup_points = \App\Models\OrdersProduct::where('order_id', $order->id)
                ->join('vendors_business_details', 'orders_products.vendor_id', '=', 'vendors_business_details.vendor_id')
                ->select('orders_products.*', 'vendors_business_details.shop_name', 'vendors_business_details.latitude', 'vendors_business_details.longitude')
                ->get();
            
            $order->drop_location = [
                'name' => $order->name,
                'address' => $order->address,
                'latitude' => $order->latitude,
                'longitude' => $order->longitude,
                'mobile' => $order->mobile
            ];
        }

        return response()->json([
            'status' => true,
            'data' => $orders
        ]);
    }

    public function getOrderDetails(Request $request, $id)
    {
        $order = \App\Models\Order::with(['orders_products'])->find($id);
        
        if (!$order) {
            return response()->json(['status' => false, 'message' => 'Order not found'], 404);
        }

        // Add pickup points and drop location as standardized before
        $pickupPoints = [];
        foreach ($order->orders_products as $op) {
            $point = [
                'product_name' => $op->product_name,
                'shop_name' => '',
                'latitude' => null,
                'longitude' => null,
                'shop_address' => '',
                'shop_mobile' => '',
            ];

            if ($op->vendor_id > 0) {
                $business = \App\Models\VendorsBusinessDetail::where('vendor_id', $op->vendor_id)->first();
                if ($business) {
                    $point['shop_name'] = $business->shop_name;
                    $point['latitude'] = $business->latitude;
                    $point['longitude'] = $business->longitude;
                    $point['shop_address'] = $business->shop_address;
                    $point['shop_mobile'] = $business->shop_mobile;
                }
            } else if ($op->admin_id > 0) {
                $seller = \App\Models\User::find($op->admin_id);
                if ($seller) {
                    $point['shop_name'] = $seller->name . " (Student Seller)";
                    $point['latitude'] = $seller->latitude;
                    $point['longitude'] = $seller->longitude;
                    $point['shop_address'] = $seller->address;
                    $point['shop_mobile'] = $seller->phone;
                }
            }
            $pickupPoints[] = $point;
        }
        $order->pickup_points = $pickupPoints;
        
        $order->drop_location = [
            'name' => $order->name,
            'address' => $order->address,
            'latitude' => $order->latitude,
            'longitude' => $order->longitude,
            'mobile' => $order->mobile,
            'email' => $order->email,
            'pincode' => $order->pincode
        ];

        return response()->json([
            'status' => true,
            'data' => $order
        ]);
    }

    public function getCountries()
    {
        $countries = \App\Models\Country::where('status', 1)->get(['id', 'name']);
        return response()->json([
            'status' => true,
            'data' => $countries
        ]);
    }

    public function getStates(Request $request)
    {
        $states = \App\Models\State::where('country_id', $request->country_id)
            ->where('status', 1)
            ->get(['id', 'name']);
        return response()->json([
            'status' => true,
            'data' => $states
        ]);
    }

    public function getDistricts(Request $request)
    {
        $districts = \App\Models\District::where('state_id', $request->state_id)
            ->where('status', 1)
            ->get(['id', 'name']);
        return response()->json([
            'status' => true,
            'data' => $districts
        ]);
    }

    public function getBlocks(Request $request)
    {
        $blocks = \App\Models\Block::where('district_id', $request->district_id)
            ->where('status', 1)
            ->get(['id', 'name']);
        return response()->json([
            'status' => true,
            'data' => $blocks
        ]);
    }

    public function getAvailableOrders(Request $request)
    {
        $user = $request->user();
        $districtId = $user->district_id;

        $profile = $user->deliveryAgent;
        $rejectedIds = $profile->rejected_order_ids ?? [];

        if (!$districtId) {
            return response()->json([
                'status' => false,
                'message' => 'Please set your district in profile first.'
            ], 400);
        }

        if (!$profile || !$profile->is_online) {
            return response()->json([
                'status' => false,
                'message' => 'You are currently offline. Please go online to see new orders.',
                'data' => []
            ]);
        }

        $lat = $request->latitude;
        $lng = $request->longitude;
        $radius = $request->radius ?? 30; // Default 30km radius

        // Get orders ready for delivery
        $query = \App\Models\Order::with([
            'orders_products.vendor', 
            'orders_products.vendor_details.user', 
            'orders_products.vendor_details.vendorbusinessdetails', 
            'user'
        ])
        ->whereNull('delivery_agent_id')
        ->whereNotIn('id', $rejectedIds)
        ->whereIn('order_status', ['New', 'Approved', 'Processing', 'Paid', 'Pending']);

        // Hybrid matching: Either in same district OR within radius of coordinates
        $query->where(function($q) use ($districtId, $lat, $lng, $radius) {
            $q->where('district_id', $districtId)
              ->orWhereHas('user', function($sub) use ($districtId) {
                  $sub->where('district_id', $districtId);
              });

            if ($lat && $lng) {
                // Haversine formula to find orders within radius
                $q->orWhereRaw("( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) < ?", [$lat, $lng, $lat, $radius]);
            }
        });

        $orders = $query->orderBy('id', 'desc')->get();

        $formattedOrders = $orders->map(function($order) {
            // Drop Location (Buyer)
            $dropLocation = [
                'name' => $order->name,
                'address' => $order->address . ', ' . $order->city . ', ' . $order->state . ' - ' . $order->pincode,
                'mobile' => $order->mobile,
                'latitude' => $order->latitude ?? ($order->user ? $order->user->latitude : null),
                'longitude' => $order->longitude ?? ($order->user ? $order->user->longitude : null),
            ];

            // Pickup Locations (Sellers) - There can be multiple sellers per order
            $pickups = $order->orders_products->map(function($item) {
                // vendor_details is the relationship to the Vendor model
                $vendorProfile = $item->vendor_details; 
                // The actual user who owns this vendor profile
                $sellerUser = $vendorProfile ? $vendorProfile->user : null;
                // The business/shop details
                $business = $vendorProfile ? $vendorProfile->vendorbusinessdetails : null;

                // Handle coordinates from vendor 'location' field (comma separated lat,long)
                $lat = null;
                $lng = null;
                if ($vendorProfile && $vendorProfile->location) {
                    $coords = explode(',', $vendorProfile->location);
                    if (count($coords) == 2) {
                        $lat = trim($coords[0]);
                        $lng = trim($coords[1]);
                    }
                }

                return [
                    'seller_name' => $sellerUser ? $sellerUser->name : 'N/A',
                    'shop_name' => $business ? $business->shop_name : 'Individual Seller',
                    'address' => $business ? $business->shop_address : ($sellerUser ? $sellerUser->address : 'N/A'),
                    'mobile' => $business ? $business->shop_mobile : ($sellerUser ? $sellerUser->phone : 'N/A'),
                    'latitude' => $lat ?? ($business ? $business->latitude : ($sellerUser ? $sellerUser->latitude : null)),
                    'longitude' => $lng ?? ($business ? $business->longitude : ($sellerUser ? $sellerUser->longitude : null)),
                    'product_name' => $item->product_name,
                    'product_qty' => $item->product_qty,
                    'product_price' => $item->product_price,
                ];
            });

            return [
                'order_id' => $order->id,
                'grand_total' => $order->grand_total,
                'payment_method' => $order->payment_method,
                'order_status' => $order->order_status,
                'created_at' => $order->created_at->format('M d, Y h:i A'),
                'buyer_details' => [
                    'id' => $order->user_id,
                    'name' => $order->user ? $order->user->name : $order->name,
                    'email' => $order->user ? $order->user->email : $order->email,
                ],
                'pickup_points' => $pickups,
                'drop_location' => $dropLocation
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $formattedOrders
        ]);
    }

    public function getAgentEarningsData(Request $request)
    {
        $user = $request->user();
        $profile = $user->deliveryAgent;
        
        $totalEarnings = \App\Models\Order::where('delivery_agent_id', $user->id)
            ->where('order_status', 'Delivered')
            ->sum('agent_trip_earning');

        $paidEarnings = \App\Models\DeliveryAgentPayout::where('delivery_agent_id', $profile->id)
            ->where('status', 'Approved')
            ->sum('amount');

        $pendingPayouts = \App\Models\DeliveryAgentPayout::where('delivery_agent_id', $profile->id)
            ->where('status', 'Pending')
            ->sum('amount');

        $availableToRequest = $totalEarnings - $paidEarnings - $pendingPayouts;

        // Recent earnings list
        $recentEarnings = \App\Models\Order::where('delivery_agent_id', $user->id)
            ->where('order_status', 'Delivered')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get(['id', 'agent_trip_earning', 'updated_at', 'total_trip_distance']);

        return response()->json([
            'status' => true,
            'total_earnings' => (float)$totalEarnings,
            'paid_payout' => (float)$paidEarnings,
            'pending_payout' => (float)$pendingPayouts,
            'available_payout' => (float)$availableToRequest,
            'total_distance' => (float)\App\Models\Order::where('delivery_agent_id', $user->id)->where('order_status', 'Delivered')->sum('total_trip_distance'),
            'count' => \App\Models\Order::where('delivery_agent_id', $user->id)->where('order_status', 'Delivered')->count(),
            'agent' => $profile,
            'data' => $recentEarnings
        ]);
    }

    public function requestPayout(Request $request)
    {
        $user = $request->user();
        $profile = $user->deliveryAgent;

        if (!$profile->account_number && !$profile->upi_id) {
            return response()->json([
                'status' => false,
                'message' => 'Please update your bank details or UPI ID first.'
            ], 400);
        }

        $totalEarnings = \App\Models\Order::where('delivery_agent_id', $user->id)
            ->where('order_status', 'Delivered')
            ->sum('agent_trip_earning');

        $paidEarnings = \App\Models\DeliveryAgentPayout::where('delivery_agent_id', $profile->id)
            ->where('status', 'Approved')
            ->sum('amount');

        $pendingPayouts = \App\Models\DeliveryAgentPayout::where('delivery_agent_id', $profile->id)
            ->where('status', 'Pending')
            ->sum('amount');

        $availableBalance = $totalEarnings - $paidEarnings - $pendingPayouts;

        if ($availableBalance <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'Insufficient balance to request payout.'
            ], 400);
        }

        \App\Models\DeliveryAgentPayout::create([
            'delivery_agent_id' => $profile->id,
            'amount' => $availableBalance,
            'status' => 'Pending'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Payout request submitted successfully!'
        ]);
    }

    public function getPayoutHistory(Request $request)
    {
        $user = $request->user();
        $profile = $user->deliveryAgent;

        $payouts = \App\Models\DeliveryAgentPayout::where('delivery_agent_id', $profile->id)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $payouts
        ]);
    }

    public function deleteAccount(Request $request)
    {
        $user = $request->user();
        
        // 1. Check for active orders
        $activeOrders = \App\Models\Order::where('delivery_agent_id', $user->id)
            ->whereNotIn('order_status', ['Delivered', 'Cancelled', 'Returned'])
            ->exists();

        if ($activeOrders) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot delete account with active orders. Please complete or cancel them first.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $profile = $user->deliveryAgent;
            
            // 2. Cleanup Delivery Agent Data
            if ($profile) {
                // Delete Payout History
                \App\Models\DeliveryAgentPayout::where('delivery_agent_id', $profile->id)->delete();

                // Delete Documents
                $docsPath = public_path('uploads/delivery_agents/docs/');
                if ($profile->id_proof && file_exists($docsPath . $profile->id_proof)) {
                    unlink($docsPath . $profile->id_proof);
                }
                if ($profile->license_image && file_exists($docsPath . $profile->license_image)) {
                    unlink($docsPath . $profile->license_image);
                }
                $profile->delete();
            }

            // 3. Cleanup User-related Data
            // Unlink Orders (Set agent_id to null for historical records)
            \App\Models\Order::where('delivery_agent_id', $user->id)->update([
                'delivery_agent_id' => null
            ]);

            // Delete Contact Queries and Messages
            $queries = \App\Models\DeliveryAgentContactQuery::where('user_id', $user->id)->get();
            foreach ($queries as $query) {
                \App\Models\DeliveryAgentContactQueryMessage::where('query_id', $query->id)->delete();
                $query->delete();
            }

            // Delete OTPs
            DB::table('otps')->where('phone', $user->phone)->delete();

            // Delete Profile Image
            if ($user->profile_image) {
                $profilePath = public_path('uploads/delivery_agents/profiles/');
                if (file_exists($profilePath . $user->profile_image)) {
                    unlink($profilePath . $user->profile_image);
                }
            }

            // 4. Revoke Tokens and Delete User
            $user->tokens()->delete();
            $user->delete();

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Account and all associated data permanently deleted successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'Deletion failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Account not found with this mobile number.'
            ], 404);
        }

        if (!$user->hasRole('delivery_agent')) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. This account is not a delivery agent.'
            ], 403);
        }

        try {
            $otp = rand(100000, 999999);
            DB::table('otps')->updateOrInsert(
                ['phone' => $request->phone],
                ['otp' => $otp, 'created_at' => now(), 'updated_at' => now()]
            );

            $sendStatus = $this->sendSMS($request->phone, $otp);

            if (!$sendStatus) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to send OTP. Please try again.'
                ], 500);
            }

            return response()->json([
                'status' => true,
                'message' => 'OTP sent successfully to your mobile number.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|numeric',
            'otp' => 'required|numeric',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $otpRecord = DB::table('otps')
            ->where('phone', $request->phone)
            ->where('otp', $request->otp)
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired OTP.'
            ], 400);
        }

        // Check if OTP is older than 15 mins
        $otpCreatedAt = \Carbon\Carbon::parse($otpRecord->created_at);
        if ($otpCreatedAt->addMinutes(15)->isPast()) {
            return response()->json([
                'status' => false,
                'message' => 'OTP has expired. Please request a new one.'
            ], 400);
        }

        try {
            $user = User::where('phone', $request->phone)->first();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Account not found.'
                ], 404);
            }

            $user->update([
                'password' => Hash::make($request->password)
            ]);

            DB::table('otps')->where('phone', $request->phone)->delete();

            return response()->json([
                'status' => true,
                'message' => 'Password reset successful. You can now login with your new password.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to reset password: ' . $e->getMessage()
            ], 500);
        }
    }
}
