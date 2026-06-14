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
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'email' => 'ada@acme.test',
            'title' => 'Engineer',
            'employment_type' => 'full_time',
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('data.first_name', 'Ada');
        $response->assertJsonPath('data.name', 'Ada Lovelace');
        $response->assertJsonPath('data.status', 'active');
        $response->assertJsonPath('data.user_id', null);
        $this->assertSame(1, Employee::count());
    }

    #[Test]
    public function post_rejects_without_first_name(): void
    {
        $ws = HostWorkspace::create(['name' => 'Acme']);

        $response = $this->postJson('/api/employees', [
            'owner_type' => HostWorkspace::class,
            'owner_id' => $ws->id,
            'email' => 'noname@acme.test',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['first_name']);
    }

    #[Test]
    public function post_rejects_end_date_before_start_date(): void
    {
        $ws = HostWorkspace::create(['name' => 'Acme']);

        $response = $this->postJson('/api/employees', [
            'owner_type' => HostWorkspace::class,
            'owner_id' => $ws->id,
            'first_name' => 'Grace',
            'last_name' => 'Hopper',
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

        $wsA->employees()->create(['first_name' => 'A1']);
        $wsA->employees()->create(['first_name' => 'A2']);
        $wsB->employees()->create(['first_name' => 'B1']);

        $response = $this->getJson('/api/employees?owner_type='.urlencode(HostWorkspace::class).'&owner_id='.$wsA->id);

        $response->assertStatus(200);
        $response->assertJsonPath('data.meta.total', 2);
    }

    #[Test]
    public function index_filters_by_status(): void
    {
        $ws = HostWorkspace::create(['name' => 'Acme']);
        $ws->employees()->create(['first_name' => 'Active', 'status' => 'active']);
        $ws->employees()->create(['first_name' => 'Gone', 'status' => 'terminated']);
        $ws->employees()->create(['first_name' => 'Gone2', 'status' => 'terminated']);

        $response = $this->getJson('/api/employees?status=terminated');

        $response->assertStatus(200);
        $response->assertJsonPath('data.meta.total', 2);
    }

    #[Test]
    public function index_searches_by_name_email_and_title(): void
    {
        $ws = HostWorkspace::create(['name' => 'Acme']);
        $ws->employees()->create(['first_name' => 'Ada', 'last_name' => 'Lovelace', 'title' => 'Engineer']);
        $ws->employees()->create(['first_name' => 'Grace', 'last_name' => 'Hopper', 'title' => 'Admiral']);

        $this->getJson('/api/employees?q=lovelace')->assertStatus(200)->assertJsonPath('data.meta.total', 1);
        $this->getJson('/api/employees?q=admiral')->assertStatus(200)->assertJsonPath('data.meta.total', 1);
        $this->getJson('/api/employees?q=zzz')->assertStatus(200)->assertJsonPath('data.meta.total', 0);
    }

    #[Test]
    public function link_user_attaches_a_login_and_fires_event(): void
    {
        Event::fake([EmployeeLinkedToUser::class]);

        $ws = HostWorkspace::create(['name' => 'Acme']);
        $employee = $ws->employees()->create(['first_name' => 'Payroll', 'last_name' => 'Only']);
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
        $employee = $ws->employees()->create(['first_name' => 'Temp']);

        $response = $this->deleteJson("/api/employees/{$employee->id}");

        $response->assertStatus(200);
        $this->assertSame(0, Employee::count());
        $this->assertSame(1, Employee::withTrashed()->count());
    }
}
