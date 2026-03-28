# API Documentation

This document describes the HTTP API exposed by this application, based on current `routes/api.php` and controller/resource implementations.

## Base URL

Use your application base URL, for example:

```text
https://your-domain.test/api/v1
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

Response:

```json
{
  "token": "plain-text-token",
  "message": "User registered successfully"
}
```

Status codes:

- `201 Created`
- `422 Unprocessable Entity`

### Login

`POST /login`

Request body:

```json
{
  "email": "jane@example.com",
  "password": "secret123"
}
```

Response:

```json
{
  "token": "plain-text-token",
  "message": "User logged in successfully"
}
```

Status codes:

- `200 OK`
- `401 Unauthorized`
- `422 Unprocessable Entity`

### Logout (Protected)

`POST /logout`

Header:

```text
Authorization: Bearer {token}
```

Response:

```json
{
  "message": "User logged out successfully"
}
```

Status codes:

- `200 OK`
- `401 Unauthorized`

### Forgot Password

`POST /forgot-password`

Request body:

```json
{
  "email": "jane@example.com"
}
```

Response:

```json
{
  "message": "We have emailed your password reset link."
}
```

Notes:

- The reset email uses `FRONTEND_RESET_PASSWORD_URL` when present.
- If `FRONTEND_RESET_PASSWORD_URL` is not set, the email falls back to `{APP_URL}/reset-password`.
- The generated link includes `token` and `email` query parameters for your frontend reset screen.
- The endpoint always returns the same success message, even for unknown emails, to avoid account enumeration.

Status codes:

- `200 OK`
- `422 Unprocessable Entity`

### Reset Password

`POST /reset-password`

Request body:

```json
{
  "email": "jane@example.com",
  "token": "reset-token-from-email",
  "password": "new-secret123",
  "password_confirmation": "new-secret123"
}
```

Response:

```json
{
  "message": "Your password has been reset."
}
```

Notes:

- Successful password resets revoke all of the user's Sanctum tokens.

Status codes:

- `200 OK`
- `400 Bad Request`
- `422 Unprocessable Entity`

## Protected Routes

Create, update, and delete operations require a Sanctum token:

```text
Authorization: Bearer {token}
```

Industry create/update/delete also enforce policy authorization and may return `403 Forbidden`.

## Response Shape

### Paginated list endpoints

`GET /industries`, `GET /services`, `GET /categories` return Laravel API Resource pagination:

```json
{
  "data": [],
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": "..."
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "...",
    "per_page": 15,
    "to": 1,
    "total": 1
  },
  "message": "... fetched successfully"
}
```

`per_page` query parameter is supported on list endpoints (`1` to `100`, default `15`).

### Single/create/update endpoints

These return:

```json
{
  "message": "...",
  "data": {}
}
```

## Industries

### List Industries

`GET /industries`

Query params:

- `per_page` (optional): integer between `1` and `100`

Notes:

- Each industry includes associated `services` and `category` summary.
- `services_count` is also included.

`data[]` item shape:

```json
{
  "id": 1,
  "name": "Inventory Management",
  "slug": "inventory-management",
  "description": "...",
  "icon": "heroicon-o-building-office-2",
  "category_id": 1,
  "services_count": 2,
  "category": {
    "id": 1,
    "name": "Supply chain",
    "slug": "supply-chain"
  },
  "services": [
    {
      "id": 10,
      "name": "Track stock levels",
      "slug": "track-stock-levels",
      "industry_id": 1
    }
  ]
}
```

Status codes:

- `200 OK`

### Get Industry

`GET /industries/{industry}`

Response `data` contains the same shape as above (including `services`, `category`, and `services_count`).

Status codes:

- `200 OK`
- `404 Not Found`

### Create Industry (Protected)

`POST /industries`

Request body:

```json
{
  "name": "Inventory Management",
  "description": "Optional description",
  "icon": "heroicon-o-building-office-2",
  "category_id": 1
}
```

Notes:

- `slug` is generated from trimmed `name`.
- `name`, `slug`, and `icon` are sanitized (trimmed) before persistence.
- If generated slug already exists, API returns `409 Conflict`.

Status codes:

- `201 Created`
- `401 Unauthorized`
- `403 Forbidden`
- `409 Conflict`
- `422 Unprocessable Entity`

### Update Industry (Protected)

`PUT /industries/{industry}`

Request body:

```json
{
  "name": "Inventory Management Updated",
  "description": "Updated description",
  "icon": "heroicon-o-cog",
  "category_id": 1
}
```

Notes:

- `slug` is regenerated from trimmed `name`.
- Slug uniqueness is enforced (excluding current record).

Status codes:

- `200 OK`
- `401 Unauthorized`
- `403 Forbidden`
- `409 Conflict`
- `422 Unprocessable Entity`

### Delete Industry (Protected)

`DELETE /industries/{industry}`

Response:

```json
{
  "message": "Industry deleted successfully"
}
```

Status codes:

- `200 OK`
- `401 Unauthorized`
- `403 Forbidden`
- `404 Not Found`

## Services

### List Services

`GET /services`

Query params:

- `per_page` (optional): integer between `1` and `100`

`data[]` item shape:

```json
{
  "id": 1,
  "name": "Track stock levels",
  "slug": "track-stock-levels",
  "industry_id": 1,
  "industry": {
    "id": 1,
    "name": "Inventory Management",
    "slug": "inventory-management"
  }
}
```

Status codes:

- `200 OK`

### Get Service

`GET /services/{service}`

Response `data` follows the same shape as list item (including `industry` summary).

Status codes:

- `200 OK`
- `404 Not Found`

### Create Service (Protected)

`POST /services`

Request body:

```json
{
  "name": "Track stock levels",
  "industry_id": 1
}
```

Notes:

- `slug` is generated from trimmed `name`.
- Slug uniqueness enforced.

Status codes:

- `201 Created`
- `401 Unauthorized`
- `409 Conflict`
- `422 Unprocessable Entity`

### Update Service (Protected)

`PUT /services/{service}`

Request body:

```json
{
  "name": "Track stock levels and warehouses",
  "industry_id": 1
}
```

Status codes:

- `200 OK`
- `401 Unauthorized`
- `409 Conflict`
- `422 Unprocessable Entity`

### Delete Service (Protected)

`DELETE /services/{service}`

Response:

```json
{
  "message": "Service deleted successfully"
}
```

Status codes:

- `200 OK`
- `401 Unauthorized`
- `404 Not Found`

## Categories

### List Categories

`GET /categories`

Query params:

- `per_page` (optional): integer between `1` and `100`

`data[]` item shape:

```json
{
  "id": 1,
  "name": "Supply chain",
  "slug": "supply-chain",
  "industries_count": 3
}
```

Status codes:

- `200 OK`

### Get Category

`GET /categories/{category}`

Response `data` follows the same shape as list item.

Status codes:

- `200 OK`
- `404 Not Found`

### Create Category (Protected)

`POST /categories`

Request body:

```json
{
  "name": "Supply chain"
}
```

Notes:

- `slug` is generated from trimmed `name`.
- Slug uniqueness enforced.

Status codes:

- `201 Created`
- `401 Unauthorized`
- `409 Conflict`
- `422 Unprocessable Entity`

### Update Category (Protected)

`PUT /categories/{category}`

Request body:

```json
{
  "name": "Supply chain and logistics"
}
```

Status codes:

- `200 OK`
- `401 Unauthorized`
- `409 Conflict`
- `422 Unprocessable Entity`

### Delete Category (Protected)

`DELETE /categories/{category}`

Response:

```json
{
  "message": "Category deleted successfully"
}
```

Status codes:

- `200 OK`
- `401 Unauthorized`
- `404 Not Found`

## Validation Error Format

Validation errors follow Laravel's default format:

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": [
      "The name field is required."
    ]
  }
}
```
