<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class OrderQueryController extends Controller
{
    public function index(Request $request)
    {
        Session::put('page', 'order_queries');
        $admin = Auth::guard('admin')->user();
        $isVendor = $admin->hasRole('vendor');
        
        if ($request->ajax()) {
            $query = OrderQuery::with(['order', 'orderProduct', 'user']);
            
            if ($isVendor) {
                $query->where('vendor_id', $admin->vendor_id);
            }

            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('ticket_id', function ($row) {
                    return '<strong>' . $row->ticket_id . '</strong>';
                })
                ->addColumn('date', function ($row) {
                    return $row->created_at->format('d-m-Y');
                })
                ->addColumn('user_info', function ($row) {
                    return $row->user->name . '<br><small>' . $row->user->email . '</small>';
                })
                ->addColumn('order_id_formatted', function ($row) {
                    return '#' . $row->order_id;
                })
                ->addColumn('product_name', function ($row) {
                    return $row->orderProduct->product_name ?? 'N/A';
                })
                ->addColumn('status_dropdown', function ($row) {
                    $html = '<select class="form-control updateQueryStatus" data-query-id="' . $row->id . '" style="height: 35px; padding: 2px 10px;">';
                    $statuses = [
                        'pending' => 'text-warning',
                        'ongoing' => 'text-info',
                        'resolved' => 'text-success',
                        'closed' => 'text-secondary'
                    ];
                    
                    foreach ($statuses as $val => $class) {
                        $selected = ($row->status == $val) ? 'selected' : '';
                        $html .= '<option value="' . $val . '" ' . $selected . ' class="' . $class . '">' . ucfirst($val) . '</option>';
                    }
                    $html .= '</select>';
                    return $html;
                })
                ->addColumn('actions', function ($row) {
                    $replyUrl = url('admin/order-query/reply/' . $row->id);
                    $html = '<a href="' . $replyUrl . '" title="Reply/View Detail"><i class="mdi mdi-reply" style="font-size: 25px;"></i></a>&nbsp;&nbsp;';
                    $html .= '<a href="javascript:void(0)" class="confirmDelete" module="order-query" moduleid="' . $row->id . '" title="Delete Query"><i class="mdi mdi-delete" style="font-size: 25px;"></i></a>';
                    return $html;
                })
                ->rawColumns(['ticket_id', 'user_info', 'status_dropdown', 'actions'])
                ->make(true);
        }
        
        return view('admin.order_queries.index');
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
        $query = $queryObj->with(['order', 'orderProduct', 'user', 'messages.user'])->firstOrFail();

        if ($request->isMethod('post')) {
            $request->validate([
                'admin_reply' => 'required',
                'status' => 'required',
                'attachment' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov,avi,pdf|max:10240',
            ]);

            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('attachments/order_queries'), $filename);
                $attachmentPath = 'attachments/order_queries/' . $filename;
            }

            // Save new message
            \App\Models\OrderQueryMessage::create([
                'order_query_id' => $id,
                'user_id' => $admin->id,
                'message' => $request->admin_reply,
                'attachment' => $attachmentPath,
                'sender_type' => $isVendor ? 'vendor' : 'admin'
            ]);

            // Update main query status
            OrderQuery::where('id', $id)->update([
                'status' => $request->status
            ]);

            // Notify User
            \App\Models\Notification::create([
                'type' => 'order_query_reply',
                'title' => 'New Reply for Ticket #' . $query->ticket_id,
                'message' => "Support has replied to your query regarding '" . ($query->orderProduct->product_name ?? 'Product') . "'.",
                'related_id' => $query->order_id,
                'related_type' => \App\Models\Order::class,
                'user_id' => $query->user_id, // Ensure this points to the student
                'is_read' => false,
            ]);

            $redirectUrl = $isVendor ? 'vendor/order-queries' : 'admin/order-queries';
            return redirect($redirectUrl)->with('success_message', 'Reply sent successfully.');
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
