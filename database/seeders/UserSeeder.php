<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->updateOrCreate(
            ['email' => 'anasuharosli@gmail.com'],
            [
                'name' => 'Anasuha',
                'password' => Hash::make(config('seeding.user.password')),
            ]
        );

        $user->assignRole(UserRole::ROLE_SUPERADMIN);
    }
}
