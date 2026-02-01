<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SettingsController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->with('roles')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.settings.index', [
            'users' => $users,
            'roles' => [
                UserRole::ROLE_SUPERADMIN => 'Super Admin',
                UserRole::ROLE_ADMIN => 'Admin',
            ],
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = User::query()->create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
        ]);

        $user->assignRole($request->validated('role'));

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'User created successfully.');
    }
}
