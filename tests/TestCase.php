<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Whilesmart\Employees\EmployeesServiceProvider;
use Whilesmart\OwnerAccess\OwnerAccessServiceProvider;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // A separate table for the test host model so the polymorphic owner
        // type column genuinely disambiguates record sources.
        Schema::create('workspaces', function ($table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    protected function getPackageProviders($app): array
    {
        return [
            OwnerAccessServiceProvider::class,
            EmployeesServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Keep routes on for API tests; drop auth middleware so tests don't
        // need a sanctum user. Consuming apps keep auth:sanctum.
        $app['config']->set('employees.route_middleware', ['api']);
    }
}
