<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('role_name', RoleName::ADMIN->value)->firstOrFail();

        User::updateOrCreate(
            ['email' => 'admin@dentalcliniccare.test'],
            [
                'role_id' => $adminRole->role_id,
                'first_name' => 'System',
                'last_name' => 'Admin',
                'username' => 'admin',
                'contact_number' => '09170000000',
                'password' => Hash::make('password123'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
