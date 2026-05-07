<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeliveryAgentPayout;
use Illuminate\Support\Facades\Session;

class DeliveryAgentPayoutController extends Controller
{
    public function payouts()
    {
        Session::put('page', 'delivery_agent_payouts');
        $payouts = DeliveryAgentPayout::with('deliveryAgent.user')->orderBy('id', 'desc')->get();
        return view('admin.payouts.delivery_agent_payouts', compact('payouts'));
    }

    public function updatePayoutStatus(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();
            
            DeliveryAgentPayout::where('id', $data['payout_id'])->update([
                'status' => $data['status'],
                'transaction_id' => $data['transaction_id'] ?? null,
                'admin_remarks' => $data['admin_remarks'] ?? null,
                'payment_method' => $data['payment_method'] ?? 'Bank Transfer'
            ]);

            return redirect()->back()->with('success_message', 'Payout status updated successfully!');
        }
    }
}
