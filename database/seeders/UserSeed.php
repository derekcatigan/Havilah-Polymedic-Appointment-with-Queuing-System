<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Demo Account
        User::create([
            'name' => 'Admin Demo User',
            'email' => 'admin@email.com',
            'role' => UserRole::Admin,
            'contact_number' => '09665443796',
            'address' => 'Sogod, Southern Leyte',
            'password' => bcrypt('password'),
        ]);

        // Doctor Demo Account
        User::create([
            'name' => 'Doctor Demo User',
            'email' => 'doctor@email.com',
            'role' => UserRole::Doctor,
            'contact_number' => '09123456789',
            'address' => 'Sogod, Southern Leyte',
            'password' => bcrypt('password'),
        ]);

        // Staff Demo Account
        User::create([
            'name' => 'Staff Demo User',
            'email' => 'staff@email.com',
            'role' => UserRole::Staff,
            'contact_number' => '09234567890',
            'address' => 'Sogod, Southern Leyte',
            'password' => bcrypt('password'),
        ]);

        // Patient Demo Account
        User::create([
            'name' => 'Patient Demo User',
            'email' => 'patient@email.com',
            'role' => UserRole::Patient,
            'contact_number' => '09345678901',
            'address' => 'Sogod, Southern Leyte',
            'password' => bcrypt('password'),
        ]);
    }
}
