<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SalesVendorController extends Controller
{

    private function checkAccess(Request $request, array $allowedRoles = ['sales'])
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $role = Role::find($user->role_id);

        if (!$role || !in_array($role->name, $allowedRoles)) {
            return response()->json([
                'status'  => false,
                'message' => 'Only Sales can access this.'
            ], 403);
        }

        if ($user->status != 1) {
            return response()->json([
                'status'  => false,
                'message' => 'Your account is inactive.'
            ], 403);
        }

        return null;
    }


    public function getVendor(Request $request)
    {
        if ($resp = $this->checkAccess($request)) {
            return $resp;
        }

        $vendors = Vendor::with('user')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $vendors
        ], 200);
    }

    public function storeVendor(Request $request)
    {
        if ($resp = $this->checkAccess($request)) {
            return $resp;
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'mobile'   => 'required|string|min:10|max:15|unique:users,phone',
            'password' => 'required|string|min:6',
        ]);

        DB::beginTransaction();

        try {

            $role = Role::where('name', 'vendor')->first();

            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'phone'    => $validated['mobile'],
                'password' => Hash::make($validated['password']),
                'role_id'  => $role ? $role->id : null,
                'status'   => 0,
            ]);

            if ($role) {
                $user->assignRole($role);
            }

            $vendor = Vendor::create([
                'user_id' => $user->id,
                'confirm' => 'Yes',
                'status'  => 0,
            ]);

            Notification::create([
                'type'         => 'vendor_added',
                'title'        => 'New Vendor Added',
                'message'      => "Sales team added vendor '{$validated['name']}' awaiting activation.",
                'related_id'   => $vendor->id,
                'related_type' => Vendor::class,
                'is_read'      => false,
            ]);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Vendor added successfully.',
                'data'    => $vendor->load('user')
            ], 201);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => 'Vendor creation failed.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function showVendor(Request $request, $id)
    {
        if ($resp = $this->checkAccess($request)) {
            return $resp;
        }

        $vendor = Vendor::with('user')->find($id);

        if (!$vendor) {
            return response()->json([
                'status'  => false,
                'message' => 'Vendor not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $vendor
        ], 200);
    }

    public function destroyVendor(Request $request, $id)
    {
        if ($resp = $this->checkAccess($request)) {
            return $resp;
        }

        $vendor = Vendor::find($id);

        if (!$vendor) {
            return response()->json([
                'status'  => false,
                'message' => 'Vendor not found.'
            ], 404);
        }

        if ($vendor->status == 1) {
            return response()->json([
                'status'  => false,
                'message' => 'Active vendors cannot be deleted.'
            ], 403);
        }

        DB::beginTransaction();

        try {

            if ($vendor->user_id) {
                User::where('id', $vendor->user_id)->delete();
            }

            $vendor->delete();

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Vendor deleted successfully.'
            ], 200);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => 'Delete failed.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
