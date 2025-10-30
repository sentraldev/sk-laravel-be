<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class AdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure the admin role exists
        $role = Role::query()->firstOrCreate(
            ['name' => 'admin', 'guard_name' => config('auth.defaults.guard', 'web')],
            []
        );

        // Optionally, ensure a broad permission exists and is assigned to admin
        // e.g., manage users
        $manageUsers = Permission::query()->firstOrCreate(
            ['name' => 'users.manage', 'guard_name' => config('auth.defaults.guard', 'web')]
        );
        if (! $role->hasPermissionTo($manageUsers)) {
            $role->givePermissionTo($manageUsers);
        }

        // Assign the role to the specific user if present
        $email = 'admin@sentralkomputer.com';
        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            // If user doesn't exist, you can create a placeholder or just log a notice
            // Here we create a minimal user to ensure access, with a random password.
            $user = User::query()->create([
                'name' => 'Administrator',
                'email' => $email,
                'password' => bcrypt(str()->password(16)),
            ]);
        }

        if (! $user->hasRole('admin')) {
            $user->assignRole($role);
        }
    }
}
