<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 60; $i++) {
            // Auto-generate IDs
            $patientId = 'PID-' . strtoupper(Str::random(6));
            $patientNumber = 'PN-' . str_pad(User::where('role', UserRole::Patient)->count() + 1, 5, '0', STR_PAD_LEFT);

            // Generate random name and address
            $name = fake()->name();
            $address = fake()->address();

            // Email pattern: unique @email.com
            $email = 'user' . $i . '@email.com';

            // Phone: random 11-digit number (unique)
            $phone = '09' . rand(100000000, 999999999);

            // Avoid duplicate name+phone combo
            if (User::where('name', $name)->where('contact_number', $phone)->exists()) {
                $i--; // retry
                continue;
            }

            User::create([
                'patient_id' => $patientId,
                'patient_number' => $patientNumber,
                'name' => $name,
                'email' => $email,
                'role' => UserRole::Patient,
                'contact_number' => $phone,
                'address' => $address,
                'password' => Hash::make('password'),
            ]);
        }

        $this->command->info('60 patient accounts created successfully.');
    }
}
