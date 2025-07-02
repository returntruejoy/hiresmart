# HireSmart - Job Platform Backend

A Laravel 12 job platform with intelligent job matching, connecting employers with candidates based on skills (50%), salary (30%), and location (20%).

## Technology Stack
- Laravel 12 (PHP 8.2+)
- PostgreSQL
- Redis (cache & queue)
- JWT authentication
- Docker (Laravel Sail)

## Core Features
- **Intelligent Job Matching**: Weighted scoring algorithm based on skills, salary, and location
- **Role-based Access**: Admin, Employer, and Candidate permissions
- **Application Management**: Complete job application workflow
- **Performance**: Redis caching and background job processing
- **Maintenance**: Scheduled cleanup of old data

## Installation

```bash
# Clone and setup
git clone https://github.com/returntruejoy/hiresmart
cd hiresmart
cp .env.example .env

# Start Docker and install dependencies
./vendor/bin/sail up -d
./vendor/bin/sail composer install

# Setup application
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan jwt:secret
./vendor/bin/sail artisan migrate:fresh --seed

# Start queue worker (separate terminal)
./vendor/bin/sail artisan queue:work
```

## Testing

### Default Admin Account
- Email: `admin@hiresmart.com`
- Password: `password`

### Available Seeders
```bash
./vendor/bin/sail artisan db:seed --class=DevelopmentSeeder    # Small dataset with test accounts
./vendor/bin/sail artisan db:seed --class=ComprehensiveSeeder  # Large dataset for performance testing
./vendor/bin/sail artisan db:seed --class=MaintenanceTestSeeder # For testing cleanup commands
./vendor/bin/sail artisan db:seed --class=EdgeCaseTestSeeder   # Boundary conditions and special cases
./vendor/bin/sail artisan db:seed                              # Interactive seeding
```

### Key Commands
```bash
./vendor/bin/sail artisan app:run-job-matching-now      # Trigger job matching
./vendor/bin/sail artisan jobs:archive-old              # Archive old job posts
./vendor/bin/sail artisan app:remove-unverified-users   # Remove old unverified users
./vendor/bin/sail artisan schedule:run                  # Run scheduled tasks
```

### Quick API Test
```bash
curl -X GET "http://localhost/api/v1/job-posts" -H "Accept: application/json" | jq
```

## API Endpoints

### Authentication
All authenticated endpoints require a JWT token in the Authorization header:
```
Authorization: Bearer {your-jwt-token}
```

### Public Endpoints
```
GET  /api/v1/job-posts                     # List active jobs
GET  /api/v1/job-posts/{job_post}          # View specific job
POST /api/v1/register                      # User registration
POST /api/v1/login                         # User login
GET  /api/v1/job-posts/cache/stats         # Cache statistics
POST /api/v1/job-posts/cache/clear         # Clear cache
```

### Employer Endpoints (Authenticated + Role: employer)
```
GET    /api/v1/employer/job-posts                 # Employer's jobs
POST   /api/v1/job-posts                          # Create job
PUT    /api/v1/job-posts/{job_post}               # Update job
DELETE /api/v1/job-posts/{job_post}               # Delete job
GET    /api/v1/job-posts/{job_post}/applications  # View applications
GET    /api/v1/employer/stats                     # Dashboard statistics
```

### Candidate Endpoints (Authenticated + Role: candidate)
```
POST /api/v1/job-posts/{job_post}/apply  # Apply to job
```

### Admin Endpoints (Authenticated + Role: admin)
```
GET /api/v1/admin/dashboard  # Admin dashboard
```

### API Usage Examples

#### Login and Get Token
```bash
curl -X POST "http://localhost/api/v1/login" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"admin@hiresmart.com","password":"password"}'
```

#### Use Token for Authenticated Request
```bash
curl -X GET "http://localhost/api/v1/admin/dashboard" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN_HERE"
```

### Response Format
```json
{
  "success": true|false,
  "message": "Description",
  "data": {...},
  "errors": {...} // Only on validation errors
}
```

### HTTP Status Codes
- `200` - Success
- `201` - Created
- `401` - Unauthorized (invalid/missing token)
- `403` - Forbidden (insufficient permissions)
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

## Architecture
- **Pattern**: Service/Repository pattern with thin controllers
- **Database**: Users, job posts, applications, skills, job matches
- **Authentication**: JWT tokens with role-based access control
- **Middleware**: Role-based route protection

## Configuration

### Job Matching Algorithm - Weghted
```php
// config/matching.php
'weights' => [
    'skills' => 0.5,    // 50% weight
    'salary' => 0.3,    // 30% weight
    'location' => 0.2,  // 20% weight
],

// JobMatchingService.php
const MATCH_THRESHOLD = 70; // Send notifications for 70%+ matches
```

### Scheduled Tasks
```php
// AppServiceProvider.php
$schedule->command('jobs:archive-old')->dailyAt('02:00');
$schedule->command('app:remove-unverified-users')->weekly()->sundays()->at('03:00');
```

