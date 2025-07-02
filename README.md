# HireSmart Backend - Installation Guide

This guide provides step-by-step instructions for setting up the HireSmart backend on a local development machine using Laravel Sail.

---

### **1. Prerequisites**

Before you begin, ensure you have the following software installed on your system:

- **Docker Desktop:** Laravel Sail uses Docker to create a consistent development environment. [Download and install it here](https://www.docker.com/products/docker-desktop/).
- **Git:** For cloning the project repository.

*You do not need to install PHP, Composer, or Node.js locally. All required versions are provided by the Sail development environment.*

---

### **2. Installation Steps**

**Step 1: Clone the Repository**
Open your terminal and clone the project repository.

```bash
git clone https://github.com/returntruejoy/hiresmart
Or with ssh
git clone git@github.com:returntruejoy/hiresmart.git
cd hiresmart
```

**Step 2: Copy the Environment File**
This file stores your application's configuration, including database credentials and API keys.

```bash
cp .env.example .env
```

**Step 3: Start the Sail Containers**
This command will download the necessary Docker images and start all the services (app, PostgreSQL database, Redis) in the background.

```bash
./vendor/bin/sail up -d
```
*Note: The first time you run this, it may take several minutes to download the required Docker images.*

**Step 4: Install Composer Dependencies**
Once the containers are running, execute this command to install the project's PHP dependencies inside the application container.

```bash
./vendor/bin/sail composer install
```

**Step 5: Generate Application & JWT Keys**
Every Laravel application needs a unique key for encryption, and our app needs a key for JWT authentication.

```bash
# Generate Laravel App Key
./vendor/bin/sail artisan key:generate

# Generate JWT Secret Key
./vendor/bin/sail artisan jwt:secret
```

**Step 6: Run Migrations and Seed the Database**
This single command creates all the necessary tables in your PostgreSQL database and populates them with essential data, including a default admin user and test data for jobs and candidates.

```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

**🎉 Congratulations, the installation is complete!**

---

### **3. Running and Testing the Application**

**Your application is already running.** The API is accessible at `http://localhost`
Do not forget to use /api/v1/ before every endpoints.

**Step 1: Run the Queue Worker (Crucial)**
Our most important features (like job matching and notifications) run as background jobs. You **must** have a queue worker running to process them.

**Open a new, separate terminal window**, navigate to the project directory, and run:

```bash
./vendor/bin/sail artisan queue:work
```
*Leave this terminal open. It acts as your background job processor.*

**Step 2: Test the Public API**
In your original terminal, you can test if the API is responding correctly by fetching the public job listings or Leverage the Postman collection.

```bash
curl -X GET "http://localhost/api/v1/job-posts" -H "Accept: application/json" | jq

Or you can use  postman*
```
*This should return a JSON array of the sample job posts created by the seeder.*

**Step 3: Default Admin User**
The database seeder creates a default admin user for you to use.

- **Email:** `admin@hiresmart.com`
- **Password:** `password`

You can use these credentials with an API client (like Postman or Insomnia) to log in via the `/api/v1/login` endpoint and test authenticated routes.

# Manually trigger the job matching process
./vendor/bin/sail artisan app:run-job-matching-now

# Manually archive job posts older than 30 days
./vendor/bin/sail artisan jobs:archive-old

# Manually remove unverified users older than 7 days
./vendor/bin/sail artisan app:remove-unverified-users

# Run the scheduler to execute daily/weekly tasks (for testing)
./vendor/bin/sail artisan schedule:run

# HireSmart Backend - Complete Project Overview & Testing Guide

## 🎯 Executive Summary

**HireSmart** is a sophisticated job platform backend built with Laravel 12, featuring intelligent job matching, automated notifications, and performance optimization. The system connects employers with qualified candidates through an AI-powered matching algorithm that considers skills, salary expectations, and location preferences.

---

## 🏗️ System Architecture

### Technology Stack
- **Framework**: Laravel 12 (PHP 8.2+)
- **Database**: PostgreSQL
- **Cache**: Redis
- **Authentication**: JWT (JSON Web Tokens)
- **Queue System**: Redis-based background job processing
- **Email**: Laravel Mail with queued notifications
- **Development Environment**: Docker (Laravel Sail)

### Architectural Pattern
The system follows a **Service/Repository Pattern** for clean, maintainable code:
- **Repositories**: Handle all database interactions (UserRepository, JobPostRepository, ApplicationRepository)
- **Services**: Contain business logic (AuthService, JobMatchingService, JobPostService, etc.)
- **Controllers**: Thin layer handling HTTP requests/responses
- **Policies**: Authorization logic for resource access control

---

## 👥 User Roles & Authentication

### User Types
1. **Admin** (`admin`): System administration and oversight
2. **Employer** (`employer`): Post jobs, manage applications, view statistics
3. **Candidate** (`candidate`): Apply to jobs, receive match notifications

### Authentication System
- **JWT-based authentication** for secure, stateless API access
- **Role-based authorization** using middleware and policies
- **Secure registration** prevents public admin user creation
- **Default admin account**: `admin@hiresmart.com` / `password`

### Security Features
- Password hashing with bcrypt
- JWT token expiration and refresh
- Role-based route protection
- Policy-based resource authorization
- Input validation and sanitization

---

## 🚀 Core Features

### 1. User Management
- **Registration**: Public endpoint for employers and candidates
- **Login/Logout**: JWT token-based authentication
- **Profile Management**: User skills, location preferences, salary expectations
- **Role-based Access Control**: Different permissions per user type

### 2. Job Management
- **CRUD Operations**: Full job post management for employers
- **Public Job Listings**: Browse active jobs without authentication
- **Application System**: Candidates can apply to jobs (one application per job)
- **Job Archiving**: Automatic deactivation of old job posts (30+ days)

### 3. Intelligent Job Matching System ⭐
- **Background Processing**: Asynchronous matching using Redis queues
- **Weighted Algorithm**: 
  - Skills matching (50% weight)
  - Salary compatibility (30% weight)
  - Location preference (20% weight)
- **Match Scoring**: 0-100% compatibility score
- **High Match Notifications**: Automatic emails for 70%+ matches
- **Match History**: Persistent storage of all match calculations

### 4. Performance Optimization
- **Redis Caching**: 
  - Job listings cached for 5 minutes
  - Employer statistics cached for 5 minutes
  - Intelligent cache invalidation on data changes
- **Query Optimization**: Eager loading to prevent N+1 problems
- **Background Jobs**: Resource-intensive tasks run asynchronously

### 5. Automated Maintenance
- **Daily Job Archiving**: Deactivates jobs older than 30 days (2:00 AM)
- **Weekly User Cleanup**: Removes unverified users after 7 days (Sundays 3:00 AM)
- **Configurable Scheduling**: Easy to modify timing and frequency

---

## 📊 Database Schema

### Core Tables
- **users**: User accounts with roles, preferences, and salary expectations
- **job_posts**: Job listings with company info, requirements, and salary ranges
- **applications**: Candidate applications to jobs
- **skills**: Skill definitions (PHP, Laravel, React, etc.)
- **job_matches**: Calculated matches with scores and details
- **job_post_skill**: Many-to-many job-skill relationships
- **user_skill**: Many-to-many user-skill relationships

### Key Relationships
- User → JobPost (employer relationship)
- User → Application (candidate applications)
- JobPost → Application (job applications)
- JobMatch → User/JobPost (matching results)
- Skills ↔ Users/JobPosts (many-to-many)

---

## 🔌 API Endpoints

### Public Endpoints
```
GET  /api/v1/job-posts           # List all active jobs
GET  /api/v1/job-posts/{id}      # View specific job
POST /api/v1/register            # User registration
POST /api/v1/login               # User login
```

### Employer Endpoints (Authenticated)
```
GET    /api/v1/employer/job-posts          # Employer's jobs
POST   /api/v1/job-posts                   # Create job
PUT    /api/v1/job-posts/{id}              # Update job
DELETE /api/v1/job-posts/{id}              # Delete job
GET    /api/v1/job-posts/{id}/applications # View applications
GET    /api/v1/employer/stats              # Dashboard statistics
```

### Candidate Endpoints (Authenticated)
```
POST /api/v1/job-posts/{id}/apply  # Apply to job
```

### Admin Endpoints (Authenticated)
```
GET /api/v1/admin/dashboard  # Admin dashboard
```

### Cache Management
```
GET  /api/v1/job-posts/cache/stats  # Cache statistics
POST /api/v1/job-posts/cache/clear  # Clear cache
```

---

## 🧪 Complete Testing Guide

 **Start Queue Worker** (CRITICAL):
   ```bash
   ./vendor/bin/sail artisan queue:work
   ```
   *Keep this running in a separate terminal - required for job matching and notifications*

### Test Data Overview
The seeder creates realistic test data:
- **Admin**: `admin@hiresmart.com` / `password`
- **Employer**: `employer@example.com` (Tech Company Inc.)
- **Candidates**: 4 test candidates with different skill sets and preferences
- **Jobs**: 2 job posts (Senior Laravel Developer, Frontend Developer)
- **Skills**: 8 technical skills (PHP, Laravel, Vue.js, React, etc.)

### Testing Scenarios

#### 1. Authentication Testing
```bash
# Test registration
curl -X POST http://localhost/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password",
    "password_confirmation": "password",
    "role": "candidate"
  }'

# Test login
curl -X POST http://localhost/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@hiresmart.com",
    "password": "password"
  }'
```

#### 2. Job Management Testing
```bash
# View public job listings
curl -X GET http://localhost/api/v1/job-posts

# Create job (requires employer token)
curl -X POST http://localhost/api/v1/job-posts \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Backend Developer",
    "description": "Looking for PHP developer",
    "company_name": "Test Company",
    "location": "San Francisco, CA",
    "salary_min": 90000,
    "salary_max": 130000,
    "skill_ids": [1, 2]
  }'
```

#### 3. Job Matching Testing
```bash
# Trigger job matching manually
./vendor/bin/sail artisan app:run-job-matching-now

# Check logs for matching results
./vendor/bin/sail logs | grep "Job Matching"

# Check email notifications in logs
./vendor/bin/sail logs | grep "notification"
```

#### 4. Application Testing
```bash
# Apply to job (requires candidate token)
curl -X POST http://localhost/api/v1/job-posts/1/apply \
  -H "Authorization: Bearer CANDIDATE_TOKEN" \
  -H "Content-Type: application/json"

# View applications (requires employer token)
curl -X GET http://localhost/api/v1/job-posts/1/applications \
  -H "Authorization: Bearer EMPLOYER_TOKEN"
```

#### 5. Performance Testing
```bash
# Check cache statistics
curl -X GET http://localhost/api/v1/job-posts/cache/stats

# Clear cache
curl -X POST http://localhost/api/v1/job-posts/cache/clear

# Test cache performance (jobs should load faster on second request)
time curl -X GET http://localhost/api/v1/job-posts
time curl -X GET http://localhost/api/v1/job-posts
```

### Expected Test Results

#### Job Matching Expectations
Based on test data, you should see these match scores:
- **Alice PerfectMatch** → Senior Laravel Developer: ~85% (perfect skills + location + good salary)
- **Bob GoodMatch** → Senior Laravel Developer: ~60% (good skills + location, but salary too high)
- **Charlie Remote** → Frontend Developer: ~90% (perfect skills + location + salary)
- **David WrongLocation** → Senior Laravel Developer: ~65% (good skills + salary, wrong location)

#### Notification Testing
- High matches (70%+) should trigger email notifications
- Check logs for: `High match notification queued for Job #X and Candidate #Y`
- Email content appears in `storage/logs/laravel.log`

#### Cache Testing
- First job listing request: ~100-200ms
- Cached job listing request: ~10-50ms
- Cache automatically clears when jobs are created/updated/deleted

---

## 🛠️ Daily Development Commands

### Essential Commands
```bash
# Start development environment
./vendor/bin/sail up -d

# Run queue worker (always needed)
./vendor/bin/sail artisan queue:work

# Reset database with fresh test data
./vendor/bin/sail artisan migrate:fresh --seed

# Trigger job matching
./vendor/bin/sail artisan app:run-job-matching-now

# View logs
./vendor/bin/sail logs -f
```

### Maintenance Commands
```bash
# Archive old jobs
./vendor/bin/sail artisan jobs:archive-old

# Remove unverified users
./vendor/bin/sail artisan app:remove-unverified-users

# Run scheduler (for testing scheduled tasks)
./vendor/bin/sail artisan schedule:run

# Clear all caches
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
```

---

## 📈 Business Value & ROI

### For Employers
- **Automated Candidate Discovery**: No manual searching through resumes
- **Quality Scoring**: Data-driven candidate ranking (0-100% match)
- **Time Savings**: Background processing doesn't slow down the platform
- **Real-time Statistics**: Dashboard with application metrics

### For Candidates
- **Proactive Notifications**: Get alerted about relevant opportunities
- **Personalized Matching**: Algorithm considers skills, salary, and location
- **One-click Applications**: Streamlined application process

### For Platform Owners
- **Scalable Architecture**: Handles growth through caching and background jobs
- **Low Maintenance**: Automated cleanup and archiving
- **Performance Optimized**: Redis caching reduces database load
- **Extensible Design**: Easy to add new features and matching criteria

---

## 🔧 Configuration & Customization

### Job Matching Algorithm
Modify weights in `config/matching.php`:
```php
'weights' => [
    'skills' => 0.5,    // 50% weight on skills
    'salary' => 0.3,    // 30% weight on salary
    'location' => 0.2,  // 20% weight on location
],
```

### Notification Threshold
Change in `JobMatchingService.php`:
```php
const MATCH_THRESHOLD = 70; // Send notifications for 70%+ matches
```

### Cache Duration
Modify in respective services:
```php
Cache::remember('key', 300, $callback); // 5 minutes = 300 seconds
```

### Scheduled Task Timing
Update in `AppServiceProvider.php`:
```php
$schedule->command('jobs:archive-old')->dailyAt('02:00');
$schedule->command('app:remove-unverified-users')->weekly()->sundays()->at('03:00');
```

---

## 🚀 Production Deployment Checklist

### Environment Configuration
- [ ] Set `APP_ENV=production`
- [ ] Configure real database credentials
- [ ] Set up Redis server
- [ ] Configure SMTP for email notifications
- [ ] Generate secure JWT secret
- [ ] Set up SSL/HTTPS

### Performance Optimization
- [ ] Enable OPcache
- [ ] Configure Redis persistence
- [ ] Set up database connection pooling
- [ ] Implement CDN for static assets
- [ ] Configure log rotation

### Security Hardening
- [ ] Disable debug mode
- [ ] Configure CORS properly
- [ ] Set up rate limiting
- [ ] Implement API versioning
- [ ] Regular security updates

### Monitoring & Maintenance
- [ ] Set up application monitoring
- [ ] Configure log aggregation
- [ ] Schedule regular backups
- [ ] Monitor queue performance
- [ ] Set up alerts for failures

---

## 💡 Future Enhancement Opportunities

### Advanced Matching
- Machine learning-based scoring
- Candidate experience level weighting
- Company culture fit analysis
- Interview availability matching

### Communication Features
- In-app messaging between employers and candidates
- Interview scheduling system
- Application status tracking
- Feedback collection system

### Analytics & Insights
- Employer dashboard with hiring analytics
- Candidate success rate tracking
- Market salary insights
- Skill demand analysis

### Integration Possibilities
- LinkedIn profile import
- GitHub skill analysis
- Calendar integration for interviews
- Payment processing for premium features

---

## 📞 Support & Maintenance

### Troubleshooting Common Issues
1. **Queue not processing**: Ensure `queue:work` is running
2. **Database connection errors**: Check PostgreSQL container status
3. **Redis connection refused**: Verify Redis container and `REDIS_HOST=redis`
4. **JWT errors**: Regenerate JWT secret with `artisan jwt:secret`
5. **Email not sending**: Check mail configuration in `.env`

### Log Locations
- Application logs: `storage/logs/laravel.log`
- Queue job logs: Included in main Laravel log
- Email notifications: Logged when using 'log' mail driver

### Performance Monitoring
- Monitor Redis memory usage
- Track database query performance
- Watch queue job processing times
- Monitor API response times

---

## 🎉 Conclusion

HireSmart represents a complete, production-ready job platform backend with intelligent matching capabilities. The system is designed for scalability, maintainability, and performance, making it suitable for everything from startup MVPs to enterprise-level deployments.

The automated job matching system, combined with performance optimization and maintenance automation, provides significant value to all stakeholders while requiring minimal ongoing intervention.

**Ready to revolutionize how people find jobs and hire talent!** 🚀 