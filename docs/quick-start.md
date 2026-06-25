## Quick Start

### 1. Attach the Trait

Add the `HasEmployees` trait to the model that employs people, such as a workspace or organisation:

```php
use Whilesmart\Employees\Traits\HasEmployees;

class Workspace extends Model
{
    use HasEmployees;
}
```

### 2. Create an Employee

```php
use Whilesmart\Employees\Models\Employee;

$employee = Employee::create([
    'owner_type' => 'workspace',
    'owner_id' => $workspace->id,
    'first_name' => 'Jane',
    'last_name' => 'Doe',
    'email' => 'jane@example.com',
    'title' => 'Software Engineer',
    'department' => 'Engineering',
    'employment_type' => 'full_time',
]);
```

### 3. Query Employees

```php
$workspace->employees; // all employees belonging to this workspace
```

Or through the API:

```bash
curl -H "Authorization: Bearer $token" /api/employees?owner_type=workspace&owner_id=1
```

### 4. Link to a User

Link an employee record to an authenticated user:

```php
$employee->user_id = $user->id;
$employee->save();
```

Or via the API:

```bash
curl -X POST /api/employees/1/link-user \
  -H "Authorization: Bearer $token" \
  -H "Content-Type: application/json" \
  -d '{"user_id": 42}'
```

### 5. Assign Roles

Since employees use the `HasRoles` trait from eloquent-roles, assign role bundles:

```php
$employee->assignRoles(['accountant', 'manager']);
```
