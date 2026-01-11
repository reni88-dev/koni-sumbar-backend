<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\StoreUserRequest;
use App\Http\Requests\Master\UpdateUserRequest;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $this->authorize('users.view');
        
        $query = User::with('role');

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->has('role_id') && $request->role_id) {
            $query->where('role_id', $request->role_id);
        }

        // Pagination
        $perPage = $request->get('per_page', 10);
        $users = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($users);
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request)
    {
        $this->authorize('users.create');
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        $user->load('role');

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
        ], 201);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load('role');
        return response()->json($user);
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('users.edit');
        
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $user->load('role');

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user,
        ]);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        $this->authorize('users.delete');
        
        // Prevent deleting yourself
        if (auth()->id() === $user->id) {
            return response()->json([
                'message' => 'You cannot delete your own account',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }

    /**
     * Get all roles for dropdown.
     */
    public function roles()
    {
        $roles = Role::all();
        return response()->json($roles);
    }
}
