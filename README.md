# FLUX Notes API

Professional Laravel 12 REST API for a notes application with JWT authentication.

## Features

- 🔐 **JWT Authentication** - Secure token-based authentication
- 👤 **User Management** - Registration, login, profile updates
- 📝 **Notes CRUD** - Complete note management with categories
- 📌 **Pin Notes** - Mark important notes as pinned
- 🏷️ **Categories** - Organize notes with colored categories
- 🔍 **Search & Filter** - Filter notes by category, pinned status, or search
- 🔒 **Password Reset** - Secure password reset functionality
- 📋 **Privacy Policy** - Built-in privacy policy management
- ✅ **Form Validation** - Comprehensive request validation
- 🏗️ **Service Layer** - Clean architecture with business logic separation

## Requirements

- PHP 8.2 or higher
- MySQL 5.7 or higher
- Composer
- Laravel 12

## Installation

1. **Clone the repository** (if applicable) or navigate to the project directory:
   ```bash
   cd /Users/tunahanyilmaz/Desktop/my_company/project/flux
   ```

2. **Install dependencies**:
   ```bash
   composer install
   ```

3. **Configure environment**:
   - The `.env` file is already configured
   - Database: `flux` (already created)
   - DB User: `root` (no password)

4. **Run migrations and seeders**:
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Start the development server**:
   ```bash
   php artisan serve
   ```

   The API will be available at: `http://127.0.0.1:8000`

## API Endpoints

### Authentication (Public)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/register` | Register a new user |
| POST | `/api/auth/login` | Login and get JWT token |
| POST | `/api/auth/sendPasswordResetLink` | Send password reset email |

### User Profile (Protected)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/auth/getProfile` | Get authenticated user profile |
| POST | `/api/auth/logout` | Logout (invalidate token) |
| PUT | `/api/profile/updateProfile` | Update user profile |
| PUT | `/api/profile/updatePassword` | Change password |

### Notes (Protected)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/notes` | List all user notes |
| POST | `/api/notes` | Create a new note |
| GET | `/api/notes/{id}` | Get a specific note |
| PUT | `/api/notes/{id}` | Update a note |
| DELETE | `/api/notes/{id}` | Delete a note |
| PUT | `/api/notes/{id}/pin` | Toggle pin status |

### Privacy Policy (Public)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/getPrivacyPolicy` | Get active privacy policy |

## Usage Examples

### Register a New User

```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "first_name": "John",
      "last_name": "Doe",
      "email": "john@example.com",
      "created_at": "2026-02-03T12:00:00.000000Z",
      "updated_at": "2026-02-03T12:00:00.000000Z"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJ..."
  }
}
```

### Login

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": { ... },
    "token": "eyJ0eXAiOiJKV1QiLCJ..."
  }
}
```

### Create a Note (Protected)

**Note:** Use the token from login/register in the Authorization header.

```bash
curl -X POST http://localhost:8000/api/notes \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "title": "My First Note",
    "content": "This is the content of my note",
    "category_id": 1,
    "is_pinned": false
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Note created successfully",
  "data": {
    "note": {
      "id": 1,
      "user_id": 1,
      "title": "My First Note",
      "content": "This is the content of my note",
      "category_id": 1,
      "is_pinned": false,
      "created_at": "2026-02-03T12:00:00.000000Z",
      "updated_at": "2026-02-03T12:00:00.000000Z",
      "category": {
        "id": 1,
        "name": "Personal",
        "color": "#FF6B6B"
      }
    }
  }
}
```

### List Notes with Filters

```bash
# Get all notes
curl -X GET "http://localhost:8000/api/notes" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"

# Get pinned notes only
curl -X GET "http://localhost:8000/api/notes?is_pinned=1" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"

# Filter by category
curl -X GET "http://localhost:8000/api/notes?category_id=1" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"

# Search notes
curl -X GET "http://localhost:8000/api/notes?search=keyword" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Update Profile

```bash
curl -X PUT http://localhost:8000/api/profile/updateProfile \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "first_name": "Jane",
    "last_name": "Smith"
  }'
```

### Change Password

