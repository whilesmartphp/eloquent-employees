## Data Model

An employee is a polymorphic party record scoped to its owner. It is distinct from the authenticated user model.

### Fields

| Field | Type | Description |
|---|---|---|
| `id` | integer | Auto-incrementing primary key |
| `owner_type` | string | Morph type of the employing business |
| `owner_id` | integer | Morph ID of the employing business |
| `user_id` | integer, nullable | Optional link to an authenticated user |
| `reporting_to_id` | integer, nullable | Self-reference to the employee's manager |
| `first_name` | string | First name |
| `last_name` | string, nullable | Last name |
| `email` | string, nullable | Email address, unique per owner |
| `phone` | string, nullable | Phone number |
| `title` | string, nullable | Job title |
| `department` | string, nullable | Department name |
| `status` | enum | `active`, `inactive`, `on_leave`, `terminated` (default: `active`) |
| `employment_type` | enum | `full_time`, `part_time`, `contractor` (default: `full_time`) |
| `start_date` | date, nullable | Employment start date |
| `end_date` | date, nullable | Employment end date |
| `metadata` | json, nullable | Host-specific extras such as avatar URLs |
| `created_at` | timestamp | Laravel timestamp |
| `updated_at` | timestamp | Laravel timestamp |
| `deleted_at` | timestamp, nullable | Soft delete timestamp |

### Accessors

| Accessor | Type | Description |
|---|---|---|
| `name` | string | Read-only full name (`first_name last_name`) |

### Relationships

| Relation | Type | Target |
|---|---|---|
| `owner()` | MorphTo | The employing business (polymorphic) |
| `user()` | BelongsTo | The linked authenticated user |
| `manager()` | BelongsTo | The employee's manager (self-reference) |
| `reports()` | HasMany | Direct reports (self-reference) |

### Enums

**EmployeeStatus**: `active`, `inactive`, `on_leave`, `terminated`

**EmploymentType**: `full_time`, `part_time`, `contractor`

### Soft Deletes

Employees use soft deletes. Records are not removed from the database on delete; the `deleted_at` timestamp is set instead. Queries exclude soft-deleted records by default.

### Roles

Employees use the `HasRoles` trait from `whilesmart/eloquent-roles`, so role bundles can be assigned in a workspace context. Roles are accessed through the same API as the eloquent-roles package.
