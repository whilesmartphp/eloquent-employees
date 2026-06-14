<?php

namespace Whilesmart\Employees\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Whilesmart\Employees\Models\Employee;

trait HasEmployees
{
    public function employees(): MorphMany
    {
        return $this->morphMany(Employee::class, 'owner');
    }
}
