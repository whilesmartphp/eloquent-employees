<?php

namespace Whilesmart\Employees\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Whilesmart\Employees\Models\Employee;

class EmployeeLinkedToUser
{
    use Dispatchable;

    public function __construct(
        public Employee $employee,
        public mixed $userId,
    ) {}
}
