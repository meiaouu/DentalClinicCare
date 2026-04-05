<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'role_name' => RoleName::ADMIN->value,
                'description' => 'Full system access',
            ],
            [
                'role_name' => RoleName::STAFF->value,
                'description' => 'Clinic assistant and front desk staff',
            ],
            [
                'role_name' => RoleName::DENTIST->value,
                'description' => 'Dentist user with patient chart access',
            ],
            [
                'role_name' => RoleName::PATIENT->value,
                'description' => 'Registered patient account',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['role_name' => $role['role_name']],
                $role
            );
        }
    }
}
