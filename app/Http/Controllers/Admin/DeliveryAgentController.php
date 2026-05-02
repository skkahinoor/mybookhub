<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\DeliveryAgent;
use App\Models\User;
use App\Models\Country;
use App\Models\District;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class DeliveryAgentController extends Controller
{
    public function index(Request $request)
    {
        // if (!Auth::guard('admin')->user()->can('view_delivery_agents')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'view_delivery_agents');
        $title = 'Delivery Agents';

        if ($request->ajax()) {
            $data = User::role('delivery_agent', 'web')->with('deliveryAgent')->orderBy('id', 'desc');

            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        return '<span class="badge bg-success status-icon text-white"><i class="mdi mdi-check"></i></span>';
                    } else {
                        return '<span class="badge bg-danger status-icon text-white"><i class="mdi mdi-close"></i></span>';
                    }
                })
                ->addColumn('actions', function ($row) {
                    return '<div class="d-flex align-items-center" style="gap: 10px;">
                                <a href="javascript:void(0)" class="view-delivery-agent"
                                    data-id="' . $row->id . '" title="View Details">
                                    <i style="font-size: 20px; color: #a71d84;" class="mdi mdi-eye"></i>
                                </a>
                                <a href="' . route('delivery_agents.add_edit', $row->id) . '"
                                    title="Edit">
                                    <i style="font-size: 20px" class="mdi mdi-pencil"></i>
                                </a>
                                <a href="' . route('delivery_agents.delete', $row->id) . '"
                                    title="Delete"
                                    onclick="return confirm(\'Delete this delivery agent?\');">
                                    <i style="font-size: 20px; color: #e74c3c;"
                                        class="mdi mdi-delete"></i>
                                </a>
                            </div>';
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }

        return view('admin.delivery_agents.index', compact('title', 'logos', 'headerLogo'));
    }

    public function addEdit(Request $request, $id = null)
    {
        Session::put('page', 'view_delivery_agents');
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();

        $deliveryAgent = null;
        $user = null;
        $isEdit = false;

        if (! empty($id)) {
            // if (!Auth::guard('admin')->user()->can('edit_delivery_agents')) {
            //     abort(403, 'Unauthorized action.');
            // }
            $user = User::findOrFail($id);
            $deliveryAgent = $user->deliveryAgent;
            $isEdit = true;
        } else {
            // if (!Auth::guard('admin')->user()->can('add_delivery_agents')) {
            //     abort(403, 'Unauthorized action.');
            // }
        }

        if ($request->isMethod('post')) {
            $data = $request->all();

            $userId = $isEdit ? $user->id : null;
            $emailRule = 'required|email|unique:users,email';
            if ($userId) {
                $emailRule .= ',' . $userId;
            }

            $rules = [
                'name'  => 'required|string|max:255',
                'email' => $emailRule,
                'phone' => 'required|numeric',
                'district_id' => 'required|exists:districts,id',
            ];

            if (! $isEdit) {
                $rules['password'] = 'required|min:6|confirmed';
            }

            $request->validate($rules);

            $userData = [
                'name'   => $data['name'],
                'email'  => $data['email'],
                'phone'  => $data['phone'],
                'district_id' => $data['district_id'],
                'status' => 1,
            ];

            if (!$isEdit) {
                $userData['password'] = Hash::make($data['password']);
                
                // Ensure role exists
                $role = Role::firstOrCreate(
                    ['name' => 'delivery_agent', 'guard_name' => 'web']
                );
                
                $userData['role_id'] = $role->id;

                $user = User::create($userData);
                $user->assignRole($role);
            } else {
                if (!empty($data['password'])) {
                    $userData['password'] = Hash::make($data['password']);
                }
                $user->update($userData);
            }

            $profileData = [
                'vehicle_type' => $data['vehicle_type'] ?? null,
                'license_number' => $data['license_number'] ?? null,
                'status' => 1,
                'user_id' => $user->id,
            ];

            $profile = DeliveryAgent::where('user_id', $user->id)->first();
            if ($profile) {
                $profile->update($profileData);
            } else {
                DeliveryAgent::create($profileData);
            }

            return redirect()->route('delivery_agents.index')->with('success_message', 'Delivery Agent saved successfully!');
        }

        $districts = District::orderBy('name', 'asc')->get();
        return view('admin.delivery_agents.add_edit', compact('deliveryAgent', 'user', 'logos', 'headerLogo', 'districts'));
    }

    public function delete($id)
    {
        // if (!Auth::guard('admin')->user()->can('delete_delivery_agents')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $user = User::findOrFail($id);
        DeliveryAgent::where('user_id', $id)->delete();
        $user->delete();

        return redirect()->back()->with('success_message', 'Delivery Agent deleted successfully!');
    }

    public function updateStatus(Request $request)
    {
        // if (!Auth::guard('admin')->user()->can('update_delivery_agent_status')) {
        //     return response()->json(['status' => 'error', 'message' => 'Unauthorized action.'], 403);
        // }

        if (! $request->ajax()) {
            abort(400, 'Invalid request');
        }

        $data = $request->validate([
            'status'             => 'required|string|in:Active,Inactive',
            'delivery_agent_id'  => 'required|integer',
        ]);

        $newStatus = $data['status'] === 'Active' ? 1 : 0;
        $userId = $data['delivery_agent_id'];

        User::where('id', $userId)->update(['status' => $newStatus]);
        DeliveryAgent::where('user_id', $userId)->update(['status' => $newStatus]);

        return response()->json([
            'status'             => $newStatus,
            'delivery_agent_id' => $userId,
        ]);
    }

    public function getDetails($id)
    {
        $user = User::with('deliveryAgent', 'district')->findOrFail($id);
        $profile = $user->deliveryAgent;

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'district' => $user->district ? $user->district->name : 'N/A',
                'vehicle_type' => $profile ? $profile->vehicle_type : 'N/A',
                'license_number' => $profile ? $profile->license_number : 'N/A',
                'status' => $user->status,
                'created_at' => $user->created_at->format('M d, Y h:i A'),
            ]
        ]);
    }
}
