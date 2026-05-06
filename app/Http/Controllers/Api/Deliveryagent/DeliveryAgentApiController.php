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

class DeliveryAgentApiController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|numeric|unique:users,phone',
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

        DB::beginTransaction();
        try {
            $role = Role::firstOrCreate(
                ['name' => 'delivery_agent', 'guard_name' => 'web']
            );

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'district_id' => $request->district_id,
                'block_id' => $request->block_id,
                'role_id' => $role->id,
                'status' => 0, // Pending approval
            ]);

            $user->assignRole($role);

            // Debugging: You can check if files exist in the request
            // Log::info($request->allFiles()); 

            $idProofName = null;
            if ($request->hasFile('id_proof')) {
                $file = $request->file('id_proof');
                if ($file->isValid()) {
                    $idProofName = 'id_' . time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/delivery_agents/docs'), $idProofName);
                }
            }

            $licenseImageName = null;
            if ($request->hasFile('license_image')) {
                $file = $request->file('license_image');
                if ($file->isValid()) {
                    $licenseImageName = 'license_' . time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/delivery_agents/docs'), $licenseImageName);
                }
            }

            DeliveryAgent::create([
                'user_id' => $user->id,
                'vehicle_type' => $request->vehicle_type,
                'license_number' => $request->license_number,
                'id_proof' => $idProofName,
                'license_image' => $licenseImageName,
                'status' => 0,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Registration successful! Please wait for admin approval.',
                'data' => $user
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
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
            'data' => $user->load('deliveryAgent')
        ]);
    }

    public function getProfile(Request $request)
    {
        $user = $request->user()->load('deliveryAgent', 'country', 'state', 'district', 'block');
        
        $stats = [
            'total_trips' => \App\Models\Order::where('delivery_agent_id', $user->id)->where('order_status', 'Delivered')->count(),
            'today_trips' => \App\Models\Order::where('delivery_agent_id', $user->id)->where('order_status', 'Delivered')->whereDate('updated_at', \Carbon\Carbon::today())->count(),
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
            // Add dynamic labels or formatting if needed
            $activeTrip->pickup_points = \App\Models\OrdersProduct::where('order_id', $activeTrip->id)
                ->join('vendors_business_details', 'orders_products.vendor_id', '=', 'vendors_business_details.vendor_id')
                ->select('orders_products.*', 'vendors_business_details.shop_name', 'vendors_business_details.latitude', 'vendors_business_details.longitude')
                ->get();
            
            $activeTrip->drop_location = [
                'name' => $activeTrip->name,
                'address' => $activeTrip->address,
                'latitude' => $activeTrip->latitude,
                'longitude' => $activeTrip->longitude,
                'mobile' => $activeTrip->mobile
            ];
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

        $newStatus = !$profile->is_online;
        $profile->update(['is_online' => $newStatus]);

        return response()->json([
            'status' => true,
            'message' => $newStatus ? 'You are now Online' : 'You are now Offline',
            'is_online' => $newStatus
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
                $file->move(public_path('uploads/delivery_agents/profiles'), $imageName);
                $userData['profile_image'] = $imageName;
            }

            $user->update($userData);

            // Update Delivery Agent Details
            if ($profile) {
                $profile->update([
                    'vehicle_type' => $request->vehicle_type,
                    'license_number' => $request->license_number,
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

    public function getEarnings(Request $request)
    {
        $user = $request->user();
        $query = \App\Models\Order::where('delivery_agent_id', $user->id)
            ->where('order_status', 'Delivered');

        // Apply Date Filters
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('updated_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        $orders = $query->orderBy('updated_at', 'desc')->get();
        $total_earnings = $orders->sum('agent_trip_earning');
        $total_distance = $orders->sum('total_trip_distance');

        return response()->json([
            'status' => true,
            'total_earnings' => $total_earnings,
            'total_distance' => $total_distance,
            'count' => $orders->count(),
            'data' => $orders
        ]);
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

        // Get orders in the agent's district that are ready for delivery
        // We assume 'Approved' or 'Processing' status is ready for pickup
        $orders = \App\Models\Order::with(['orders_products.vendor', 'orders_products.vendor_details', 'user'])
            ->whereNull('delivery_agent_id') // Only unassigned orders
            ->whereNotIn('id', $rejectedIds) // Exclude orders this agent rejected
            ->where(function($query) use ($districtId) {
                $query->where('district_id', $districtId)
                      ->orWhereHas('user', function($q) use ($districtId) {
                          $q->where('district_id', $districtId);
                      });
            })
            ->whereIn('order_status', ['New', 'Approved', 'Processing', 'Paid', 'Pending'])
            ->orderBy('id', 'desc')
            ->get();

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
}
