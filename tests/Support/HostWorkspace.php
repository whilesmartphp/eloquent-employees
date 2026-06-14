<?php

namespace Tests\Support;

use Illuminate\Database\Eloquent\Model;
use Whilesmart\Employees\Traits\HasEmployees;

class HostWorkspace extends Model
{
    use HasEmployees;

    protected $table = 'workspaces';

    protected $guarded = [];
}
