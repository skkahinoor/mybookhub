<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Admin;
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
        $adminStatuses = Admin::where('type', 'vendor')
            ->pluck('status', 'vendor_id');

        return view('sales.vendors.index', compact('vendors', 'adminStatuses', 'logos', 'headerLogo'));
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
             'email'                 => 'required|email|unique:vendors,email|unique:admins,email',
             'mobile'                => 'required|string|min:10|max:15|unique:vendors,mobile|unique:admins,mobile',
             'password'              => 'required|string|min:6',
         ]);

         $vendor = null;

         DB::transaction(function () use ($data, &$vendor) {
             $vendor = Vendor::create([
                 'name'    => $data['name'],
                 'email'   => $data['email'],
                 'mobile'  => $data['mobile'],
                 'confirm' => 'Yes',
                 'status'  => 0,
             ]);

             Admin::create([
                 'name'      => $data['name'],
                 'email'     => $data['email'],
                 'mobile'    => $data['mobile'],
                 'type'      => 'vendor',
                 'vendor_id' => $vendor->id,
                 'password'  => Hash::make($data['password']),
                 'confirm'   => 'Yes',
                 'status'    => 0,
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
            Admin::where('vendor_id', $vendor->id)->delete();
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

        $adminAccount = Admin::where('vendor_id', $vendor->id)->first();

        return view('sales.vendors.show', compact('vendor', 'adminAccount', 'logos', 'headerLogo'));
    }
}

