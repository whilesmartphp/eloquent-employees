<?php

use Whilesmart\Employees\Models\Employee;

return [
    'register_routes' => env('EMPLOYEES_REGISTER_ROUTES', true),
    'route_prefix' => env('EMPLOYEES_ROUTE_PREFIX', 'api'),
    'route_middleware' => ['api', 'auth:sanctum'],
    'table' => env('EMPLOYEES_TABLE', 'employees'),
    'model' => Employee::class,
    'user_model' => env('EMPLOYEES_USER_MODEL', 'App\\Models\\User'),
];
