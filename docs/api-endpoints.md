## API Endpoints

The package registers a RESTful API resource controller under the configured prefix (default `api`) with Sanctum authentication middleware.

### List Employees

```http
GET /api/employees
```

**Query Parameters:**
| Parameter | Type | Description |
|---|---|---|
| `owner_type` | string | Filter by owner morph type |
| `owner_id` | integer | Filter by owner ID |
| `status` | enum | Filter by status (`active`, `inactive`, `on_leave`, `terminated`) |
| `employment_type` | enum | Filter by type (`full_time`, `part_time`, `contractor`) |
| `department` | string | Filter by department |
| `has_login` | boolean | Filter to employees with or without a linked user |
| `q` | string | Full-text search across first name, last name, email, and title |
| `per_page` | integer | Pagination size (default: 25) |

The index is scoped to owners the authenticated user can access through `eloquent-owner-access`.

### Create Employee

```http
POST /api/employees
```

**Body Parameters:**
| Parameter | Type | Required | Description |
|---|---|---|---|
| `owner_type` | string | yes | Morph type of the employing business |
| `owner_id` | mixed | yes | Morph ID of the employing business |
| `first_name` | string | yes | First name |
| `last_name` | string | no | Last name |
| `email` | email | no | Email, unique per owner |
| `phone` | string | no | Phone number |
| `title` | string | no | Job title |
| `department` | string | no | Department |
| `status` | enum | no | Defaults to `active` |
| `employment_type` | enum | no | Defaults to `full_time` |
| `start_date` | date | no | Start date |
| `end_date` | date | no | Must be after or equal to start date |
| `metadata` | object | no | Arbitrary extra data |

### Get Employee

```http
GET /api/employees/{employee}
```

Returns a single employee record. Authorized against the employee's owner.

### Update Employee

```http
PUT /api/employees/{employee}
```

Accepts the same body parameters as create. Only the provided fields are updated.

### Delete Employee

```http
DELETE /api/employees/{employee}
```

Soft-deletes the employee record.

### Link to User

```http
POST /api/employees/{employee}/link-user
```

**Body Parameters:**
| Parameter | Type | Required | Description |
|---|---|---|---|
| `user_id` | integer | yes | The user ID to link |

Links the employee to an authenticated user account and dispatches `EmployeeLinkedToUser`.
