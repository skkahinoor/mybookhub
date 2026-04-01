<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class OrderQueryController extends Controller
{
    public function index()
    {
        Session::put('page', 'order_queries');
        $admin = Auth::guard('admin')->user();
        $isVendor = $admin->hasRole('vendor');
        
        $query = OrderQuery::with(['order', 'orderProduct', 'user']);
        
        if ($isVendor) {
            $query->where('vendor_id', $admin->vendor_id);
        }
        
        $queries = $query->orderBy('created_at', 'desc')->get();
        return view('admin.order_queries.index', compact('queries'));
    }

    public function updateStatus(Request $request)
    {
        if ($request->ajax()) {
            $admin = Auth::guard('admin')->user();
            $query = OrderQuery::where('id', $request->query_id);
            if ($admin->hasRole('vendor')) {
                $query->where('vendor_id', $admin->vendor_id);
            }
            $query->update(['status' => $request->status]);
            return response()->json(['status' => true, 'message' => 'Status updated successfully.']);
        }
    }

    public function reply(Request $request, $id)
    {
        $admin = Auth::guard('admin')->user();
        $isVendor = $admin->hasRole('vendor');

        $queryObj = OrderQuery::where('id', $id);
        if ($isVendor) {
            $queryObj->where('vendor_id', $admin->vendor_id);
        }
        $query = $queryObj->with(['order', 'orderProduct', 'user'])->firstOrFail();

        if ($request->isMethod('post')) {
            $request->validate([
                'admin_reply' => 'required',
                'status' => 'required'
            ]);

            OrderQuery::where('id', $id)->update([
                'admin_reply' => $request->admin_reply,
                'status' => $request->status
            ]);

            $redirectUrl = $isVendor ? 'vendor/order-queries' : 'admin/order-queries';
            return redirect($redirectUrl)->with('success_message', 'Reply updated successfully.');
        }

        return view('admin.order_queries.reply', compact('query'));
    }

    public function delete($id)
    {
        $admin = Auth::guard('admin')->user();
        $query = OrderQuery::where('id', $id);
        if ($admin->hasRole('vendor')) {
            $query->where('vendor_id', $admin->vendor_id);
        }
        $query->delete();
        return redirect()->back()->with('success_message', 'Query deleted successfully.');
    }
}
