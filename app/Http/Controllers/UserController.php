<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['roles', 'branch'])->get();
        return view('admin.user.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        $branches = Branch::all();

        return view('admin.user.create', compact('roles', 'branches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:6',
            'role_id'   => 'required|exists:roles,id',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $role = Role::find($data['role_id']);
        if ($role->name === 'kasir' && empty($data['branch_id'])) {
            return back()->withErrors([
                'branch_id' => 'Kasir wajib memilih cabang'
            ])->withInput();
        }

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'branch_id' => $data['branch_id'],
        ]);

        $user->roles()->sync([$data['role_id']]);

        return redirect()->route('users.index')->with('success', 'User berhasil dibuat');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $branches = Branch::all();

        return view('admin.user.edit', compact('user', 'roles', 'branches'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'password'  => 'nullable|min:6',
            'role_id'   => 'required|exists:roles,id',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $role = Role::find($data['role_id']);
        if ($role->name === 'kasir' && empty($data['branch_id'])) {
            return back()->withErrors([
                'branch_id' => 'Kasir wajib memilih cabang'
            ])->withInput();
        }

        $user->update([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'branch_id' => $data['branch_id'],
            'password'  => $data['password']
                ? Hash::make($data['password'])
                : $user->password,
        ]);

        $user->roles()->sync([$data['role_id']]);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus');
    }
}
