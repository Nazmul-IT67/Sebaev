<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserPermissionController extends Controller
{
    public function index()
    {
        $roles = Role::whereHas('permissions')->with('permissions')->get();
        return view('backend.layouts.user_role.permission.index', compact('roles'));
    }

    public function create()
    {
        $roles = Role::all();
        $permissions = Permission::all()->sortBy('name')->groupBy(function ($permission) {
            return Str::before($permission->name, '_');
        });
        return view('backend.layouts.user_role.permission.create', compact('permissions', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::findOrFail($request->role_id);
        if ($request->filled('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->syncPermissions($permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('admin.permissions.index')->with('t-success', 'Created successfully');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $roles = Role::all();
        $permissions = Permission::all()->sortBy('name')->groupBy(function ($permission) {
            return Str::before($permission->name, '_');
        });
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        return view('backend.layouts.user_role.permission.edit', compact('role', 'roles', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::findOrFail($id);

        if ($request->filled('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->syncPermissions($permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('admin.permissions.index')->with('t-success', 'Permissions updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json(['t-success' => true, 'message' => 'Deleted successfully.']);
    }

}
