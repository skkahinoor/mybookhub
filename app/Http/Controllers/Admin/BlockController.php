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
    public function index(Request $request)
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        Session::put('page', 'blocks');

        if ($request->ajax()) {
            $query = Block::with('district.state.country');

            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('district', function($row) {
                    return $row->district->name ?? 'N/A';
                })
                ->addColumn('state', function($row) {
                    return $row->district->state->name ?? 'N/A';
                })
                ->addColumn('country', function($row) {
                    return $row->district->state->country->name ?? 'N/A';
                })
                ->addColumn('status', function ($row) {
                    $status = $row->status ? 'Active' : 'Inactive';
                    $class = $row->status ? 'status-active' : 'status-inactive';
                    return '<a href="javascript:void(0)"
                               class="status-badge ' . $class . '"
                               id="block-' . $row->id . '"
                               onclick="updateBlockStatus(' . $row->id . ', \'' . $status . '\')">
                                ' . $status . '
                            </a>';
                })
                ->addColumn('actions', function ($row) {
                    $editUrl = route('admin.blocks.edit', $row->id);
                    return '<a href="' . $editUrl . '" class="btn-action btn-edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="javascript:void(0)" class="btn-action btn-delete"
                               onclick="confirmDelete(' . $row->id . ', \'' . $row->name . '\')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                            <form id="delete-block-' . $row->id . '"
                                  action="' . route('admin.blocks.destroy', $row->id) . '"
                                  method="POST" style="display:none;">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '
                            </form>';
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }

        return view('admin.blocks.index')->with(compact('logos', 'headerLogo'));
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
