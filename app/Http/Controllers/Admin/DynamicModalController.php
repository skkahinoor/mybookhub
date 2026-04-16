<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DynamicModal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Intervention\Image\Facades\Image;

class DynamicModalController extends Controller
{
    public function index()
    {
        Session::put('page', 'dynamic_modals');
        $dynamicModals = DynamicModal::orderByDesc('id')->get();

        return view('admin.dynamic_modals.index', compact('dynamicModals'));
    }

    public function addEdit(Request $request, $id = null)
    {
        Session::put('page', 'dynamic_modals');

        // This module is single-entry only. Admin always uses Add form
        // and on submit we replace old data with a fresh insert.
        $dynamicModal = new DynamicModal();
        $title = 'Add Dynamic Modal';

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'text'   => 'nullable|string',
                'link'   => 'nullable|string|max:255',
                'status' => 'nullable|in:0,1',
                'image'  => 'nullable|image|mimes:jpeg,png,jpg,webp,avif,gif|max:5120',
            ]);

            // Remove old stored image(s) and old record(s) before insert.
            $existingModals = DynamicModal::get();
            foreach ($existingModals as $existingModal) {
                if (!empty($existingModal->getRawOriginal('image'))) {
                    $oldImagePath = public_path('front/images/dynamic_modal_images/' . $existingModal->getRawOriginal('image'));
                    if (File::exists($oldImagePath)) {
                        File::delete($oldImagePath);
                    }
                }
            }
            DynamicModal::query()->delete();

            $newDynamicModal = new DynamicModal();
            $newDynamicModal->text = $validated['text'] ?? null;
            $newDynamicModal->link = $validated['link'] ?? null;
            $newDynamicModal->status = isset($validated['status']) ? (int) $validated['status'] : 1;

            if ($request->hasFile('image')) {
                $imageFile = $request->file('image');
                $imageName = uniqid('dynamic_modal_') . '.' . $imageFile->getClientOriginalExtension();
                $relativeDir = 'front/images/dynamic_modal_images';
                $destinationDir = public_path($relativeDir);

                if (!File::exists($destinationDir)) {
                    File::makeDirectory($destinationDir, 0755, true);
                }

                if (!extension_loaded('gd')) {
                    return back()->withErrors(['image' => 'GD Library extension is not enabled for the web server.'])->withInput();
                }

                try {
                    Image::configure(['driver' => 'gd']);
                    Image::make($imageFile->getRealPath())->save($destinationDir . DIRECTORY_SEPARATOR . $imageName);
                } catch (\Throwable $e) {
                    return back()->withErrors(['image' => 'Image processing failed: ' . $e->getMessage()])->withInput();
                }

                $newDynamicModal->image = $imageName;
            }

            $newDynamicModal->save();

            return redirect()->to(url('admin/dynamic-modals'))
                ->with('success_message', 'Dynamic modal replaced successfully');
        }

        return view('admin.dynamic_modals.add_edit', compact('dynamicModal', 'title'));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'dynamic_modal_id' => 'required|integer|exists:dynamic_modals,id',
            'status'           => 'required|in:0,1',
        ]);

        $dynamicModal = DynamicModal::findOrFail($request->input('dynamic_modal_id'));
        $dynamicModal->status = (int) $request->input('status');
        $dynamicModal->save();

        return response()->json(['status' => 'success']);
    }

    public function delete($id)
    {
        $dynamicModal = DynamicModal::findOrFail($id);

        if (!empty($dynamicModal->getRawOriginal('image'))) {
            $filePath = public_path('front/images/dynamic_modal_images/' . $dynamicModal->getRawOriginal('image'));
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }

        $dynamicModal->delete();

        return redirect()->back()->with('success_message', 'Dynamic modal deleted successfully');
    }
}