```bash
curl -X PUT http://localhost:8000/api/profile/updatePassword \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "current_password": "password123",
    "new_password": "newpassword123",
    "new_password_confirmation": "newpassword123"
  }'
```

## Database Schema

### Users
- `id`, `first_name`, `last_name`, `email`, `password`
- `email_verified_at`, `remember_token`, `created_at`, `updated_at`

### Categories
- `id`, `user_id`, `name`, `color`
- `created_at`, `updated_at`

### Notes
- `id`, `user_id`, `title`, `content`, `category_id`
- `is_pinned`, `created_at`, `updated_at`

### Privacy Policies
- `id`, `version`, `content`, `is_active`, `effective_date`
- `created_at`, `updated_at`

### Password Reset Tokens
- `email`, `token`, `created_at`

## Seeded Data

The database is seeded with:

**Demo User:**
- Email: `demo@flux.com`
- Password: `password`

**Default Categories:**
1. Personal (#FF6B6B)
2. Work (#4ECDC4)
3. Ideas (#FFE66D)
4. Todo (#95E1D3)

**Privacy Policy:**
- Version 1.0 (Active)

## Architecture

### Service Layer Pattern

Business logic is separated into service classes:

- `AuthService` - Authentication and user management
- `ProfileService` - Profile updates
- `NoteService` - Note CRUD operations
- `PrivacyPolicyService` - Privacy policy management

### Request Validation

All endpoints use Form Request classes for validation:

- `RegisterRequest`
- `LoginRequest`
- `UpdateProfileRequest`
- `UpdatePasswordRequest`
- `StoreNoteRequest`
- `UpdateNoteRequest`

### Middleware

- **JWT Middleware** (`jwt.auth`) - Protects routes requiring authentication
- Handles token expiration, invalid tokens, and missing tokens

## Security

- ✅ Passwords hashed with bcrypt
- ✅ JWT token authentication
- ✅ CORS configured
- ✅ Request validation on all inputs
- ✅ SQL injection protection via Eloquent ORM
- ✅ User data isolation (users can only access their own data)

## Response Format

All API responses follow a consistent format:

**Success Response:**
```json
{
  "success": true,
  "message": "Operation successful",
  "data": {
    ...
  }
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Error description",
  "error": "Detailed error message"
}
```

## HTTP Status Codes

- `200` - OK (successful GET, PUT, DELETE)
- `201` - Created (successful POST)
- `400` - Bad Request (validation errors)
- `401` - Unauthorized (invalid/missing token)
- `404` - Not Found
- `500` - Internal Server Error

## Development

### Running Tests

```bash
php artisan test
```

### Clearing Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### Database Reset

```bash
php artisan migrate:fresh --seed
```

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── AuthController.php
│   │       ├── ProfileController.php
│   │       ├── NoteController.php
│   │       └── PrivacyPolicyController.php
│   ├── Middleware/
│   │   └── JwtMiddleware.php
│   └── Requests/
│       ├── Auth/
│       ├── Profile/
│       └── Note/
├── Models/
│   ├── User.php
│   ├── Note.php
│   ├── Category.php
│   └── PrivacyPolicy.php
└── Services/
    ├── AuthService.php
    ├── ProfileService.php
    ├── NoteService.php
    └── PrivacyPolicyService.php

database/
├── migrations/
└── seeders/
    ├── CategorySeeder.php
    ├── PrivacyPolicySeeder.php
    └── DatabaseSeeder.php

routes/
└── api.php
```

## Notes

- JWT tokens expire after 60 minutes
- Password reset tokens expire after 60 minutes
- All timestamps are in UTC
- Email functionality is configured to log (update for production)

## Production Deployment

Before deploying to production:

1. Update `.env` with production values
2. Configure proper CORS settings in `config/cors.php`
3. Set `APP_DEBUG=false` in `.env`
4. Configure mail driver for password reset emails
5. Use HTTPS for all API requests
6. Set strong `JWT_SECRET` and `APP_KEY`

## License

This project is proprietary and confidential.

## Support

For questions or issues, please contact the development team.

---

**Built with Laravel 12** | **JWT Authentication** | **RESTful API**
