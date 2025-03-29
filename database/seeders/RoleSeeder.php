<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Create the admin role
        $adminRole = Role::create(['name' => 'admin']);

        // Assign the admin role to the first user
        $user = User::first();
        if ($user) {
            $user->assignRole($adminRole);
        }
    }
}
