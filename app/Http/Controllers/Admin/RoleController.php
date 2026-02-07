<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        Session::put('page', 'roles');
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $roles = Role::with('permissions')->orderBy('id', 'desc')->get();
        return view('admin.roles.index', compact('roles', 'logos', 'headerLogo'));
    }

    public function create()
    {
        Session::put('page', 'roles');
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $permissions = Permission::all();
        return view('admin.roles.create', compact('permissions', 'logos', 'headerLogo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'nullable|array'
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);
        
        if ($request->has('permissions')) {
            // Convert permission IDs to Permission models
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->syncPermissions($permissions);
        }

        return redirect()->route('admin.roles.index')->with('success_message', 'Role created successfully.');
    }

    public function edit($id)
    {
        Session::put('page', 'roles');
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions', 'logos', 'headerLogo'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        
        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
            'permissions' => 'nullable|array'
        ]);

        $role->update(['name' => $request->name]);
        
        if ($request->has('permissions')) {
            // Convert permission IDs to Permission models
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->syncPermissions($permissions);
        } else {
            $role->syncPermissions([]);
        }

        // Check if request is AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully.',
                'role' => $role->load('permissions')
            ]);
        }

        return redirect()->route('admin.roles.index')->with('success_message', 'Role updated successfully.');
    }


    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        
        // Prevent deleting core roles if necessary
        if (in_array($role->name, ['admin', 'vendor', 'sales', 'student'])) {
            return redirect()->route('admin.roles.index')->with('error_message', 'Core roles cannot be deleted.');
        }

        $role->delete();
        return redirect()->route('admin.roles.index')->with('success_message', 'Role deleted successfully.');
    }

    public function assignPermissions()
    {
        Session::put('page', 'roles');
        $headerLogo = HeaderLogo::first();
        $logos = HeaderLogo::first();
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        
        return view('admin.roles.assign_permissions', compact('roles', 'permissions', 'logos', 'headerLogo'));
    }

    public function updatePermissions(Request $request)
    {
        $roleId = $request->role_id;
        $role = Role::findOrFail($roleId);

        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->syncPermissions($permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->back()->with('success_message', 'Permissions updated successfully for role: ' . ucfirst($role->name));
    }
}
