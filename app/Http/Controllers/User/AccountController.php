<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\HeaderLogo;
use App\Models\InstitutionManagement;
use App\Models\InstitutionClass;
use App\Models\AcademicProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $countries = Country::where('status', true)->get();
        $institutions = InstitutionManagement::where('status', 1)->orderBy('name')->get();
        $user      = User::with([
            'country',
            'state',
            'district',
            'block',
            'institution',
            'institutionClass.subcategory',
            'academicProfile',
        ])->find(Auth::id());
        $logos = HeaderLogo::first();
        $headerLogo = HeaderLogo::first();

        // Compute simple profile completion percentage
        $completionFields = [
            'name'        => $user->name ?? null,
            'email'       => $user->email ?? null,
            'phone'       => $user->phone ?? null,
            'pincode'     => $user->pincode ?? null,
            'address'     => $user->address ?? null,
            'country_id'  => $user->country_id ?? null,
            'state_id'    => $user->state_id ?? null,
            'district_id' => $user->district_id ?? null,
            'block_id'    => $user->block_id ?? null,
            'bank_name'   => $user->bank_name ?? null,
            'upi_id'      => $user->upi_id ?? null,
        ];

        $totalFields  = count($completionFields);
        $filledFields = collect($completionFields)->filter(function ($value) {
            return ! is_null($value) && $value !== '';
        })->count();

        $profileCompletion = $totalFields > 0 ? round(($filledFields / $totalFields) * 100) : 0;

        // Handle POST request for form submission
        if ($request->isMethod('post')) {
            $data = $request->all();

            // Validation
            try {
                $validated = $request->validate([
                    'email'       => 'required|email|max:100|unique:users,email,' . Auth::id(),
                    'name'        => 'required|string|max:100',
                    'address'     => 'nullable|string|max:100',
                    'country_id'  => 'nullable|exists:countries,id',
                    'state_id'    => 'nullable|exists:states,id',
                    'district_id' => 'nullable|exists:districts,id',
                    'block_id'    => 'nullable|exists:blocks,id',
                    'mobile'      => 'required|numeric|digits:10',
                    'pincode'     => 'required|digits:6',
                    'institution_id' => 'nullable|exists:institution_managements,id',
                    'board_id'       => 'nullable|exists:categories,id',
                    'institution_classes_id' => 'nullable|exists:institution_classes,id',
                    'bank_name'      => 'nullable|string|max:100',
                    'account_holder_name' => 'nullable|string|max:100',
                    'account_number' => 'nullable|string|max:30',
                    'ifsc_code'      => 'nullable|string|max:20',
                    'upi_id'         => 'nullable|string|max:100',
                ]);

                $user = User::where('id', Auth::id())->first();
                $user->update([
                    'email'       => $validated['email'],
                    'name'        => $validated['name'],
                    'phone'       => $validated['mobile'],
                    'country_id'  => $validated['country_id'] ?? null,
                    'state_id'    => $validated['state_id'] ?? null,
                    'district_id' => $validated['district_id'] ?? null,
                    'block_id'    => $validated['block_id'] ?? null,
                    'pincode'     => $validated['pincode'],
                    'address'     => $validated['address'] ?? null,
                    'institution_id' => $validated['institution_id'] ?? $user->institution_id,
                    'institution_classes_id' => $validated['institution_classes_id'] ?? $user->institution_classes_id,
                    'bank_name'   => $validated['bank_name'] ?? null,
                    'account_holder_name' => $validated['account_holder_name'] ?? null,
                    'account_number' => $validated['account_number'] ?? null,
                    'ifsc_code'   => $validated['ifsc_code'] ?? null,
                    'upi_id'      => $validated['upi_id'] ?? null,
                ]);

                // Sync academic profile if institution or class changed
                if (!empty($validated['institution_id']) || !empty($validated['institution_classes_id'])) {
                    $institution = null;
                    if (!empty($validated['institution_id'])) {
                        $institution = InstitutionManagement::find($validated['institution_id']);
                    } elseif ($user->institution_id) {
                        $institution = InstitutionManagement::find($user->institution_id);
                    }

                    $profileData = [
                        'education_level_id' => $institution?->type,
                        'board_id'           => $validated['board_id'] ?? $institution?->board,
                        'class_id'           => $validated['institution_classes_id'] ?? $user->institution_classes_id,
                    ];

                    if ($user->academicProfile) {
                        $user->academicProfile->update($profileData);
                    } else {
                        $user->academicProfile()->create($profileData);
                    }
                }

                // Refresh user model to get updated values
                $user->refresh();

                // Recalculate profile completion after update
                $completionFields = [
                    'name'        => $user->name ?? null,
                    'email'       => $user->email ?? null,
                    'phone'       => $user->phone ?? null,
                    'pincode'     => $user->pincode ?? null,
                    'address'     => $user->address ?? null,
                    'country_id'  => $user->country_id ?? null,
                    'state_id'    => $user->state_id ?? null,
                    'district_id' => $user->district_id ?? null,
                    'block_id'    => $user->block_id ?? null,
                    'bank_name'   => $user->bank_name ?? null,
                    'upi_id'      => $user->upi_id ?? null,
                ];

                $totalFields  = count($completionFields);
                $filledFields = collect($completionFields)->filter(function ($value) {
                    return ! is_null($value) && $value !== '';
                })->count();

                $profileCompletion = $totalFields > 0 ? round(($filledFields / $totalFields) * 100) : 0;

                // Return JSON response for AJAX requests
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Account details updated successfully!',
                        'profileCompletion' => $profileCompletion,
                    ]);
                }

                // Redirect back with success message for non-AJAX requests
                return redirect()->back()->with('success_message', 'Account details updated successfully!');
            } catch (\Illuminate\Validation\ValidationException $e) {
                // Return JSON response for AJAX validation errors
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors'  => $e->errors(),
                    ], 422);
                }
                // Re-throw for non-AJAX requests to show normal validation errors
                throw $e;
            }
        }

        return view('user.profile.accountdetails', compact('user', 'countries', 'institutions', 'profileCompletion', 'logos', 'headerLogo'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        // Save profile fields here...

        // Calculate profile completion
        $completedFields = 0;
        $totalFields     = 5;

        if ($user->name) {
            $completedFields++;
        }

        if ($user->email) {
            $completedFields++;
        }

        if ($user->phone) {
            $completedFields++;
        }

        if ($user->dob) {
            $completedFields++;
        }

        if ($user->address) {
            $completedFields++;
        }

        $profileCompletion = round(($completedFields / $totalFields) * 100);

        return response()->json([
            'profileCompletion' => $profileCompletion,
        ]);
    }

    /**
     * Update the user's profile image via AJAX.
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048', // max 2MB
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Define upload directory in public folder
        $uploadDir = public_path('asset/user');

        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Delete old image if exists (handle both storage/ and asset/user/ paths)
        if (!empty($user->profile_image)) {
            $oldImagePath = $user->profile_image;

            // If old path is in storage, try to delete from storage
            if (strpos($oldImagePath, 'storage/') !== false) {
                $oldPath = str_replace('storage/', '', $oldImagePath);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            } else {
                // If old path is in asset/user/, delete from public directory
                $oldFullPath = public_path($oldImagePath);
                if (file_exists($oldFullPath)) {
                    unlink($oldFullPath);
                }
            }
        }

        // Store new image in public/asset/user directory
        $file = $request->file('avatar');
        $fileName = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($uploadDir, $fileName);

        // Store relative path for database (asset/user/filename)
        $relativePath = 'asset/user/' . $fileName;

        $user->profile_image = $relativePath;
        $user->save();

        return response()->json([
            'success'    => true,
            'message'    => 'Profile photo updated successfully.',
            'avatar_url' => asset($relativePath),
        ]);
    }

    /**
     * AJAX: Get boards for a given institution (based on institution_managements table).
     */
    public function getAcademicBoards(Request $request)
    {
        $institutionId = $request->query('institution_id');

        if (!$institutionId) {
            return response()->json([], 200);
        }

        $institution = InstitutionManagement::with('category')->find($institutionId);

        if (!$institution || !$institution->category) {
            return response()->json([], 200);
        }

        return response()->json([
            [
                'id'   => $institution->category->id,
                'name' => $institution->category->category_name,
            ],
        ]);
    }

    /**
     * AJAX: Get classes for a given institution and optional board.
     */
    public function getAcademicClasses(Request $request)
    {
        $institutionId = $request->query('institution_id');
        if (!$institutionId) {
            return response()->json([], 200);
        }

        $classes = InstitutionClass::with('subcategory')
            ->where('institution_id', $institutionId)
            ->get()
            ->map(function ($item) {
            return [
                'id'        => $item->id,
                'name'      => optional($item->subcategory)->subcategory_name,
                'strength'  => $item->total_strength,
            ];
        });

        return response()->json($classes);
    }
}
