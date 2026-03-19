<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mov;
use App\Models\HeaderLogo;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class MovController extends Controller
{
    public function movs()
    {
        Session::put('page', 'movs');

        $movs = Mov::orderBy('id', 'desc')->get();
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();

        return view('admin.movs.movs', compact('movs', 'logos', 'headerLogo'));
    }

    public function addEditMov(Request $request, $id = null)
    {
        Session::put('page', 'movs');
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();

        if ($id == '') {
            $title = 'Add MOV';
            $mov = new Mov();
            $message = 'MOV added successfully!';
        } else {
            $title = 'Edit MOV';
            $mov = Mov::find($id);
            $message = 'MOV updated successfully!';
        }

        if ($request->isMethod('post')) {
            $data = $request->all();

            $rules = [
                'price' => 'required|numeric',
                'cashback_percentage' => 'required|numeric',
            ];

            $this->validate($request, $rules);

            $mov->price = $data['price'];
            $mov->cashback_percentage = $data['cashback_percentage'];
            $mov->save();

            return redirect('admin/movs')->with('success_message', $message);
        }

        return view('admin.movs.add_edit_mov', compact('title', 'mov', 'logos', 'headerLogo'));
    }

    public function deleteMov($id)
    {
        Mov::where('id', $id)->delete();
        $message = 'MOV has been deleted successfully!';
        return redirect()->back()->with('success_message', $message);
    }
}
