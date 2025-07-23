<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'Admin',
            'CRM Agent',
            'Doctor',
            'Patient',
            'Lab Manager',
        ];
        foreach ($roles as $role) {
            $email = strtolower(str_replace(' ', '.', $role)) . '@email.com';
            User::createOrFirst([
                'name' => $role,
                'email' => $email,
                'password' => bcrypt('Password@123'), // Use a secure password
            ])->assignRole($role);
        }
    }
}