## 📝 API Endpoints

### User Registration Endpoints

#### Employer Registration
```
POST /api/v1/auth/register/employer
```

#### Candidate Registration
```
POST /api/v1/auth/register/candidate
```

## 📊 Request Examples

### Employer Registration Request
```json
{
    "name": "TechCorp HR",
    "email": "hr@techcorp.com",
    "password": "SecurePass123!",
    "password_confirmation": "SecurePass123!"
}
```

### Candidate Registration Request
```json
{
    "name": "Jane Smith",
    "email": "jane@example.com",
    "password": "SecurePass123!",
    "password_confirmation": "SecurePass123!"
}
```

## ✅ Success Response Example
```json
{
    "success": true,
    "message": "User registered successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "role": "candidate",
            "email_verified_at": null,
            "created_at": "2025-01-20 14:30:00",
            "updated_at": "2025-01-20 14:30:00"
        },
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "token_type": "bearer",
        "expires_in": 3600
    },
    "meta": {
        "api_version": "v1",
        "timestamp": "2025-01-20T14:30:00.000000Z"
    }
}
```

## ❌ Error Response Example
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["The email address is already registered."],
        "password": ["The password confirmation does not match."]
    }
}
```

## 🔐 Using the JWT Token

### Include in Headers
```
Authorization: Bearer your_jwt_token_here
```

### cURL Example
```bash
curl -X GET "http://your-app.com/api/v1/user/profile" \
  -H "Authorization: Bearer your_jwt_token_here" \
  -H "Accept: application/json"
```

### JavaScript/Axios Example
```javascript
const token = localStorage.getItem('jwt_token');

axios.get('/api/v1/user/profile', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json'
  }
});
```

## 🛡️ Password Requirements
- Minimum 8 characters
- Must contain uppercase letters
- Must contain lowercase letters  
- Must contain numbers
- Must contain symbols

## 🔄 Token Information
- **Default TTL**: 60 minutes
- **Refresh TTL**: 2 weeks
- **Algorithm**: HS256
- **Custom Claims**: role, email, name

## ⚙️ JWT Custom Claims
The JWT token includes these custom claims:
```json
{
  "role": "candidate",
  "email": "user@example.com",
  "name": "User Name",
  "sub": 1,
  "iat": 1642680000,
  "exp": 1642683600
}
```

## 🚀 Testing with Postman

1. **Set Request Type**: POST
2. **URL**: `http://localhost/api/v1/auth/register`
3. **Headers**:
   - `Content-Type: application/json`
   - `Accept: application/json`
