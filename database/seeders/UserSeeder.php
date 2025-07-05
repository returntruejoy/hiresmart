<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if an admin user already exists to prevent duplication.
        if (User::where('role', User::ROLE_ADMIN)->exists()) {
            $this->command->info('An admin user already exists. Skipping creation.');

            return;
        }

        // Create the single admin user for the application.
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@hiresmart.com', // You can change this
            'password' => Hash::make('password'), // Change this in production!
            'role' => User::ROLE_ADMIN,
            'email_verified_at' => now(), // Pre-verify the admin's email
        ]);

        $this->command->info('Default admin user created successfully.');
        $this->command->comment('Email: admin@hiresmart.com');
        $this->command->comment('Password: password');
    }
}
