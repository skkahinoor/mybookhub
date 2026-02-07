<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
class AuthController extends Controller
{
    public function Login(){
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        return view('user.auth.login', compact('logos', 'headerLogo'));
    }
    public function Register(){
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        return view('user.auth.register', compact('logos', 'headerLogo'));
    }
    public function loginStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login'    => 'required|string|max:150', // email or phone
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return redirect()->route('user.login')
                ->with('error', $validator->errors()->first());
        }

        $loginInput = trim($request->login);

        // Detect email or phone
        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $credentials = [
                'email'    => $loginInput,
                'password' => $request->password,
            ];
        } else {
            $credentials = [
                'phone'    => $loginInput,
                'password' => $request->password,
            ];
        }

        if (Auth::attempt($credentials)) {
            return redirect()->route('user.index');
        }

        return redirect()->route('user.login')->with('error', 'Invalid credentials');
    }


    public function registerStore(Request $request){
       //dd($request->all());
        $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:8',
            'phone'=>'required|numeric|digits:10',
        ]);
        $role = \Spatie\Permission\Models\Role::where('name', 'user')->first();
        $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'phone'=>$request->phone,
            'password'=>Hash::make($request->password),
            'role_id'=>$role ? $role->id : null,
            'status' => 1,
        ]);
        if ($role) {
            $user->assignRole($role);
        }
        return redirect()->route('user.login')->with('success','Account created successfully');
    }
    public function logout(Request $request)
    {
        // Log out the user from the web guard
        Auth::logout();

        // Invalidate and regenerate session to prevent session fixation
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('user.login')->with('success', 'You are logged out');
    }

}
