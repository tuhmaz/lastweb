<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->get();
        return view('content.dashboard.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('content.dashboard.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array'
        ]);

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return redirect()->route('dashboard.roles.index')
            ->with('success', __('Role created successfully.'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        return view('content.dashboard.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'array'
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return redirect()->route('dashboard.roles.index')
            ->with('success', __('Role updated successfully.'));
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('dashboard.roles.index')
            ->with('success', __('Role deleted successfully.'));
    }
}
