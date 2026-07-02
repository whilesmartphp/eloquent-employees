## Installation

Install the package via Composer:

```bash
composer require whilesmart/eloquent-employees
```

Run the migrations:

```bash
php artisan migrate
```

Optionally publish the configuration:

```bash
php artisan vendor:publish --tag=employees-config
php artisan vendor:publish --tag=employees-migrations
```

### Requirements

- PHP 8.2 or later
- Laravel 11 or 12
- `whilesmart/eloquent-owner-access` for authorization
- `whilesmart/eloquent-roles` for role assignment on employee records

### Environment Variables

| Variable | Default | Description |
|---|---|---|
| `EMPLOYEES_REGISTER_ROUTES` | `true` | Register the API routes |
| `EMPLOYEES_ROUTE_PREFIX` | `api` | Route prefix |
| `EMPLOYEES_TABLE` | `employees` | Database table name |
| `EMPLOYEES_USER_MODEL` | `App\Models\User` | User model class for the `user()` relation |
