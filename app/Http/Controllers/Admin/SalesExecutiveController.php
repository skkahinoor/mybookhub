<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\SalesExecutive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class SalesExecutiveController extends Controller
{
    public function index()
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'view_sales');
        $title = 'Sales Executives';
        $salesExecutives = SalesExecutive::all(); // Remove toArray() to keep as collection for object access
        return view('admin.salesexecutives.index', compact('salesExecutives', 'title', 'logos', 'headerLogo'));
    }

    public function addEdit(Request $request, $id = null)
    {
        Session::put('page', 'view_sales');
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();

        $salesExecutive = null;
        $isEdit = false;
        if (! empty($id)) {
            $salesExecutive = SalesExecutive::findOrFail($id);
            $isEdit = true;
        }

        if ($request->isMethod('post')) {
            $data = $request->all();

            $rules = [
                'name'  => 'required|string|max:255',
                'email' => 'required|email|unique:sales_executives,email' . ($isEdit ? ',' . $salesExecutive->id : ''),
                'phone' => 'required|numeric',
            ];

            if (! $isEdit) {
                $rules['password'] = 'required|min:6|confirmed';
            }

            $validated = $request->validate($rules);

            $payload = [
                'name'   => $validated['name'],
                'email'  => $validated['email'],
                'phone'  => $validated['phone'],
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'district' => $data['district'] ?? null,
                'state' => $data['state'] ?? null,
                'pincode' => $data['pincode'] ?? null,
                'country' => $data['country'] ?? null,
                'bank_name' => $data['bank_name'] ?? null,
                'account_number' => $data['account_number'] ?? null,
                'ifsc_code' => $data['ifsc_code'] ?? null,
                'bank_branch' => $data['bank_branch'] ?? null,
                'upi_id' => $data['upi_id'] ?? null,
                'total_target' => $data['total_target'] ?? null,
                'completed_target' => $data['completed_target'] ?? null,
                'income_per_target' => $data['income_per_target'] ?? null,
                'status' => 0,
            ];

            if (! $isEdit) {
                $payload['password'] = Hash::make($validated['password']);
                SalesExecutive::create($payload);
                return redirect()->route('salesexecutives.index')->with('success_message', 'Sales Executive added successfully!');
            }

            // Do not overwrite password on edit unless provided
            if (! empty($data['password'])) {
                $payload['password'] = Hash::make($data['password']);
            }

            $salesExecutive->update($payload);
            return redirect()->route('salesexecutives.index')->with('success_message', 'Sales Executive updated successfully!');
        }

        return view('admin.salesexecutives.add_edit', compact('salesExecutive', 'logos', 'headerLogo'));
    }

    public function delete($id)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        SalesExecutive::where('id', $id)->delete();
        return redirect()->back()->with('success_message', 'Sales Executive deleted successfully!');
    }

    public function updateStatus(Request $request)
    {
        if (! $request->ajax()) {
            abort(400, 'Invalid request');
        }

        $data = $request->validate([
            'status'              => 'required|string|in:Active,Inactive',
            'sales_executive_id'  => 'required|integer|exists:sales_executives,id',
        ]);

        // Fix: status 1 = Active, status 0 = Inactive
        $newStatus = $data['status'] === 'Active' ? 1 : 0;

        SalesExecutive::where('id', $data['sales_executive_id'])->update(['status' => $newStatus]);

        return response()->json([
            'status'             => $newStatus,
            'sales_executive_id' => $data['sales_executive_id'],
        ]);
    }

    /**
     * Get sales executive details for modal (AJAX)
     */
    public function getDetails($id)
    {
        $salesExecutive = SalesExecutive::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $salesExecutive->id,
                'name' => $salesExecutive->name,
                'email' => $salesExecutive->email,
                'phone' => $salesExecutive->phone,
                'address' => $salesExecutive->address,
                'city' => $salesExecutive->city,
                'district' => $salesExecutive->district,
                'state' => $salesExecutive->state,
                'pincode' => $salesExecutive->pincode,
                'country' => $salesExecutive->country,
                // 'bank_name' => $salesExecutive->bank_name,
                // 'account_number' => $salesExecutive->account_number,
                // 'ifsc_code' => $salesExecutive->ifsc_code,
                // 'bank_branch' => $salesExecutive->bank_branch,
                // 'upi_id' => $salesExecutive->upi_id,
                // 'total_target' => $salesExecutive->total_target,
                // 'completed_target' => $salesExecutive->completed_target,
                // 'income_per_target' => $salesExecutive->income_per_target,
                'status' => $salesExecutive->status,
                'created_at' => $salesExecutive->created_at->format('M d, Y h:i A'),
            ]
        ]);
    }
}


