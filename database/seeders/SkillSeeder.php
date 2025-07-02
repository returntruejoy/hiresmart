<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            // Programming Languages
            'PHP',
            'JavaScript',
            'Python',
            'Java',
            'C#',
            'Ruby',
            'Go',
            'TypeScript',
            'Swift',
            'Kotlin',
            
            // Web Frameworks
            'Laravel',
            'React',
            'Vue.js',
            'Angular',
            'Node.js',
            'Express.js',
            'Django',
            'Flask',
            'Spring Boot',
            'Symfony',
            
            // Databases
            'MySQL',
            'PostgreSQL',
            'MongoDB',
            'Redis',
            'SQLite',
            'Oracle',
            'SQL Server',
            'Elasticsearch',
            
            // Cloud & DevOps
            'AWS',
            'Azure',
            'Google Cloud',
            'Docker',
            'Kubernetes',
            'Jenkins',
            'GitLab CI',
            'GitHub Actions',
            'Terraform',
            'Ansible',
            
            // Frontend Technologies
            'HTML5',
            'CSS3',
            'SASS/SCSS',
            'Bootstrap',
            'Tailwind CSS',
            'jQuery',
            'Webpack',
            'Vite',
            'Next.js',
            'Nuxt.js',
            
            // Mobile Development
            'React Native',
            'Flutter',
            'iOS Development',
            'Android Development',
            'Xamarin',
            
            // Tools & Technologies
            'Git',
            'Linux',
            'Nginx',
            'Apache',
            'GraphQL',
            'REST APIs',
            'Microservices',
            'WebSockets',
            'OAuth',
            'JWT',
            
            // Testing
            'PHPUnit',
            'Jest',
            'Cypress',
            'Selenium',
            'Unit Testing',
            'Integration Testing',
            'TDD',
            'BDD',
            
            // Design & UI/UX
            'Figma',
            'Adobe XD',
            'Photoshop',
            'Illustrator',
            'UI Design',
            'UX Research',
            'Wireframing',
            'Prototyping',
            
            // Data & Analytics
            'SQL',
            'Data Analysis',
            'Machine Learning',
            'TensorFlow',
            'Pandas',
            'NumPy',
            'Power BI',
            'Tableau',
            
            // Project Management
            'Agile',
            'Scrum',
            'Kanban',
            'Jira',
            'Trello',
            'Asana',
            
            // Other Technologies
            'Blockchain',
            'IoT',
            'AR/VR',
            'AI',
            'Cybersecurity',
            'DevSecOps',
        ];

        foreach ($skills as $skillName) {
            Skill::firstOrCreate(['name' => $skillName]);
        }

        $this->command->info('Skills seeded successfully!');
    }
} 