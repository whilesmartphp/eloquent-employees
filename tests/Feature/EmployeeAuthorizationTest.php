<?php

namespace Tests\Feature;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\HostWorkspace;
use Tests\TestCase;
use Whilesmart\OwnerAccess\Contracts\OwnerAuthorizer;

class EmployeeAuthorizationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->instance(OwnerAuthorizer::class, new class implements OwnerAuthorizer
        {
            public function authorize(?Authenticatable $user, string $ownerType, mixed $ownerId): bool
            {
                return false;
            }

            public function scope(Builder $query, ?Authenticatable $user, string $ownerTypeColumn = 'owner_type', string $ownerIdColumn = 'owner_id'): Builder
            {
                return $query->whereRaw('0 = 1');
            }
        });
    }

    #[Test]
    public function store_returns_403_when_authorizer_denies(): void
    {
        $ws = HostWorkspace::create(['name' => 'Acme']);

        $this->postJson('/api/employees', [
            'owner_type' => HostWorkspace::class,
            'owner_id' => $ws->id,
            'name' => 'Denied Person',
        ])->assertForbidden();
    }

    #[Test]
    public function show_returns_403_when_authorizer_denies(): void
    {
        $ws = HostWorkspace::create(['name' => 'Acme']);
        $employee = $ws->employees()->create(['name' => 'Hidden']);

        $this->getJson("/api/employees/{$employee->id}")->assertForbidden();
    }

    #[Test]
    public function update_returns_403_when_authorizer_denies(): void
    {
        $ws = HostWorkspace::create(['name' => 'Acme']);
        $employee = $ws->employees()->create(['name' => 'Original']);

        $this->putJson("/api/employees/{$employee->id}", [
            'name' => 'Renamed',
        ])->assertForbidden();

        $this->assertSame('Original', $employee->fresh()->name);
    }

    #[Test]
    public function destroy_returns_403_when_authorizer_denies(): void
    {
        $ws = HostWorkspace::create(['name' => 'Acme']);
        $employee = $ws->employees()->create(['name' => 'Keep Me']);

        $this->deleteJson("/api/employees/{$employee->id}")->assertForbidden();

        $this->assertNotNull($employee->fresh());
    }

    #[Test]
    public function link_user_returns_403_when_authorizer_denies(): void
    {
        $ws = HostWorkspace::create(['name' => 'Acme']);
        $employee = $ws->employees()->create(['name' => 'No Login']);

        $this->postJson("/api/employees/{$employee->id}/link-user", [
            'user_id' => 5,
        ])->assertForbidden();

        $this->assertNull($employee->fresh()->user_id);
    }

    #[Test]
    public function index_applies_scope_from_authorizer(): void
    {
        $ws = HostWorkspace::create(['name' => 'Acme']);
        $ws->employees()->create(['name' => 'Scoped Out']);

        $response = $this->getJson('/api/employees')->assertOk();

        $this->assertSame(0, $response->json('data.meta.total'));
    }
}
