<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => User::ROLE_CANDIDATE,
            'location_preference' => fake()->city(),
            'salary_expectation_min' => fake()->numberBetween(30000, 80000),
            'salary_expectation_max' => fake()->numberBetween(90000, 150000),
            'bio' => fake()->paragraph(3),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create a user with admin role.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_ADMIN,
            'location_preference' => null,
            'salary_expectation_min' => null,
            'salary_expectation_max' => null,
            'bio' => null,
        ]);
    }

    /**
     * Create a user with employer role.
     */
    public function employer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_EMPLOYER,
            'location_preference' => null,
            'salary_expectation_min' => null,
            'salary_expectation_max' => null,
            'bio' => fake()->paragraph(2),
        ]);
    }

    /**
     * Create a user with candidate role.
     */
    public function candidate(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_CANDIDATE,
            'location_preference' => fake()->city(),
            'salary_expectation_min' => fake()->numberBetween(40000, 80000),
            'salary_expectation_max' => fake()->numberBetween(90000, 180000),
            'bio' => fake()->paragraph(3),
        ]);
    }

    /**
     * Create a user with a specific role.
     */
    public function withRole(string $role): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => $role,
        ]);
    }

    /**
     * Create a candidate with specific location preference.
     */
    public function withLocation(string $location): static
    {
        return $this->state(fn (array $attributes) => [
            'location_preference' => $location,
        ]);
    }

    /**
     * Create a candidate with specific salary expectations.
     */
    public function withSalaryExpectation(int $min, int $max): static
    {
        return $this->state(fn (array $attributes) => [
            'salary_expectation_min' => $min,
            'salary_expectation_max' => $max,
        ]);
    }

    /**
     * Create a senior candidate with higher salary expectations.
     */
    public function senior(): static
    {
        return $this->state(fn (array $attributes) => [
            'salary_expectation_min' => fake()->numberBetween(80000, 120000),
            'salary_expectation_max' => fake()->numberBetween(130000, 200000),
            'bio' => 'Senior developer with '.fake()->numberBetween(5, 15).' years of experience. '.fake()->paragraph(2),
        ]);
    }

    /**
     * Create a junior candidate with lower salary expectations.
     */
    public function junior(): static
    {
        return $this->state(fn (array $attributes) => [
            'salary_expectation_min' => fake()->numberBetween(25000, 50000),
            'salary_expectation_max' => fake()->numberBetween(55000, 80000),
            'bio' => 'Junior developer eager to learn and grow. '.fake()->paragraph(2),
        ]);
    }
}
