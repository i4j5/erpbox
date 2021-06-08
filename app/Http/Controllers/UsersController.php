<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Actions\Fortify\CreateNewUser;
use App\Actions\Jetstream\DeleteUser;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('read user')) {
            abort(403);
        }

        $users = User::all();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        if (!Auth::user()->can('create user')) {
            abort(403);
        }

        $roles = Role::all();

        return view('users.create', compact('roles'));
    }

    public function store(Request $request, CreateNewUser $newUser)
    {
        if (!Auth::user()->can('create user')) {
            abort(403);
        }

        $user = $newUser->create($request->input());

        $user->assignRole($request->input('roles'));

        return redirect()->route('users.index');
    }

    public function show(User $user)
    {
        if (!Auth::user()->can('read user')) {
            abort(403);
        }
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        if (!Auth::user()->can('update user')) {
            abort(403);
        }

        $roles = Role::all();
        $userRoleArray = [];

        foreach ($user->getRoleNames() as $role) {
            $userRoleArray[$role] = $role;
        }

        return view('users.edit', compact('user', 'roles', 'userRoleArray'));
    }

    public function update(Request $request, User $user, UpdateUserProfileInformation $userProfileInformation)
    {
        if (!Auth::user()->can('update user')) {
            abort(403);
        }

        $userProfileInformation->update($user, $request->input());

        $user->syncRoles($request->input('roles'));

        return redirect()->route('users.index');
    }

    public function destroy(User $user, DeleteUser $deleteUser)
    {
        if (!Auth::user()->can('updete delete')) {
            abort(403);
        }

        $deleteUser->delete($user);

        return redirect()->route('users.index');
    }
}
