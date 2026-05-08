# Treak Backend API Documentation

## Overview

The Treak Backend is a Laravel-based API for managing treks (outdoor adventures). It provides user authentication and CRUD operations for trek management.

## Setup Instructions

1. **Clone the repository**:

    ```bash
    git clone https://github.com/DipeshAcharya1/Treak.git
    cd Treak
    ```

2. **Install dependencies**:

    ```bash
    composer install
    npm install
    ```

3. **Environment setup**:
    - Copy `.env.example` to `.env`
    - Set database credentials in `.env` (MySQL required)
    - Generate app key: `php artisan key:generate`

4. **Database setup**:

    ```bash
    php artisan migrate --seed
    ```

5. **Start the server**:
    ```bash
    php artisan serve
    ```

## Authentication

All API endpoints except registration and health check require Bearer token authentication.

**Header**: `Authorization: Bearer {token}`

Tokens are obtained via login/registration and are valid until logout.

## Roles

The system supports two user roles:

- **user**: Can manage their own treks only
- **admin**: Can manage all treks and access admin-only endpoints

Admins are identified by the `role` field in the user record.

## API Endpoints

### Health Check

- **Method**: GET
- **URL**: `/api/health`
- **Description**: Check if the API is running
- **Authentication**: None
- **Response**:
    ```json
    {
        "status": "ok"
    }
    ```

### User Registration

- **Method**: POST
- **URL**: `/api/register`
- **Description**: Register a new user account
- **Authentication**: None
- **Request Body**:
    ```json
    {
        "name": "John Doe",
        "email": "john@example.com",
        "password": "password123"
    }
    ```
- **Response**:
    ```json
    {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com"
        },
        "token": "random_token_string"
    }
    ```

### User Login

- **Method**: POST
- **URL**: `/api/login`
- **Description**: Authenticate user and get token
- **Authentication**: None
- **Request Body**:
    ```json
    {
        "email": "john@example.com",
        "password": "password123"
    }
    ```
- **Response**:
    ```json
    {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com"
        },
        "token": "random_token_string"
    }
    ```

### Get Current User

- **Method**: GET
- **URL**: `/api/me`
- **Description**: Get authenticated user's profile
- **Authentication**: Required (Bearer token)
- **Response**:
    ```json
    {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com"
        }
    }
    ```

### User Logout

- **Method**: POST
- **URL**: `/api/logout`
- **Description**: Invalidate the current token
- **Authentication**: Required (Bearer token)
- **Response**:
    ```json
    {
        "message": "Logged out successfully."
    }
    ```

### Update User Profile

- **Method**: PUT
- **URL**: `/api/profile`
- **Description**: Update authenticated user's profile
- **Authentication**: Required (Bearer token)
- **Request Body** (partial update allowed):
    ```json
    {
        "name": "Jane Doe",
        "email": "jane@example.com"
    }
    ```
- **Response**:
    ```json
    {
        "user": {
            "id": 1,
            "name": "Jane Doe",
            "email": "jane@example.com"
        },
        "message": "Profile updated successfully."
    }
    ```

### List All Users (Admin Only)

- **Method**: GET
- **URL**: `/api/users`
- **Description**: Get all users (admin only)
- **Authentication**: Required (Bearer token, admin role)
- **Response**:
    ```json
    [
        {
            "id": 1,
            "name": "Test User",
            "email": "test@example.com",
            "role": "user"
        },
        {
            "id": 2,
            "name": "Admin User",
            "email": "admin@example.com",
            "role": "admin"
        }
    ]
    ```

### List Treks

- **Method**: GET
- **URL**: `/api/treks`
- **Description**: Get all treks for the authenticated user (admins see all treks)
- **Authentication**: Required (Bearer token)
- **Response**:
    ```json
    [
        {
            "id": 1,
            "user_id": 1,
            "title": "Mountain Hike",
            "description": "A challenging mountain trail",
            "date": "2026-05-15",
            "created_at": "2026-05-08T10:00:00.000000Z",
            "updated_at": "2026-05-08T10:00:00.000000Z"
        }
    ]
    ```

### Create Trek

- **Method**: POST
- **URL**: `/api/treks`
- **Description**: Create a new trek for the authenticated user
- **Authentication**: Required (Bearer token)
- **Request Body**:
    ```json
    {
        "title": "Beach Walk",
        "description": "Relaxing walk along the shore",
        "date": "2026-05-20"
    }
    ```
- **Response**:
    ```json
    {
        "id": 2,
        "user_id": 1,
        "title": "Beach Walk",
        "description": "Relaxing walk along the shore",
        "date": "2026-05-20",
        "created_at": "2026-05-08T11:00:00.000000Z",
        "updated_at": "2026-05-08T11:00:00.000000Z"
    }
    ```

### Get Specific Trek

- **Method**: GET
- **URL**: `/api/treks/{trek}`
- **Description**: Get details of a specific trek (must belong to user)
- **Authentication**: Required (Bearer token)
- **Response**: Same as individual trek object in list

### Update Trek

- **Method**: PUT
- **URL**: `/api/treks/{trek}`
- **Description**: Update a specific trek (must belong to user)
- **Authentication**: Required (Bearer token)
- **Request Body** (partial update allowed):
    ```json
    {
        "title": "Updated Mountain Hike",
        "description": "An even more challenging trail"
    }
    ```
- **Response**: Updated trek object

### Delete Trek

- **Method**: DELETE
- **URL**: `/api/treks/{trek}`
- **Description**: Delete a specific trek (must belong to user)
- **Authentication**: Required (Bearer token)
- **Response**:
    ```json
    {
        "message": "Trek deleted successfully."
    }
    ```

## Error Handling

### Common Error Responses

- **401 Unauthorized**:

    ```json
    {
        "message": "Authorization token required."
    }
    ```

    or

    ```json
    {
        "message": "Invalid authorization token."
    }
    ```

- **403 Forbidden**:

    ```json
    {
        "message": "This resource does not belong to you."
    }
    ```

- **404 Not Found**:

    ```json
    {
        "message": "Resource not found."
    }
    ```

- **422 Unprocessable Entity** (Validation errors):

    ```json
    {
        "message": "The email field is required.",
        "errors": {
            "email": ["The email field is required."]
        }
    }
    ```

- **500 Internal Server Error**:
    ```json
    {
        "message": "Internal server error."
    }
    ```

## Data Models

### User

- `id`: integer (primary key)
- `name`: string
- `email`: string (unique)
- `password`: string (hashed)
- `role`: enum ('user', 'admin') (default: 'user')
- `api_token`: string (nullable, hashed)
- `created_at`: timestamp
- `updated_at`: timestamp

### Trek

- `id`: integer (primary key)
- `user_id`: integer (foreign key to users)
- `title`: string
- `description`: text (nullable)
- `date`: date (nullable)
- `created_at`: timestamp
- `updated_at`: timestamp

## Testing

Use tools like Postman or curl to test the endpoints. Demo users are seeded:

**Regular User:**

- Email: `test@example.com`
- Password: `password`

**Admin User:**

- Email: `admin@example.com`
- Password: `admin123`

Example curl command:

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"admin123"}'
```
