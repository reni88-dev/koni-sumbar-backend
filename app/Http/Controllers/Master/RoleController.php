<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index(Request $request)
    {
        $this->authorize('roles.view');
        
        $query = Role::with('permissions');

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%");
            });
        }

        $roles = $query->orderBy('id')->get();

        return response()->json($roles);
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $this->authorize('roles.create');
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:roles,name',
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ], [
            'name.required' => 'Nama role wajib diisi',
            'name.unique' => 'Nama role sudah digunakan',
            'display_name.required' => 'Display name wajib diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $role = Role::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        $role->load('permissions');

        return response()->json([
            'message' => 'Role created successfully',
            'role' => $role,
        ], 201);
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $role->load('permissions');
        return response()->json($role);
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role)
    {
        $this->authorize('roles.edit');
        
        // Prevent editing super_admin role name
        if ($role->isSuperAdmin() && $request->name !== 'super_admin') {
            return response()->json([
                'message' => 'Cannot change super_admin role name',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:roles,name,' . $role->id,
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $role->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        $role->load('permissions');

        return response()->json([
            'message' => 'Role updated successfully',
            'role' => $role,
        ]);
    }

    /**
     * Update only permissions for a role.
     */
    public function updatePermissions(Request $request, Role $role)
    {
        $this->authorize('roles.permissions');
        
        // Super admin doesn't need permissions assigned
        if ($role->isSuperAdmin()) {
            return response()->json([
                'message' => 'Super Admin has all permissions by default',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $role->syncPermissions($request->permissions);
        $role->load('permissions');

        return response()->json([
            'message' => 'Permissions updated successfully',
            'role' => $role,
        ]);
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role)
    {
        $this->authorize('roles.delete');
        
        // Prevent deleting super_admin role
        if ($role->isSuperAdmin()) {
            return response()->json([
                'message' => 'Cannot delete super_admin role',
            ], 403);
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete role with assigned users',
            ], 400);
        }

        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully',
        ]);
    }

    /**
     * Get all available permissions grouped.
     */
    public function permissions()
    {
        $permissions = Permission::all()->groupBy('group');
        return response()->json($permissions);
    }
}
