<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\HostWorkspace;
use Tests\TestCase;
use Whilesmart\Employees\Events\EmployeeLinkedToUser;
use Whilesmart\Employees\Models\Employee;

class EmployeeApiTest extends TestCase
{
    #[Test]
    public function post_creates_an_employee(): void
    {
        $ws = HostWorkspace::create(['name' => 'Acme']);

        $response = $this->postJson('/api/employees', [
            'owner_type' => HostWorkspace::class,
            'owner_id' => $ws->id,
            'name' => 'Ada Lovelace',
            'email' => 'ada@acme.test',
            'title' => 'Engineer',
            'employment_type' => 'full_time',
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('data.name', 'Ada Lovelace');
        $response->assertJsonPath('data.status', 'active');
        $response->assertJsonPath('data.user_id', null);
        $this->assertSame(1, Employee::count());
    }

    #[Test]
    public function post_rejects_without_name(): void
    {
        $ws = HostWorkspace::create(['name' => 'Acme']);

        $response = $this->postJson('/api/employees', [
            'owner_type' => HostWorkspace::class,
            'owner_id' => $ws->id,
            'email' => 'noname@acme.test',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    #[Test]
    public function post_rejects_end_date_before_start_date(): void
    {
        $ws = HostWorkspace::create(['name' => 'Acme']);

        $response = $this->postJson('/api/employees', [
            'owner_type' => HostWorkspace::class,
            'owner_id' => $ws->id,
            'name' => 'Grace Hopper',
            'start_date' => '2024-01-10',
            'end_date' => '2024-01-01',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['end_date']);
    }

    #[Test]
    public function index_filters_by_owner(): void
    {
        $wsA = HostWorkspace::create(['name' => 'Acme A']);
        $wsB = HostWorkspace::create(['name' => 'Acme B']);

        $wsA->employees()->create(['name' => 'A1']);
        $wsA->employees()->create(['name' => 'A2']);
        $wsB->employees()->create(['name' => 'B1']);

        $response = $this->getJson('/api/employees?owner_type='.urlencode(HostWorkspace::class).'&owner_id='.$wsA->id);

        $response->assertStatus(200);
        $response->assertJsonPath('data.meta.total', 2);
    }

    #[Test]
    public function index_filters_by_status(): void
    {
        $ws = HostWorkspace::create(['name' => 'Acme']);
        $ws->employees()->create(['name' => 'Active One', 'status' => 'active']);
        $ws->employees()->create(['name' => 'Gone One', 'status' => 'terminated']);
        $ws->employees()->create(['name' => 'Gone Two', 'status' => 'terminated']);

        $response = $this->getJson('/api/employees?status=terminated');

        $response->assertStatus(200);
        $response->assertJsonPath('data.meta.total', 2);
    }

    #[Test]
    public function link_user_attaches_a_login_and_fires_event(): void
    {
        Event::fake([EmployeeLinkedToUser::class]);

        $ws = HostWorkspace::create(['name' => 'Acme']);
        $employee = $ws->employees()->create(['name' => 'Payroll Only']);
        $this->assertNull($employee->user_id);

        $response = $this->postJson("/api/employees/{$employee->id}/link-user", [
            'user_id' => 99,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.user_id', 99);
        $this->assertSame(99, $employee->fresh()->user_id);
        Event::assertDispatched(EmployeeLinkedToUser::class);
    }

    #[Test]
    public function delete_soft_deletes_the_employee(): void
    {
        $ws = HostWorkspace::create(['name' => 'Acme']);
        $employee = $ws->employees()->create(['name' => 'Temp']);

        $response = $this->deleteJson("/api/employees/{$employee->id}");

        $response->assertStatus(200);
        $this->assertSame(0, Employee::count());
        $this->assertSame(1, Employee::withTrashed()->count());
    }
}
