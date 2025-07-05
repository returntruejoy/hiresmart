<?php

namespace Database\Factories;

use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Skill>
 */
class SkillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $skills = [
            'PHP', 'JavaScript', 'Python', 'Java', 'C#', 'Ruby', 'Go', 'TypeScript', 'Swift', 'Kotlin',
            'Laravel', 'React', 'Vue.js', 'Angular', 'Node.js', 'Express.js', 'Django', 'Flask', 'Spring Boot',
            'MySQL', 'PostgreSQL', 'MongoDB', 'Redis', 'SQLite', 'Oracle', 'SQL Server',
            'AWS', 'Azure', 'Google Cloud', 'Docker', 'Kubernetes', 'Jenkins', 'GitLab CI', 'Terraform',
            'HTML5', 'CSS3', 'SASS', 'Bootstrap', 'Tailwind CSS', 'jQuery', 'Webpack', 'Vite',
            'React Native', 'Flutter', 'iOS Development', 'Android Development',
            'Git', 'Linux', 'Nginx', 'Apache', 'Elasticsearch', 'GraphQL', 'REST APIs', 'Microservices',
            'PHPUnit', 'Jest', 'Cypress', 'Selenium', 'Unit Testing', 'Integration Testing',
            'Figma', 'Adobe XD', 'Photoshop', 'Illustrator', 'UI Design', 'UX Research',
            'SQL', 'Data Analysis', 'Machine Learning', 'TensorFlow', 'Pandas', 'NumPy',
        ];

        return [
            'name' => fake()->unique()->randomElement($skills),
        ];
    }

    /**
     * Create a programming language skill.
     */
    public function programmingLanguage(): static
    {
        $languages = ['PHP', 'JavaScript', 'Python', 'Java', 'C#', 'Ruby', 'Go', 'TypeScript'];

        return $this->state(fn (array $attributes) => [
            'name' => fake()->randomElement($languages),
        ]);
    }

    /**
     * Create a web framework skill.
     */
    public function webFramework(): static
    {
        $frameworks = ['Laravel', 'React', 'Vue.js', 'Angular', 'Node.js', 'Django', 'Flask'];

        return $this->state(fn (array $attributes) => [
            'name' => fake()->randomElement($frameworks),
        ]);
    }

    /**
     * Create a database skill.
     */
    public function database(): static
    {
        $databases = ['MySQL', 'PostgreSQL', 'MongoDB', 'Redis', 'SQLite'];

        return $this->state(fn (array $attributes) => [
            'name' => fake()->randomElement($databases),
        ]);
    }

    /**
     * Create a cloud/DevOps skill.
     */
    public function cloudDevOps(): static
    {
        $cloudSkills = ['AWS', 'Azure', 'Google Cloud', 'Docker', 'Kubernetes', 'Jenkins'];

        return $this->state(fn (array $attributes) => [
            'name' => fake()->randomElement($cloudSkills),
        ]);
    }
}
