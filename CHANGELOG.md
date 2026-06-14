# Changelog

All notable changes to `whilesmart/eloquent-employees` are documented here.

## [1.0.0] - 2026-06-14

- Initial release
- `Employee` model with `first_name` / `last_name` (plus a read-only full-name `name` accessor)
- `Employee` model with polymorphic `owner` (the employing workspace or organisation)
- Optional `user_id` link so an employee may exist without a login (payroll-only staff, contractors) and be connected to an authenticated user later
- `HasEmployees` trait for owner-side models (workspaces, organisations)
- `HasRoles` integration so employees can be assigned roles via `whilesmart/eloquent-roles`
- Self-referential `reporting_to_id` for manager / direct-report relationships
- `EmployeeStatus` enum: active, inactive, on_leave, terminated
- `EmploymentType` enum: full_time, part_time, contractor
- Owner-scoped authorization throughout via `whilesmart/eloquent-owner-access`
- Auto-registered API routes: `apiResource employees` plus `POST employees/{employee}/link-user`
- `EmployeeLinkedToUser` event for host apps to provision membership on link
- Index filters: owner, status, employment type, department, has-login, free-text query
- Factory for testing
- Publishable config (`employees-config`) and migrations (`employees-migrations`)
