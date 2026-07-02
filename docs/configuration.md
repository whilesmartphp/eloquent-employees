## Configuration

Publish the configuration:

```bash
php artisan vendor:publish --tag=employees-config
```

### Config File (`config/employees.php`)

```php
return [
    'register_routes' => env('EMPLOYEES_REGISTER_ROUTES', true),
    'route_prefix' => env('EMPLOYEES_ROUTE_PREFIX', 'api'),
    'route_middleware' => ['api', 'auth:sanctum'],
    'table' => env('EMPLOYEES_TABLE', 'employees'),
    'model' => Employee::class,
    'user_model' => env('EMPLOYEES_USER_MODEL', 'App\\Models\\User'),
];
```

### Configuration Options

| Key | Type | Default | Description |
|---|---|---|---|
| `register_routes` | bool | `true` | Whether to register the API routes |
| `route_prefix` | string | `api` | URL prefix for all employee routes |
| `route_middleware` | array | `['api', 'auth:sanctum']` | Middleware applied to routes |
| `table` | string | `employees` | Database table name |
| `model` | string | `Employee::class` | The employee model class (can be swapped) |
| `user_model` | string | `App\Models\User` | User model for the `user()` relation |
