<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\OldBookCondition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class OldBookConditionController extends Controller
{
    // ── LIST ──────────────────────────────────────────────────────────────
    public function index()
    {
        Session::put('page', 'old_book_conditions');

        $logos      = HeaderLogo::first();
        $headerLogo = $logos;
        $adminType  = Auth::guard('admin')->user()->type;
        $conditions = OldBookCondition::orderBy('id', 'desc')->get();

        return view('admin.old_book_conditions.index',
            compact('conditions', 'logos', 'headerLogo', 'adminType'));
    }

    // ── CREATE FORM ────────────────────────────────────────────────────────
    public function create()
    {
        Session::put('page', 'old_book_conditions');

        $logos      = HeaderLogo::first();
        $headerLogo = $logos;
        $condition  = new OldBookCondition();
        $title      = 'Add Old Book Condition';

        return view('admin.old_book_conditions.form',
            compact('condition', 'title', 'logos', 'headerLogo'));
    }

    // ── STORE ──────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:100|unique:old_book_conditions,name',
            'percentage' => 'required|numeric|min:0|max:100',
        ], [
            'name.required'       => 'Condition name is required.',
            'name.unique'         => 'This condition name already exists.',
            'percentage.required' => 'Percentage is required.',
            'percentage.numeric'  => 'Percentage must be a number.',
            'percentage.min'      => 'Percentage must be at least 0.',
            'percentage.max'      => 'Percentage cannot exceed 100.',
        ]);

        OldBookCondition::create([
            'name'       => trim($request->name),
            'percentage' => $request->percentage,
        ]);

        return redirect()
            ->route('admin.old_book_conditions.index')
            ->with('success_message', 'Condition added successfully!');
    }

    // ── EDIT FORM ──────────────────────────────────────────────────────────
    public function edit($id)
    {
        Session::put('page', 'old_book_conditions');

        $logos      = HeaderLogo::first();
        $headerLogo = $logos;
        $condition  = OldBookCondition::findOrFail($id);
        $title      = 'Edit Old Book Condition';

        return view('admin.old_book_conditions.form',
            compact('condition', 'title', 'logos', 'headerLogo'));
    }

    // ── UPDATE ─────────────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $condition = OldBookCondition::findOrFail($id);

        $request->validate([
            'name'       => 'required|string|max:100|unique:old_book_conditions,name,' . $id,
            'percentage' => 'required|numeric|min:0|max:100',
        ], [
            'name.required'       => 'Condition name is required.',
            'name.unique'         => 'This condition name already exists.',
            'percentage.required' => 'Percentage is required.',
            'percentage.numeric'  => 'Percentage must be a number.',
            'percentage.min'      => 'Percentage must be at least 0.',
            'percentage.max'      => 'Percentage cannot exceed 100.',
        ]);

        $condition->update([
            'name'       => trim($request->name),
            'percentage' => $request->percentage,
        ]);

        return redirect()
            ->route('admin.old_book_conditions.index')
            ->with('success_message', 'Condition updated successfully!');
    }

    // ── DELETE ─────────────────────────────────────────────────────────────
    public function destroy($id)
    {
        OldBookCondition::findOrFail($id)->delete();

        return redirect()
            ->route('admin.old_book_conditions.index')
            ->with('success_message', 'Condition deleted successfully!');
    }
}
