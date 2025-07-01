<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call the UserSeeder to create the default admin user.
        $this->call([
            UserSeeder::class,
        ]);

        // You can add other seeders here in the future.
        // For example, creating some sample employer and candidate users:
        // User::factory(10)->create(['role' => User::ROLE_EMPLOYER]);
        // User::factory(50)->create(['role' => User::ROLE_CANDIDATE]);
    }
}
