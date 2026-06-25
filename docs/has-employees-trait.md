## HasEmployees Trait

The `HasEmployees` trait provides a polymorphic one-to-many relationship from your employing model to employee records.

### Usage

Attach the trait to any model that represents a business, workspace, or organisation:

```php
use Whilesmart\Employees\Traits\HasEmployees;

class Workspace extends Model
{
    use HasEmployees;
}
```

### Methods

#### employees()

Returns a `MorphMany` relationship to the `Employee` model:

```php
$workspace->employees; // Collection of Employee models

$workspace->employees()->where('status', 'active')->get();
$workspace->employees()->where('department', 'Engineering')->count();
```

### Relationship to eloquent-workspaces

This trait is the employee-side counterpart to workspace members. Workspace members are authenticated users belonging to a workspace; employees are people records that may or may not have a login. The two are linked through the employee's `user_id` field.