4. **Body (raw JSON)**:
   ```json
   {
       "name": "Test User",
       "email": "test@example.com",
       "password": "SecurePass123!",
       "password_confirmation": "SecurePass123!",
       "role": "candidate"
   }

# HireSmart Factories and Seeders Guide

Complete test data generation system for users, job posts, skills, applications, and job matches with realistic relationships.

## Factories

### UserFactory
```php
User::factory()->create();                    // Basic user
User::factory()->admin()->create();           // Admin user
User::factory()->employer()->create();        // Employer user
User::factory()->candidate()->create();       // Candidate user
User::factory()->candidate()->senior()->create();  // Senior candidate
User::factory()->candidate()->withLocation('NYC')->create();
User::factory()->candidate()->withSalaryExpectation(80000, 120000)->create();
```

### JobPostFactory
```php
JobPost::factory()->create();                 // Basic job post
JobPost::factory()->senior()->create();       // Senior position
JobPost::factory()->junior()->create();       // Junior position
JobPost::factory()->remote()->create();       // Remote job
JobPost::factory()->inLocation('SF')->create();
JobPost::factory()->withSalary(70000, 100000)->create();
JobPost::factory()->forEmployer($employer)->create();
```

### SkillFactory
```php
Skill::factory()->create();                   // Random skill
Skill::factory()->programmingLanguage()->create();
Skill::factory()->webFramework()->create();
Skill::factory()->database()->create();
Skill::factory()->cloudDevOps()->create();
```

### ApplicationFactory
```php
Application::factory()->create();             // Basic application
Application::factory()->submitted()->create(); // Status: submitted
Application::factory()->viewed()->create();    // Status: viewed
Application::factory()->shortlisted()->create(); // Status: shortlisted
Application::factory()->rejected()->create();  // Status: rejected
Application::factory()->forJobPost($job)->create();
Application::factory()->fromCandidate($candidate)->create();
```

### JobMatchFactory
```php
JobMatch::factory()->create();                // Basic match
JobMatch::factory()->perfect()->create();     // 90-95 score
JobMatch::factory()->highMatch()->create();   // 80-95 score
JobMatch::factory()->mediumMatch()->create(); // 60-79 score
JobMatch::factory()->lowMatch()->create();    // 40-59 score
```

## Seeders

### Interactive Seeding (Recommended)
```bash
sail artisan db:seed
```

### Individual Seeders

#### DevelopmentSeeder - Small focused dataset
```bash
sail artisan db:seed --class=DevelopmentSeeder
```
**Creates:**
- 2 employers, 3 candidates with known credentials
- 3 job posts with realistic requirements
- Applications and job matches

**Test Accounts:** (all password: `password`)
- `employer1@hiresmart.com` - John Employer
- `employer2@hiresmart.com` - Sarah Manager  
- `candidate1@hiresmart.com` - Alice Developer (Senior Laravel)
- `candidate2@hiresmart.com` - Bob Frontend (Junior React)
- `candidate3@hiresmart.com` - Carol Fullstack (Full Stack)

#### ComprehensiveSeeder - Large realistic dataset
```bash
sail artisan db:seed --class=ComprehensiveSeeder
```
**Creates:**
- 15 employers, 82 candidates with diverse skills
- 45+ job posts with skill requirements
- Hundreds of applications and job matches
- Complete skill relationships

#### MaintenanceTestSeeder - Test cleanup commands
```bash
sail artisan db:seed --class=MaintenanceTestSeeder
```
**Creates:**
- Unverified users at various ages (8, 10, 15, 30, 45 days old)
- Recent unverified users (1, 3, 6 days old) - should NOT be cleaned
- Job posts older than 30 days for archiving tests

**Perfect for testing:**
- `sail artisan app:remove-unverified-users`
- `sail artisan jobs:archive-old`

#### EdgeCaseTestSeeder - Boundary conditions
```bash
sail artisan db:seed --class=EdgeCaseTestSeeder
```
**Creates:**
- Users/jobs at exact boundary dates (7, 30 days)
- Special characters, emojis, international names
- Extreme salary ranges and null values
- Job matches with scores 0 and 100

#### SkillSeeder - Technical skills database
```bash
sail artisan db:seed --class=SkillSeeder
```
Creates 100+ technical skills across programming languages, frameworks, databases, and DevOps tools.

## Usage Examples

### Development Setup
```bash
sail artisan migrate:fresh --seed  # Choose "development"
```

### Performance Testing
```bash
sail artisan migrate:fresh --seed  # Choose "comprehensive"
```

### Maintenance Testing
```bash
sail artisan db:seed --class=MaintenanceTestSeeder
sail artisan app:remove-unverified-users  # Test cleanup
sail artisan jobs:archive-old             # Test archiving
```

### Custom Data Generation
```php
// Create specialized candidate
$candidate = User::factory()
    ->candidate()
    ->senior()
    ->withLocation('Remote')
    ->withSalaryExpectation(100000, 150000)
    ->create(['name' => 'Expert Developer']);

// Assign skills
$skills = Skill::whereIn('name', ['PHP', 'Laravel', 'React'])->get();
$candidate->skills()->attach($skills);

// Create matching job
$job = JobPost::factory()
    ->senior()
    ->remote()
    ->withSalary(110000, 140000)
    ->create();
$job->skills()->attach($skills);

// Create perfect match
JobMatch::factory()->perfect()->forJobPost($job)->forCandidate($candidate)->create();
```

## Data Relationships

- **Users**: Senior candidates (4-6 skills), Junior candidates (2-4 skills)
- **Job Posts**: Each requires 3-6 skills matching job titles
- **Applications**: Candidates apply to 1-5 jobs with realistic cover letters
- **Matches**: 20% perfect, 30% high, 30% medium, 20% low distribution

## Verification Commands

```bash
sail artisan tinker
>>> User::count()                    # Total users
>>> JobPost::count()                 # Total job posts
>>> Application::count()             # Total applications
>>> JobMatch::count()                # Total matches
>>> DB::table('user_skill')->count() # Skill relationships

# Maintenance test verification
>>> User::whereNull('email_verified_at')->where('created_at', '<', now()->subDays(7))->count()
>>> JobPost::where('created_at', '<', now()->subDays(30))->count()

# Edge case verification
>>> User::where('name', 'like', '%ñ%')->count()  # Special characters
>>> JobMatch::whereIn('match_score', [0, 100])->count()  # Extreme scores
```

## Best Practices

- **Development**: Use `DevelopmentSeeder` for daily work (fast, known accounts)
- **Testing**: Use `ComprehensiveSeeder` for integration tests (large, diverse dataset)
- **Maintenance**: Use `MaintenanceTestSeeder` to verify cleanup commands
- **Edge Cases**: Use `EdgeCaseTestSeeder` for comprehensive boundary testing
- **Production**: Only run `SkillSeeder` (never seed user data in production)

## Troubleshooting

### Common Issues
```bash
# Unique constraint violations
sail artisan migrate:fresh --seed

# Memory issues with large datasets
sail php -d memory_limit=512M artisan db:seed --class=ComprehensiveSeeder

# Ensure skills exist first
sail artisan db:seed --class=SkillSeeder
```

### Environment Detection
- **Development/Local**: Interactive menu with all options
- **Production**: Only essential data (admin + skills) 