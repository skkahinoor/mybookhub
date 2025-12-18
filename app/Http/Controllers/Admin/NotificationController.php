<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\HeaderLogo;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    /**
     * Get notifications for dropdown (AJAX)
     */
    public function getNotifications()
    {
        $notifications = Notification::orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'related_id' => $notification->related_id,
                    'related_type' => $notification->related_type,
                    'is_read' => (bool) $notification->is_read,
                    'created_at' => $notification->created_at->toDateTimeString(),
                ];
            })
            ->values()
            ->toArray();

        $unreadCount = Notification::where('is_read', false)->count();

        return response()->json([
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Notification::where('is_read', false)->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Show all notifications page
     */

    public function index(Request $request)
    {
        Session::put('page', 'notifications');
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $title = 'All Notifications';
        if ($request->ajax()) {

            $query = Notification::latest();

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
                ->addColumn('action', function ($n) {

                    $html = '<div class="d-flex align-items-center" style="gap:10px;">';

                    // Mark as read
                    if (!$n->is_read) {
                        $html .= '
                        <a href="#" class="mark-as-read"
                           data-id="' . $n->id . '" title="Mark as Read">
                           <i class="mdi mdi-check-circle text-success" style="font-size:20px"></i>
                        </a>';
                    }

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
                        $html .= '
                        <a href="' . route('admin.withdrawals.show', $n->related_id) . '"
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

                    // Student
                    if ($n->related_type === 'App\Models\User' && $n->related_id) {
                        $html .= '
                        <a href="#" class="view-student"
                           data-id="' . $n->related_id . '"
                           data-notification-id="' . $n->id . '"
                           title="View Student">
                           <i class="mdi mdi-account text-info" style="font-size:20px"></i>
                        </a>';
                    }

                    $html .= '</div>';

                    return $html;
                })

                ->rawColumns(['type', 'is_read', 'action'])
                ->make(true);
        }

        return view('admin.notifications.index', [
            'title' => 'Notifications',
            'headerLogo' => $headerLogo,
            'logos' => $logos,
        ]);
    }
}
