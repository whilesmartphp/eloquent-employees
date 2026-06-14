# Eloquent Employees

Polymorphic employee records for Laravel. An employee belongs to a business (workspace / organisation) and
may optionally be linked to an authenticated user. People who never log in (payroll-only staff, contractors)
are first-class: they exist as records and can be referenced in expenses, payroll, and reports without an
account.

## Install

```
composer require whilesmart/eloquent-employees
php artisan migrate
```

Attach `HasEmployees` to the model that employs people (workspace / organisation):

```php
use Whilesmart\Employees\Traits\HasEmployees;

class Workspace extends Model
{
    use HasEmployees;
}
```

## Data model

An employee is a party record scoped to its owner, distinct from the auth user:

- **`owner`** -- the employing business, polymorphic (`owner_type` + `owner_id`), required.
- **`user_id`** -- optional link to an authenticated user. Null for people without a login.
- **`reporting_to_id`** -- optional self-reference to the employee's manager.

Other fields: `first_name`, `last_name`, `email`, `phone`, `title`, `department`, `status`
(`active | inactive | on_leave | terminated`), `employment_type` (`full_time | part_time | contractor`),
`start_date`, `end_date`, `metadata`. A read-only `name` accessor returns the full name. Host-specific
extras (such as an avatar) live in the `metadata` bag, not on the core table.

`email` is unique per owner, not globally. Employees use the `HasRoles` trait from
`whilesmart/eloquent-roles`, so role bundles (accountant, manager, ...) can be assigned in a workspace
context.

## Routes

Registers an `apiResource` plus a link action at the configured prefix (default `api`, middleware
`['api', 'auth:sanctum']`):

```
GET    /api/employees
POST   /api/employees
GET    /api/employees/{employee}
PUT    /api/employees/{employee}
DELETE /api/employees/{employee}
POST   /api/employees/{employee}/link-user
```

`link-user` attaches a `user_id` to an employee and fires `EmployeeLinkedToUser`; the host app listens to
that event to add the user to the workspace, send an invite, or grant a default role bundle.

Index filters: `owner_type`, `owner_id`, `status`, `employment_type`, `department`, `has_login`, `q`,
`per_page`.

## Authorization

Every action is scoped through `whilesmart/eloquent-owner-access`. The host app binds an `OwnerAuthorizer`;
`index` is constrained to accessible owners and `show`/`update`/`destroy`/`link-user` authorize the record's
owner. The package never decides tenancy itself.

## Relationship to other packages

- **`whilesmart/eloquent-workspaces`** -- workspace *members* are auth users; employees are people, with or
  without a login. Link the two via `user_id`.
- **`whilesmart/eloquent-roles`** -- employees are assignable role holders; capability bundles live here.
- **`whilesmart/eloquent-expenses`** -- attribute who incurred or approved an expense to an employee.

## Config

`php artisan vendor:publish --tag=employees-config`:

```php
return [
    'register_routes' => env('EMPLOYEES_REGISTER_ROUTES', true),
    'route_prefix' => env('EMPLOYEES_ROUTE_PREFIX', 'api'),
    'route_middleware' => ['api', 'auth:sanctum'],
    'table' => env('EMPLOYEES_TABLE', 'employees'),
    'user_model' => env('EMPLOYEES_USER_MODEL', 'App\\Models\\User'),
];
```
