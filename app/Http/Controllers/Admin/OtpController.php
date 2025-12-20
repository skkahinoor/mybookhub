<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\HeaderLogo;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class OtpController extends Controller
{
    public function otps()
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();

        $otps = Otp::orderByDesc('id')->get();
        return view('admin.otps.index', compact('otps', 'logos', 'headerLogo'));
    }
}