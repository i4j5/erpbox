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

class UsersController extends Controller
{
    public function index()
    {
        $users = User::all();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();

        return view('users.create', compact('roles'));
    }

    public function store(Request $request, CreateNewUser $newUser)
    {
        $user = $newUser->create($request->input());

        $user->assignRole($request->input('roles'));

        return redirect()->route('users.index');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $userRoleArray = [];

        foreach ($user->getRoleNames() as $role) {
            $userRoleArray[$role] = $role;
        }

        return view('users.edit', compact('user', 'roles', 'userRoleArray'));
    }

    public function update(Request $request, User $user, UpdateUserProfileInformation $userProfileInformation)
    {

        $userProfileInformation->update($user, $request->input());
        
        $user->syncRoles($request->input('roles'));

        return redirect()->route('users.index');
    }

    public function destroy(User $user, DeleteUser $deleteUser)
    {
        $deleteUser->delete($user);

        return redirect()->route('users.index');
    }
}