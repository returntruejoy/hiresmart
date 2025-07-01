<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:create-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user with a specified role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // --- Check if an admin already exists ---
        if (User::where('role', User::ROLE_ADMIN)->exists()) {
            $this->error('An admin user already exists. To enforce the single-admin rule, a new one cannot be created.');
            $this->comment('If you need to replace the admin, please remove the existing one from the database first.');
            return 1;
        }

        $this->info('Creating the single admin user for the application...');

        $name = $this->ask('Full Name');
        $email = $this->ask('Email Address');
        $password = $this->secret('Password');
        $password_confirmation = $this->secret('Confirm Password');

        $userData = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password_confirmation,
            'role' => User::ROLE_ADMIN, // Hardcode the role to admin
        ];

        // --- Validation ---
        $validator = Validator::make($userData, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'role' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            $this->error('Admin user creation failed!');
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1; // Return a non-zero exit code for failure
        }

        // --- User Creation ---
        try {
            User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'role' => $userData['role'],
            ]);

            $this->info('Admin user created successfully!');
            $this->comment('Name: ' . $userData['name']);
            $this->comment('Email: ' . $userData['email']);

        } catch (\Exception $e) {
            $this->error('An error occurred while creating the admin user:');
            $this->error($e->getMessage());
            return 1;
        }

        return 0; // Return zero for success
    }
}
