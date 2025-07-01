# JWT User Registration Setup Guide

## 🔧 Complete Setup Steps

### 1. Generate JWT Secret Key
Run this command to generate your JWT secret:
```bash
php artisan jwt:secret
```

### 2. Environment Configuration
Add to your `.env` file:
```env
JWT_SECRET=your_generated_secret_key
JWT_TTL=60                    # Token expires in 60 minutes
JWT_REFRESH_TTL=20160         # Refresh token expires in 2 weeks
JWT_ALGO=HS256               # Algorithm for signing tokens
```

### 3. Run Migration
```bash
php artisan migrate
```

## 📝 API Endpoints

### User Registration Endpoints

#### 1. General Registration
```
POST /api/v1/auth/register
```

#### 2. Employer Registration
```
POST /api/v1/auth/register/employer
```

#### 3. Candidate Registration
```
POST /api/v1/auth/register/candidate
```

## 📊 Request Examples

### General Registration Request
```json
{
    "name": "John Doe",
    "email": "john@example.com", 
    "password": "SecurePass123!",
    "password_confirmation": "SecurePass123!",
    "role": "candidate"
}
```

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

## 📋 User Roles
- `admin`: System administrators
- `employer`: Companies/recruiters
- `candidate`: Job seekers (default)

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
2. **URL**: `http://your-app.com/api/v1/auth/register`
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
   ```

## 🔧 Troubleshooting

### Common Issues

1. **JWT_SECRET not set**
   ```
   Solution: Run `php artisan jwt:secret`
   ```

2. **Validation errors**
   ```
   Check password requirements and email format
   ```

3. **Token not working**
   ```
   Ensure 'api' guard is set to 'jwt' in config/auth.php
   ```

## 📁 Updated Files

We've updated your existing `RegistrationController` with complete JWT functionality:
- `app/Http/Controllers/Api/V1/Auth/RegistrationController.php` - Updated with JWT registration methods
- `routes/api.php` - Routes now use your existing RegistrationController

Your JWT-based user registration system is now ready! 🎉 