<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HeaderLogo;
use App\Models\DeliveryAgentContactQuery;
use App\Models\DeliveryAgentContactQueryMessage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class DeliveryAgentQueryController extends Controller
{
    public function index(Request $request)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'delivery_agent_queries');
        $title = 'Delivery Agent Queries';

        if ($request->ajax()) {
            $data = DeliveryAgentContactQuery::with('user')->orderBy('created_at', 'desc');

            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('agent_name', function ($row) {
                    return $row->user ? $row->user->name : 'N/A';
                })
                ->addColumn('status', function ($row) {
                    if ($row->status === 'Open') {
                        return '<span class="badge bg-warning">Open</span>';
                    } elseif ($row->status === 'Solved') {
                        return '<span class="badge bg-success">Solved</span>';
                    } else {
                        return '<span class="badge bg-secondary">Closed</span>';
                    }
                })
                ->addColumn('actions', function ($row) {
                    return '<div class="d-flex align-items-center" style="gap: 10px;">
                                <a href="' . route('admin.delivery_agent_queries.show', $row->id) . '" title="View Details">
                                    <i style="font-size: 20px; color: #a71d84;" class="mdi mdi-eye"></i>
                                </a>
                                <a href="' . route('admin.delivery_agent_queries.delete', $row->id) . '" title="Delete" onclick="return confirm(\'Delete this query?\');">
                                    <i style="font-size: 20px; color: #e74c3c;" class="mdi mdi-delete"></i>
                                </a>
                            </div>';
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }

        return view('admin.delivery_agent_queries.index', compact('title', 'logos', 'headerLogo'));
    }

    public function show($id)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        Session::put('page', 'delivery_agent_queries');
        $title = 'Query Details';

        $query = DeliveryAgentContactQuery::with('user', 'messages')->findOrFail($id);

        return view('admin.delivery_agent_queries.show', compact('title', 'logos', 'headerLogo', 'query'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $query = DeliveryAgentContactQuery::findOrFail($id);
        
        if ($query->status === 'Closed' || $query->status === 'Solved') {
            return redirect()->back()->with('error_message', 'Cannot reply to a closed or solved query.');
        }

        DeliveryAgentContactQueryMessage::create([
            'query_id' => $query->id,
            'sender_type' => 'admin',
            'message' => $request->message,
        ]);

        return redirect()->back()->with('success_message', 'Reply sent successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Open,Solved,Closed',
        ]);

        $query = DeliveryAgentContactQuery::findOrFail($id);
        $query->update(['status' => $request->status]);

        return redirect()->back()->with('success_message', 'Query status updated to ' . $request->status);
    }

    public function delete($id)
    {
        $query = DeliveryAgentContactQuery::findOrFail($id);
        $query->delete(); // This will cascade delete messages

        return redirect()->route('admin.delivery_agent_queries.index')->with('success_message', 'Query deleted successfully!');
    }
}
