<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\OldBookCommission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class OldBookCommissionController extends Controller
{
    /**
     * Show old book commission percentages.
     */
    public function index()
    {
        Session::put('page', 'old_book_commissions_crud');

        $logos      = HeaderLogo::first();
        $headerLogo = $logos;
        $adminType  = Auth::guard('admin')->user()->type;
        $commissions = OldBookCommission::orderBy('id', 'desc')->get();
        $commissionCount = $commissions->count();

        return view('admin.old_book_commissions.index',
            compact('commissions', 'logos', 'headerLogo', 'adminType', 'commissionCount'));
    }

    /**
     * Store a new old book commission.
     */
    public function store(Request $request)
    {
        if (OldBookCommission::count() >= 1) {
            return redirect()
                ->route('admin.old_book_commissions.index')
                ->with('error_message', 'Only one commission percentage can be added. Please edit the existing one.');
        }

        $request->validate([
            'percentage' => 'required|numeric|min:0|max:100',
        ], [
            'percentage.required' => 'Percentage is required.',
            'percentage.numeric'  => 'Percentage must be a number.',
            'percentage.min'      => 'Percentage must be at least 0.',
            'percentage.max'      => 'Percentage cannot exceed 100.',
        ]);

        OldBookCommission::create([
            'percentage' => $request->percentage,
        ]);

        return redirect()
            ->route('admin.old_book_commissions.index')
            ->with('success_message', 'Old book commission added successfully!');
    }

    /**
     * Show the edit form for a commission.
     */
    public function edit($id)
    {
        Session::put('page', 'old_book_commissions_crud');

        $logos      = HeaderLogo::first();
        $headerLogo = $logos;
        $commission  = OldBookCommission::findOrFail($id);
        $title      = 'Edit Old Book Commission';

        return view('admin.old_book_commissions.add_edit',
            compact('commission', 'title', 'logos', 'headerLogo'));
    }

    /**
     * Update an old book commission.
     */
    public function update(Request $request, $id)
    {
        $commission = OldBookCommission::findOrFail($id);

        $request->validate([
            'percentage' => 'required|numeric|min:0|max:100',
        ], [
            'percentage.required' => 'Percentage is required.',
            'percentage.numeric'  => 'Percentage must be a number.',
            'percentage.min'      => 'Percentage must be at least 0.',
            'percentage.max'      => 'Percentage cannot exceed 100.',
        ]);

        $commission->update([
            'percentage' => $request->percentage,
        ]);

        return redirect()
            ->route('admin.old_book_commissions.index')
            ->with('success_message', 'Old book commission updated successfully!');
    }

    /**
     * Delete an old book commission.
     */
    public function destroy($id)
    {
        OldBookCommission::findOrFail($id)->delete();

        return redirect()
            ->route('admin.old_book_commissions.index')
            ->with('success_message', 'Old book commission deleted successfully!');
    }

    /**
     * Show the create form.
     */
    public function create()
    {
        if (OldBookCommission::count() >= 1) {
            return redirect()
                ->route('admin.old_book_commissions.index')
                ->with('error_message', 'Only one commission percentage can be added. Please edit the existing one.');
        }

        Session::put('page', 'old_book_commissions_crud');

        $logos      = HeaderLogo::first();
        $headerLogo = $logos;
        $commission  = new OldBookCommission();
        $title      = 'Add Old Book Commission';

        return view('admin.old_book_commissions.add_edit',
            compact('commission', 'title', 'logos', 'headerLogo'));
    }
}
