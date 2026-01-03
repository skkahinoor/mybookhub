<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\HeaderLogo;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class BannerController extends Controller
{
    // List banners (Admin)
    public function banners()
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $banners = Banner::orderByDesc('id')->get();
        // View expected by existing admin UI
        return view('admin.banners.banners', compact('banners', 'logos', 'headerLogo'));
    }

    // Add/Edit Banner (GET render form, POST submit form)
    public function addEditBanner(Request $request, $id = null)
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $banner = $id ? Banner::findOrFail($id) : new Banner();

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'type'  => 'required|string|max:50',
                'link'  => 'required|string|max:255',
                'title' => 'required|string|max:255',
                'alt'   => 'required|string|max:255',
                'status'=> 'nullable|in:0,1',
                'image' => ($id ? 'nullable' : 'required') . '|image|mimes:jpeg,png,jpg,webp,avif,gif|max:5120',
            ]);

            $banner->type = $validated['type'];
            $banner->link = $validated['link'];
            $banner->title = $validated['title'];
            $banner->alt = $validated['alt'];
            $banner->status = isset($validated['status']) ? (int) $validated['status'] : 1;

            // Handle image upload if provided
            if ($request->hasFile('image')) {
                $imageFile = $request->file('image');
                $imageName = uniqid('banner_') . '.' . $imageFile->getClientOriginalExtension();
                $relativeDir = 'front/images/banner_images';
                $destinationDir = public_path($relativeDir);

                if (!File::exists($destinationDir)) {
                    File::makeDirectory($destinationDir, 0755, true);
                }

                $savePath = $destinationDir . DIRECTORY_SEPARATOR . $imageName;

                // Ensure GD is available for Intervention Image
                if (!extension_loaded('gd')) {
                    return back()->withErrors(['image' => 'GD Library extension is not enabled for the web server. Enable extension=gd in Apache PHP (php.ini) and restart Apache.'])->withInput();
                }

                try {
                    // Force GD driver and save (resize can be added if needed)
                    Image::configure(['driver' => 'gd']);
                    Image::make($imageFile->getRealPath())->save($savePath);
                } catch (\Throwable $e) {
                    return back()->withErrors(['image' => 'Image processing failed: ' . $e->getMessage()])->withInput();
                }

                // Delete old image when updating
                if ($id && !empty($banner->image)) {
                    $oldPath = $destinationDir . DIRECTORY_SEPARATOR . $banner->image;
                    if (File::exists($oldPath)) {
                        File::delete($oldPath);
                    }
                }

                $banner->image = $imageName;
            }

            $banner->save();

            return redirect()->to(url('admin/banners'))
                ->with('success_message', $id ? 'Banner updated successfully' : 'Banner added successfully');
        }

        $title = $id ? 'Edit Banner' : 'Add Banner';
        return view('admin.banners.add_edit_banner', compact('banner', 'title', 'logos', 'headerLogo'));
    }

    // AJAX: Update status (active/inactive)
    public function updateBannerStatus(Request $request)
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $request->validate([
            'banner_id' => 'required|integer|exists:banners,id',
            'status'    => 'required|in:0,1',
        ]);

        $banner = Banner::findOrFail($request->input('banner_id'));
        $banner->status = (int) $request->input('status');
        $banner->save();

        return response()->json(['status' => 'success']);
    }

    // Delete banner (and file)
    public function deleteBanner($id)
    {
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();
        $banner = Banner::findOrFail($id);

        if (!empty($banner->image)) {
            $relativeDir = 'front/images/banner_images';
            $filePath = public_path($relativeDir . DIRECTORY_SEPARATOR . $banner->image);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }

        $banner->delete();

        return redirect()->back()->with('success_message', 'Banner deleted successfully', 'logos');
        return view('admin.banners.banners', compact('banners', 'logos', 'headerLogo'));
    }

}
