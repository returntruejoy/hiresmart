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
            // Programming Languages
            'PHP', 'JavaScript', 'Python', 'Java', 'C#', 'Ruby', 'Go', 'TypeScript', 'Swift', 'Kotlin',
            
            // Web Frameworks
            'Laravel', 'React', 'Vue.js', 'Angular', 'Node.js', 'Express.js', 'Django', 'Flask', 'Spring Boot',
            
            // Databases
            'MySQL', 'PostgreSQL', 'MongoDB', 'Redis', 'SQLite', 'Oracle', 'SQL Server',
            
            // Cloud & DevOps
            'AWS', 'Azure', 'Google Cloud', 'Docker', 'Kubernetes', 'Jenkins', 'GitLab CI', 'Terraform',
            
            // Frontend Technologies
            'HTML5', 'CSS3', 'SASS', 'Bootstrap', 'Tailwind CSS', 'jQuery', 'Webpack', 'Vite',
            
            // Mobile Development
            'React Native', 'Flutter', 'iOS Development', 'Android Development',
            
            // Tools & Technologies
            'Git', 'Linux', 'Nginx', 'Apache', 'Elasticsearch', 'GraphQL', 'REST APIs', 'Microservices',
            
            // Testing
            'PHPUnit', 'Jest', 'Cypress', 'Selenium', 'Unit Testing', 'Integration Testing',
            
            // Design & UI/UX
            'Figma', 'Adobe XD', 'Photoshop', 'Illustrator', 'UI Design', 'UX Research',
            
            // Data & Analytics
            'SQL', 'Data Analysis', 'Machine Learning', 'TensorFlow', 'Pandas', 'NumPy'
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