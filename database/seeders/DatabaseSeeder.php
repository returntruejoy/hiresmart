<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Starting HireSmart Database Seeding...');

        // Always seed the default admin user first
        $this->call([
            UserSeeder::class,
        ]);

        // Check environment or ask for seeding preference
        $environment = app()->environment();

        if ($environment === 'local' || $environment === 'development') {
            $this->command->info('Development environment detected.');

            $choice = $this->command->choice(
                'Which seeding option would you like?',
                [
                    'development' => 'Development (Small dataset for testing)',
                    'comprehensive' => 'Comprehensive (Large realistic dataset)',
                    'maintenance-test' => 'Maintenance Test (Old data for cleanup testing)',
                    'edge-case-test' => 'Edge Case Test (Boundary conditions & special cases)',
                    'matching-test' => 'Matching Test (Original algorithm test data)',
                    'skills-only' => 'Skills Only (Just populate skills)',
                    'custom' => 'Custom (Choose individual seeders)',
                ],
                'development'
            );

            switch ($choice) {
                case 'development':
                    $this->call([DevelopmentSeeder::class]);
                    break;

                case 'comprehensive':
                    $this->call([ComprehensiveSeeder::class]);
                    break;

                case 'maintenance-test':
                    $this->call([MaintenanceTestSeeder::class]);
                    break;

                case 'edge-case-test':
                    $this->call([EdgeCaseTestSeeder::class]);
                    break;

                case 'matching-test':
                    $this->call([
                        SkillSeeder::class,
                        MatchingTestDataSeeder::class,
                    ]);
                    break;

                case 'skills-only':
                    $this->call([SkillSeeder::class]);
                    break;

                case 'custom':
                    $this->runCustomSeeding();
                    break;
            }
        } else {
            // Production environment - only seed essential data
            $this->command->info('Production environment detected. Seeding essential data only.');
            $this->call([
                SkillSeeder::class,
            ]);
        }

        $this->command->info('Database seeding completed successfully!');
    }

    /**
     * Run custom seeding based on user choices.
     */
    private function runCustomSeeding(): void
    {
        $availableSeeders = [
            'SkillSeeder' => 'Skills (Technical skills database)',
            'DevelopmentSeeder' => 'Development Data (Small test dataset)',
            'ComprehensiveSeeder' => 'Comprehensive Data (Large realistic dataset)',
            'MaintenanceTestSeeder' => 'Maintenance Test Data (Old unverified users & job posts)',
            'EdgeCaseTestSeeder' => 'Edge Case Test Data (Boundary conditions & special cases)',
            'MatchingTestDataSeeder' => 'Matching Test Data (Original algorithm test data)',
        ];

        $selectedSeeders = $this->command->choice(
            'Select seeders to run (separate multiple choices with commas):',
            array_keys($availableSeeders),
            null,
            null,
            true
        );

        foreach ($selectedSeeders as $seeder) {
            $this->command->info("Running {$seeder}...");
            $this->call([$seeder]);
        }
    }
}
