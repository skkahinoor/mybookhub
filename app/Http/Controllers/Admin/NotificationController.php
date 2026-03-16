<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Helpers\RoleHelper;
use Yajra\DataTables\DataTables;

class NotificationController extends Controller
{
    /**
     * Check if the current user is a vendor (by type or role_id) and has vendor_id set.
     */
    private function isVendorUser($admin): bool
    {
        $isVendor = ($admin->type ?? null) === 'vendor' || $admin->role_id == RoleHelper::vendorId();

        return $isVendor && $admin->vendor_id !== null;
    }

    /**
     * Get notifications for dropdown (AJAX)
     */
    public function getNotifications()
    {
        $admin = Auth::guard('admin')->user();
        
        // Build query based on admin type
        $query = Notification::orderBy('created_at', 'desc');
        
        // Filter notifications based on role (use type for vendor - consistent with rest of app)
        if ($this->isVendorUser($admin)) {
            // Vendor sees ONLY their own notifications (vendor_id matches their vendor profile ID)
            $query->where('vendor_id', $admin->vendor_id);
        }
        
        $notifications = $query->limit(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id'           => $notification->id,
                    'type'         => $notification->type,
                    'title'        => $notification->title,
                    'message'      => $notification->message,
                    'related_id'   => $notification->related_id,
                    'related_type' => $notification->related_type,
                    'is_read'      => (bool) $notification->is_read,
                    'created_at'   => $notification->created_at->toDateTimeString(),
                ];
            })
            ->values()
            ->toArray();

        // Count unread notifications with same filter
        $unreadQuery = Notification::where('is_read', false);
        if ($this->isVendorUser($admin)) {
            $unreadQuery->where('vendor_id', $admin->vendor_id);
        }
        $unreadCount = $unreadQuery->count();

        return response()->json([
            'notifications' => $notifications,
            'unreadCount'   => $unreadCount,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $admin = Auth::guard('admin')->user();
        $notification = Notification::findOrFail($id);
        
        if ($this->isVendorUser($admin)) {
            if ($notification->vendor_id !== (int) $admin->vendor_id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
        }
        
        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $admin = Auth::guard('admin')->user();
        
        $query = Notification::where('is_read', false);
        
        if ($this->isVendorUser($admin)) {
            $query->where('vendor_id', $admin->vendor_id);
        }
        
        $query->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Show all notifications page
     */

    public function index(Request $request)
    {
        Session::put('page', 'notifications');
        $headerLogo = HeaderLogo::first();
        $logos      = HeaderLogo::first();
        $title      = 'All Notifications';
        
        $admin = Auth::guard('admin')->user();
        
        if ($request->ajax()) {
            $query = Notification::latest();
            
            if ($this->isVendorUser($admin)) {
                $query->where('vendor_id', $admin->vendor_id);
            }

            return DataTables::of($query)
                ->addIndexColumn()

                ->editColumn('message', function ($n) {
                    return Str::limit($n->message, 50);
                })

                ->editColumn('type', function ($n) {
                    return '<span class="badge badge-info">'
                    . ucfirst(str_replace('_', ' ', $n->type))
                        . '</span>';
                })

                ->editColumn('is_read', function ($n) {
                    return $n->is_read
                        ? '<span class="badge badge-success">Read</span>'
                        : '<span class="badge badge-warning">Unread</span>';
                })

                ->editColumn('created_at', function ($n) {
                    return $n->created_at->format('M d, Y h:i A');
                })

            // 🔥 ACTION COLUMN
                ->addColumn('action', function ($n) use ($admin) {

                    $html = '<div class="d-flex align-items-center" style="gap:10px;">';

                    // Mark as read
                    if (! $n->is_read) {
                        $html .= '
                        <a href="#" class="mark-as-read"
                           data-id="' . $n->id . '" title="Mark as Read">
                           <i class="mdi mdi-check-circle text-success" style="font-size:20px"></i>
                        </a>';
                    }

                    $prefix = $this->isVendorUser($admin) ? 'vendor' : 'admin';

                    // Sales Executive
                    if ($n->related_type === 'App\Models\SalesExecutive' && $n->related_id) {
                        $html .= '
                        <a href="#" class="view-sales-executive"
                           data-id="' . $n->related_id . '"
                           data-notification-id="' . $n->id . '"
                           title="View Sales Executive">
                           <i class="mdi mdi-eye text-primary" style="font-size:20px"></i>
                        </a>';
                    }

                    // Withdrawal
                    if ($n->related_type === 'App\Models\Withdrawal' && $n->related_id) {
                        $routeName = ($prefix == 'vendor') ? 'vendor.withdrawals.show' : 'admin.withdrawals.show';
                        $html .= '
                        <a href="' . route($routeName, $n->related_id) . '"
                           title="View Withdrawal Request">
                           <i class="mdi mdi-cash-multiple text-info" style="font-size:20px"></i>
                        </a>';
                    }

                    // Institution
                    if ($n->related_type === 'App\Models\InstitutionManagement' && $n->related_id) {
                        $html .= '
                        <a href="#" class="view-institution"
                           data-id="' . $n->related_id . '"
                           data-notification-id="' . $n->id . '"
                           title="View Institution">
                           <i class="mdi mdi-school text-warning" style="font-size:20px"></i>
                        </a>';
                    }

                    // Student or User profile
                    if ($n->related_type === 'App\Models\User' && $n->related_id) {
                        $html .= '
                        <a href="#" class="view-student"
                           data-id="' . $n->related_id . '"
                           data-notification-id="' . $n->id . '"
                           title="View Details">
                           <i class="mdi mdi-account text-info" style="font-size:20px"></i>
                        </a>';
                    }

                    // Product added
                    if ($n->related_type === 'App\Models\Product' && $n->related_id) {
                        $productUrl = ($prefix == 'vendor') 
                            ? url('vendor/add-edit-product/' . $n->related_id)
                            : url('admin/add-edit-product/' . $n->related_id);
                        $html .= '
                        <a href="' . $productUrl . '"
                           title="View Product">
                           <i class="mdi mdi-eye text-primary" style="font-size:20px"></i>
                        </a>';
                    }

                    // Order placed (vendor sees "View Order" in notifications)
                    if ($n->related_type === 'App\Models\Order' && $n->related_id) {
                        $orderUrl = ($prefix == 'vendor')
                            ? url('vendor/orders/' . $n->related_id)
                            : url('admin/orders/' . $n->related_id);
                        $html .= '
                        <a href="' . $orderUrl . '"
                           title="View Order">
                           <i class="mdi mdi-cart text-info" style="font-size:20px"></i>
                        </a>';
                    }

                    $html .= '</div>';

                    return $html;
                })

                ->rawColumns(['type', 'is_read', 'action'])
                ->make(true);
        }

        return view('admin.notifications.index', [
            'title'      => 'Notifications',
            'headerLogo' => $headerLogo,
            'logos'      => $logos,
        ]);
    }
}
