# API Documentation

This document describes the HTTP API exposed by this application, based on `routes/api.php` and the current controller implementations.

## Base URL

Use your application base URL, for example:

```
https://your-domain.test/api
```

## Authentication

This API uses Laravel Sanctum personal access tokens for protected routes.

### Register

`POST /register`

Request body:

```json
{
  "name": "Jane Doe",
  "email": "jane@example.com",
  "password": "secret123",
  "password_confirmation": "secret123"
}
```

Responses:

```json
{
  "token": "plain-text-token",
  "message": "User registered successfully"
}
```

Status codes:

- `201 Created` on success
- `422 Unprocessable Entity` for validation errors

### Login

`POST /login`

Request body:

```json
{
  "email": "jane@example.com",
  "password": "secret123"
}
```

Responses:

```json
{
  "token": "plain-text-token",
  "message": "User logged in successfully"
}
```

Status codes:

- `200 OK` on success
- `401 Unauthorized` for invalid credentials
- `422 Unprocessable Entity` for validation errors

### Logout (Protected)

`POST /logout`

Headers:

```
Authorization: Bearer {token}
```

Responses:

```json
{
  "message": "User logged out successfully"
}
```

Status codes:

- `200 OK` on success
- `401 Unauthorized` if the token is missing or invalid

## Protected Routes

All create, update, and delete operations require authentication with a Sanctum token.

```
Authorization: Bearer {token}
```

Some operations also require authorization via policies (for example, creating or updating industries). A `403 Forbidden` may be returned if the current user is not allowed to perform the action.

## Industries

### List Industries

`GET /industries`

Response example:

```json
[
  {
    "id": 1,
    "name": "Manufacturing",
    "slug": "manufacturing",
    "description": "Industrial manufacturing services",
    "category_id": 2,
    "services": [],
    "category": {
      "id": 2,
      "name": "Industrial",
      "slug": "industrial"
    }
  }
]
```

Status codes:

- `200 OK`

### Get Industry

`GET /industries/{industry}`

Response example:

```json
{
  "id": 1,
  "name": "Manufacturing",
  "slug": "manufacturing",
  "description": "Industrial manufacturing services",
  "category_id": 2
}
```

Status codes:

- `200 OK`
- `404 Not Found` if the industry does not exist

### Create Industry (Protected)

`POST /industries`

Request body:

```json
{
  "name": "Manufacturing",
  "description": "Industrial manufacturing services",
  "category_id": 2
}
```

Notes:

- `slug` is generated from `name`.
- If a slug already exists, the API returns `409 Conflict`.

Response example:

```json
{
  "message": "Industry created successfully",
  "data": {
    "id": 1,
    "name": "Manufacturing",
    "slug": "manufacturing",
    "description": "Industrial manufacturing services",
    "category_id": 2
  }
}
```

Status codes:

- `201 Created`
- `401 Unauthorized` if the token is missing or invalid
- `403 Forbidden` if the user is not authorized by policy
- `409 Conflict` if the generated slug already exists
- `422 Unprocessable Entity` for validation errors

### Update Industry (Protected)

`PUT /industries/{industry}`

Request body:

```json
{
  "name": "Advanced Manufacturing",
  "description": "Updated description",
  "category_id": 2
}
```

Response example:

```json
{
  "message": "Industry updated successfully",
  "data": {
    "id": 1,
    "name": "Advanced Manufacturing",
    "slug": "advanced-manufacturing",
    "description": "Updated description",
    "category_id": 2
  }
}
```

Status codes:

- `200 OK`
- `401 Unauthorized` if the token is missing or invalid
- `403 Forbidden` if the user is not authorized by policy
- `422 Unprocessable Entity` for validation errors

### Delete Industry (Protected)

`DELETE /industries/{industry}`

Response example:

```json
{
  "message": "Industry deleted successfully"
}
```

Status codes:

- `200 OK`
- `401 Unauthorized` if the token is missing or invalid
- `403 Forbidden` if the user is not authorized by policy
- `404 Not Found` if the industry does not exist

## Services

### List Services

`GET /services`

Response example:

```json
[
  {
    "id": 1,
    "name": "Welding",
    "slug": "welding",
    "industry_id": 1
  }
]
```

Status codes:

- `200 OK`

### Get Service

`GET /services/{service}`

Response example:

```json
{
  "id": 1,
  "name": "Welding",
  "slug": "welding",
  "industry_id": 1
}
```

Status codes:

- `200 OK`
- `404 Not Found` if the service does not exist

### Create Service (Protected)

`POST /services`

Request body:

```json
{
  "name": "Welding",
  "industry_id": 1
}
```

Notes:

- `slug` is generated from `name`.
- If a slug already exists, the API returns `409 Conflict`.

Response example:

```json
{
  "message": "Service created successfully",
  "data": {
    "id": 1,
    "name": "Welding",
    "slug": "welding",
    "industry_id": 1
  }
}
```

Status codes:

- `201 Created`
- `401 Unauthorized` if the token is missing or invalid
- `409 Conflict` if the generated slug already exists
- `422 Unprocessable Entity` for validation errors

### Update Service (Protected)

`PUT /services/{service}`

Request body:

```json
{
  "name": "Advanced Welding",
  "industry_id": 1
}
```

Response example:

```json
{
  "message": "Service updated successfully",
  "data": {
    "id": 1,
    "name": "Advanced Welding",
    "slug": "advanced-welding",
    "industry_id": 1
  }
}
```

Status codes:

- `200 OK`
- `401 Unauthorized` if the token is missing or invalid
- `409 Conflict` if the generated slug already exists
- `422 Unprocessable Entity` for validation errors

### Delete Service (Protected)

`DELETE /services/{service}`

Response example:

```json
{
  "message": "Service deleted successfully"
}
```

Status codes:

- `200 OK`
- `401 Unauthorized` if the token is missing or invalid
- `404 Not Found` if the service does not exist

## Categories

### List Categories

`GET /categories`

Response example:

```json
[
  {
    "id": 1,
    "name": "Industrial",
    "slug": "industrial"
  }
]
```

Status codes:

- `200 OK`

### Get Category

`GET /categories/{category}`

Response example:

```json
{
  "id": 1,
  "name": "Industrial",
  "slug": "industrial"
}
```

Status codes:

- `200 OK`
- `404 Not Found` if the category does not exist

### Create Category (Protected)

`POST /categories`

Request body:

```json
{
  "name": "Industrial"
}
```

Notes:

- `slug` is generated from `name`.
- If a slug already exists, the API returns `409 Conflict`.

Response example:

```json
{
  "message": "Category created successfully",
  "data": {
    "id": 1,
    "name": "Industrial",
    "slug": "industrial"
  }
}
```

Status codes:

- `201 Created`
- `401 Unauthorized` if the token is missing or invalid
- `409 Conflict` if the generated slug already exists
- `422 Unprocessable Entity` for validation errors

### Update Category (Protected)

`PUT /categories/{category}`

Notes:

- `routes/api.php` includes this route, but the current `CategoryController` does not implement an `update` method yet.

### Delete Category (Protected)

`DELETE /categories/{category}`

Notes:

- `routes/api.php` includes this route, but the current `CategoryController` does not implement a `destroy` method yet.

## Error Format

Validation errors follow Laravel’s default structure, for example:

```json
{
  "message": "The name field is required.",
  "errors": {
    "name": [
      "The name field is required."
    ]
  }
}
```
