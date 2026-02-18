<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\HeaderLogo;
use App\Models\Notification;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class VendorController extends Controller
{
    /**
     * Display a listing of vendors for sales executives.
     */
    public function index()
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'vendors');
        $vendors = Vendor::orderByDesc('id')->get();
        return view('sales.vendors.index', compact('vendors', 'logos', 'headerLogo'));
    }

    /**
     * Show the form for creating a new vendor.
     */
    public function create()
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'vendors');

        return view('sales.vendors.create', compact('logos', 'headerLogo'));
    }

     /**
      * Store a newly created vendor with a linked admin account.
      */
     public function store(Request $request)
     {
         $data = $request->validate([
             'name'                  => 'required|string|max:255',
             'email'                 => 'required|email|unique:users,email',
             'mobile'                => 'required|string|min:10|max:15|unique:users,phone',
             'password'              => 'required|string|min:6',
         ]);

         DB::transaction(function () use ($data) {
             // 1. Create User first
             $role = Role::where('name', 'vendor')->first();
             
             $user = User::create([
                 'name'      => $data['name'],
                 'email'     => $data['email'],
                 'phone'     => $data['mobile'],
                 'password'  => Hash::make($data['password']),
                 'role_id'   => $role ? $role->id : null,
                 'status'    => 0, // Inactive by default
             ]);
             
             if ($role) {
                 $user->assignRole($role);
             }

             // 2. Create Vendor linked to User
             $vendor = Vendor::create([
                 'user_id' => $user->id,
                 'confirm' => 'Yes',
                 'status'  => 0,
             ]);

             Notification::create([
                 'type'         => 'vendor_added',
                 'title'        => 'New Vendor Added',
                 'message'      => "Sales team added vendor '{$data['name']}' awaiting activation.",
                 'related_id'   => $vendor->id,
                 'related_type' => Vendor::class,
                 'is_read'      => false,
             ]);
         });

         return redirect()
             ->route('sales.vendors.index')
             ->with('success_message', 'Vendor added successfully.');
     }
     /**
      * Remove the specified vendor (only if inactive).
      */
    public function destroy(Vendor $vendor)
    {
        if ($vendor->status == 1) {
            return redirect()
                ->route('sales.vendors.index')
                ->with('error_message', 'Active vendors cannot be deleted.');
        }

        DB::transaction(function () use ($vendor) {
            if ($vendor->user_id) {
                User::where('id', $vendor->user_id)->delete();
            }
            $vendor->delete();
        });

        return redirect()
            ->route('sales.vendors.index')
            ->with('success_message', 'Vendor deleted successfully.');
    }

    /**
     * Display the specified vendor.
     */
    public function show(Vendor $vendor)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'vendors');

        $adminAccount = User::find($vendor->user_id);

        return view('sales.vendors.show', compact('vendor', 'adminAccount', 'logos', 'headerLogo'));
    }
}

