## Authorization

Every API action is scoped through `whilesmart/eloquent-owner-access`. The package never decides tenancy itself; the host application binds an `OwnerAuthorizer` to control which owners a user can access.

### How it Works

- **Index** - Constrained to owners the authenticated user can access.
- **Show, Update, Delete, Link-User** - Authorize the record's owner against the authenticated user.
- **Create** - Validates that the user has access to the specified owner.

### OwnerAuthorizer

The host application must bind an implementation of the `OwnerAuthorizer` contract:

```php
use Whilesmart\OwnerAccess\Contracts\OwnerAuthorizer;

$this->app->bind(OwnerAuthorizer::class, function ($app) {
    return new class implements OwnerAuthorizer {
        public function canAccess(mixed $user, string $ownerType, mixed $ownerId): bool
        {
            return $user->workspaces()
                ->where('workspaces.id', $ownerId)
                ->exists();
        }

        public function accessibleOwnerIds(mixed $user, string $ownerType): array
        {
            return $user->workspaces->pluck('id')->toArray();
        }
    };
});
```

See the `eloquent-owner-access` package documentation for more details.
