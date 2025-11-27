<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\ServiceType;
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
            'status' => 'active',
            'password' => bcrypt('password'),
        ]);

        // Staff Demo Account
        // User::create([
        //     'name' => 'Staff Demo User',
        //     'email' => 'staff@email.com',
        //     'role' => UserRole::Staff,
        //     'contact_number' => '09234567890',
        //     'address' => 'Sogod, Southern Leyte',
        //     'status' => 'active',
        //     'password' => bcrypt('password'),
        // ]);

        // Patient Demo Account (🆕 with patient ID and number)
        // User::create([
        //     'patient_id' => 'P-0001',
        //     'patient_number' => '100001',
        //     'name' => 'Patient Demo User',
        //     'email' => 'patient@email.com',
        //     'role' => UserRole::Patient,
        //     'contact_number' => '09345678901',
        //     'address' => 'Sogod, Southern Leyte',
        //     'status' => 'active',
        //     'password' => bcrypt('password'),
        // ]);

        // 🆕 Optional: Seed sample Service Types for testing
        // ServiceType::insert([
        //     [
        //         'item_code_id' => 'SRV001',
        //         'standard_barcode_id' => 'BAR001',
        //         'short_description' => 'General Checkup',
        //         'standard_description' => 'Basic consultation and physical examination.',
        //         'generic_name' => 'Consultation',
        //         'specifications' => 'Standard service for initial diagnosis',
        //         'item_category' => 'Outpatient',
        //         'examination_type' => 'Medical',
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        //     [
        //         'item_code_id' => 'SRV002',
        //         'standard_barcode_id' => 'BAR002',
        //         'short_description' => 'Blood Test',
        //         'standard_description' => 'Comprehensive blood examination service.',
        //         'generic_name' => 'Laboratory',
        //         'specifications' => 'Includes CBC, glucose, cholesterol',
        //         'item_category' => 'Lab',
        //         'examination_type' => 'Diagnostic',
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        // ]);
    }
}
