<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Intervention\Image\Facades\Image;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    /**
     * List all staff members (users who have a custom/staff role,
     * excluding the core system roles).
     */
    public function index(Request $request)
    {
        Session::put('page', 'view_staff');
        $headerLogo = HeaderLogo::first();
        $logos      = HeaderLogo::first();

        if ($request->ajax()) {
            // All non-core roles are "staff"
            $coreRoles = ['admin', 'superadmin', 'vendor', 'sales', 'student', 'user', 'delivery_agent'];
            $staffRoles = Role::whereNotIn('name', $coreRoles)->pluck('name')->toArray();

            $query = User::query();

            if (!empty($staffRoles)) {
                $query->role($staffRoles, 'web');
            } else {
                // No staff roles exist yet – return empty
                $query->whereRaw('0 = 1');
            }

            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('image', function ($row) {
                    if (!empty($row->profile_image)) {
                        $url = asset('admin/images/photos/' . $row->profile_image);
                        return '<img src="' . $url . '" alt="Staff Image" style="width:50px;height:50px;object-fit:cover;border-radius:50%;">';
                    }
                    $url = asset('admin/images/photos/no-image.gif');
                    return '<img src="' . $url . '" alt="No Image" style="width:50px;height:50px;object-fit:cover;border-radius:50%;">';
                })
                ->addColumn('role_name', function ($row) {
                    $role = $row->roles->first();
                    return $role
                        ? '<span class="badge badge-primary">' . ucfirst($role->name) . '</span>'
                        : '<span class="badge badge-secondary">N/A</span>';
                })
                ->addColumn('status', function ($row) {
                    $url    = route('admin.staff.update_status');
                    $status = $row->status == 1 ? 'Active' : 'Inactive';
                    $icon   = $row->status == 1 ? 'mdi-bookmark-check' : 'mdi-bookmark-outline';
                    return '<a class="updateStaffStatus" id="staff-' . $row->id . '" staff_id="' . $row->id . '" data-url="' . $url . '" href="javascript:void(0)">
                                <i style="font-size:25px" class="mdi ' . $icon . '" status="' . $status . '"></i>
                            </a>';
                })
                ->addColumn('actions', function ($row) {
                    $html  = '<div class="d-flex align-items-center" style="gap:10px;">';
                    $html .= '<a href="' . route('admin.staff.edit', $row->id) . '" title="Edit"><i style="font-size:20px" class="mdi mdi-pencil"></i></a>';
                    $html .= '<form action="' . route('admin.staff.destroy', $row->id) . '" method="POST" onsubmit="return confirm(\'Are you sure you want to delete this staff member?\')" style="display:inline;">'
                           . csrf_field()
                           . method_field('DELETE')
                           . '<button type="submit" style="background:none;border:none;padding:0;"><i class="mdi mdi-delete" style="font-size:20px;color:#e74c3c;"></i></button>'
                           . '</form>';
                    $html .= '</div>';
                    return $html;
                })
                ->rawColumns(['image', 'role_name', 'status', 'actions'])
                ->make(true);
        }

        return view('admin.admins.staff', compact('logos', 'headerLogo'));
    }

    /**
     * Show the Add Staff form.
     */
    public function create()
    {
        Session::put('page', 'view_staff');
        $headerLogo = HeaderLogo::first();
        $logos      = HeaderLogo::first();

        // Only non-core roles can be assigned to staff
        $coreRoles = ['admin', 'superadmin', 'vendor', 'sales', 'student', 'user'];
        $roles = Role::whereNotIn('name', $coreRoles)->get();

        return view('admin.admins.add_staff', compact('logos', 'headerLogo', 'roles'));
    }

    /**
     * Store a new staff member.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'                  => 'required|regex:/^[\pL\s\-&\.]+$/u',
            'email'                 => 'required|email|unique:users,email',
            'mobile'                => 'required|numeric',
            'role_id'               => 'required|exists:roles,id',
            'password'              => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
        ], [
            'name.required'     => 'Name is required',
            'name.regex'        => 'Valid Name is required',
            'email.required'    => 'Email is required',
            'email.unique'      => 'Email already exists',
            'mobile.required'   => 'Mobile is required',
            'mobile.numeric'    => 'Valid Mobile is required',
            'role_id.required'  => 'Please select a role',
            'role_id.exists'    => 'Selected role is invalid',
            'password.required' => 'Password is required',
            'password.min'      => 'Password must be at least 6 characters',
            'password.confirmed'=> 'Passwords do not match',
        ]);

        // Handle photo upload
        $imageName = '';
        if ($request->hasFile('admin_image')) {
            $image_tmp = $request->file('admin_image');
            if ($image_tmp->isValid()) {
                $extension = $image_tmp->getClientOriginalExtension();
                $imageName = rand(111, 99999) . '.' . $extension;
                $imagePath = 'admin/images/photos/' . $imageName;
                Image::make($image_tmp)->save($imagePath);
            }
        }

        $role = Role::findOrFail($request->role_id);

        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'phone'         => $request->mobile,
            'password'      => Hash::make($request->password),
            'profile_image' => $imageName,
            'role_id'       => $role->id,
            'status'        => 1,
        ]);

        // Assign the Spatie role (inherits all permissions attached to that role)
        $user->assignRole($role);

        return redirect()->route('admin.staff.index')
            ->with('success_message', 'Staff member added successfully!');
    }

    /**
     * Show Edit form for a staff member.
     */
    public function edit($id)
    {
        Session::put('page', 'view_staff');
        $headerLogo = HeaderLogo::first();
        $logos      = HeaderLogo::first();

        $staff = User::with('roles')->findOrFail($id);

        $coreRoles = ['admin', 'superadmin', 'vendor', 'sales', 'student', 'user'];
        $roles     = Role::whereNotIn('name', $coreRoles)->get();

        $currentRoleId = $staff->roles->first()?->id;

        return view('admin.admins.edit_staff', compact('staff', 'roles', 'currentRoleId', 'logos', 'headerLogo'));
    }

    /**
     * Update a staff member.
     */
    public function update(Request $request, $id)
    {
        $staff = User::findOrFail($id);

        $request->validate([
            'name'    => 'required|regex:/^[\pL\s\-&\.]+$/u',
            'email'   => 'required|email|unique:users,email,' . $id,
            'mobile'  => 'required|numeric',
            'role_id' => 'required|exists:roles,id',
        ]);

        // Handle photo upload
        $imageName = $staff->profile_image;
        if ($request->hasFile('admin_image')) {
            $image_tmp = $request->file('admin_image');
            if ($image_tmp->isValid()) {
                $extension = $image_tmp->getClientOriginalExtension();
                $imageName = rand(111, 99999) . '.' . $extension;
                Image::make($image_tmp)->save('admin/images/photos/' . $imageName);
            }
        }

        $updateData = [
            'name'          => $request->name,
            'email'         => $request->email,
            'phone'         => $request->mobile,
            'profile_image' => $imageName,
            'role_id'       => $request->role_id,
        ];

        if (!empty($request->password)) {
            $request->validate(['password' => 'min:6|confirmed']);
            $updateData['password'] = Hash::make($request->password);
        }

        $staff->update($updateData);

        // Sync the Spatie role
        $role = Role::findOrFail($request->role_id);
        $staff->syncRoles([$role]);

        return redirect()->route('admin.staff.index')
            ->with('success_message', 'Staff member updated successfully!');
    }

    /**
     * Delete a staff member.
     */
    public function destroy($id)
    {
        if ($id == Auth::guard('admin')->user()->id) {
            return redirect()->back()->with('error_message', 'You cannot delete yourself!');
        }

        $staff = User::findOrFail($id);

        // Delete profile image if exists
        if (!empty($staff->profile_image) && file_exists(public_path('admin/images/photos/' . $staff->profile_image))) {
            unlink(public_path('admin/images/photos/' . $staff->profile_image));
        }

        $staff->delete();

        return redirect()->route('admin.staff.index')
            ->with('success_message', 'Staff member deleted successfully!');
    }

    /**
     * Toggle staff account status via AJAX.
     */
    public function updateStatus(Request $request)
    {
        if ($request->ajax()) {
            $data      = $request->all();
            $staffUser = User::find($data['staff_id']);
            if (!$staffUser) {
                return response()->json(['status' => 'error', 'message' => 'User not found.'], 404);
            }

            $status = ($data['status'] === 'Active') ? 0 : 1;
            $staffUser->update(['status' => $status]);

            return response()->json(['status' => $status, 'staff_id' => $data['staff_id']]);
        }
    }
}
