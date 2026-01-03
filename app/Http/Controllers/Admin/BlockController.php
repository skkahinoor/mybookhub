<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\District;
use App\Models\HeaderLogo;
// use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class BlockController extends Controller
{
    public function index()
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        Session::put('page', 'blocks');

        $blocks = Block::with('district.state.country')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.blocks.index')->with(compact('blocks', 'logos', 'headerLogo'));
    }

    public function create()
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        Session::put('page', 'blocks');

        $districts = District::where('status', true)
            ->with('state.country')
            ->orderBy('name')
            ->get();

        return view('admin.blocks.create')->with(compact('districts', 'logos', 'headerLogo'));
    }

    public function store(Request $request)
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $request->validate([
            'name' => 'required|string|max:255',
            'district_id' => 'required|exists:districts,id',
            'status' => 'boolean'
        ]);

        $data = $request->all();
        $data['status'] = $request->has('status') ? true : false;

        Block::create($data);

        return redirect('admin/blocks')->with('success_message', 'Block has been added successfully', 'logos');
        return view('admin.blocks.index', compact('blocks', 'logos', 'headerLogo'));
    }

    public function edit($id)
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        Session::put('page', 'blocks');

        $block = Block::findOrFail($id);
        $districts = District::where('status', true)
            ->with('state.country')
            ->orderBy('name')
            ->get();

        return view('admin.blocks.edit')->with(compact('block', 'districts', 'logos', 'headerLogo'));
    }

    public function update(Request $request, $id)
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $request->validate([
            'name' => 'required|string|max:255',
            'district_id' => 'required|exists:districts,id',
            'status' => 'boolean'
        ]);

        $block = Block::findOrFail($id);
        $data = $request->all();
        $data['status'] = $request->has('status') ? true : false;

        $block->update($data);

        return redirect('admin/blocks')->with('success_message', 'Block has been updated successfully', 'logos');
        return view('admin.blocks.index', compact('blocks', 'logos', 'headerLogo'));
    }

    public function destroy($id)
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $block = Block::findOrFail($id);
        $block->delete();

        return redirect('admin/blocks')->with('success_message', 'Block has been deleted successfully', 'logos');
        return view('admin.blocks.index', compact('blocks', 'logos', 'headerLogo'));
    }

    public function updateStatus(Request $request)
    {
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        if ($request->ajax()) {
            $data = $request->all();

            if ($data['status'] == "Active") {
                $status = 0;
            } else {
                $status = 1;
            }

            Block::where('id', $data['block_id'])->update(['status' => $status]);
            return response()->json(['status' => $status, 'block_id' => $data['block_id']]);
        }
        return view('admin.blocks.index', compact('blocks', 'logos', 'headerLogo'));
    }
}
