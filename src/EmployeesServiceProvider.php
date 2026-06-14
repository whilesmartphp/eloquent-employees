<?php

namespace Whilesmart\Employees;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Whilesmart\Employees\Models\Employee;

class EmployeesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/employees.php', 'employees');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../config/employees.php' => config_path('employees.php'),
        ], 'employees-config');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'employees-migrations');

        if (config('employees.register_routes', true)) {
            Route::model('employee', config('employees.model', Employee::class));

            Route::middleware(config('employees.route_middleware', ['api', 'auth:sanctum']))
                ->prefix(config('employees.route_prefix', 'api'))
                ->group(__DIR__.'/../routes/api.php');
        }
    }
}
