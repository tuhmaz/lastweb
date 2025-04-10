<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
        return view('content.dashboard.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('content.dashboard.permissions.create');
    }

    public function store(Request $request)
{
    try {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions'],
            'guard_name' => ['required', 'string', 'in:sanctum,web,api']
        ]);

        Permission::create([
            'name' => $request->name,
            'guard_name' => $request->guard_name
        ]);

        return redirect()->route('dashboard.permissions.index')
            ->with('success', __('Permission created successfully.'));
    } catch (\Exception $e) {
        return back()->withInput()
            ->withErrors(['error' => __('Failed to create permission. Please try again.')]);
    }
}

    public function edit(Permission $permission)
    {
        return view('content.dashboard.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,' . $permission->id],
            'guard_name' => ['required', 'string', 'in:sanctum,web,api']
        ]);

        $permission->update([
            'name' => $request->name,
            'guard_name' => $request->guard_name ?? 'sanctum'
        ]);

        return redirect()->route('dashboard.permissions.index')
            ->with('success', __('Permission updated successfully.'));
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('dashboard.permissions.index')
            ->with('success', __('Permission deleted successfully.'));
    }
}
