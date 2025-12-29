<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserRoleController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();
        return view('admin.user_role.index', compact('users'));
    }

    public function edit(User $user)
    {
        abort_unless(Auth::user()->hasRole('admin'), 403);

        $roles = Role::all();
        return view('admin.user_role.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        abort_unless(Auth::user()->hasRole('admin'), 403);

        $request->validate([
            'roles' => 'array'
        ]);

        $user->roles()->sync($request->roles ?? []);

        return redirect()
            ->route('user-roles.index')
            ->with('success', 'Role user berhasil diperbarui');
    }
}
