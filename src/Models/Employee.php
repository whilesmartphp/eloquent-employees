<?php

namespace Whilesmart\Employees\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Whilesmart\Employees\Database\Factories\EmployeeFactory;
use Whilesmart\Employees\Enums\EmployeeStatus;
use Whilesmart\Employees\Enums\EmploymentType;
use Whilesmart\Roles\Traits\HasRoles;

class Employee extends Model
{
    use HasFactory;
    use HasRoles;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $attributes = [
        'status' => 'active',
        'employment_type' => 'full_time',
    ];

    protected $casts = [
        'status' => EmployeeStatus::class,
        'employment_type' => EmploymentType::class,
        'start_date' => 'date',
        'end_date' => 'date',
        'metadata' => 'array',
    ];

    public function getTable(): string
    {
        return config('employees.table', 'employees');
    }

    public function getNameAttribute(): string
    {
        return trim(($this->first_name ?? '').' '.($this->last_name ?? ''));
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('employees.user_model', 'App\\Models\\User'), 'user_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reporting_to_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(self::class, 'reporting_to_id');
    }

    public function hasLogin(): bool
    {
        return $this->user_id !== null;
    }

    protected static function newFactory(): EmployeeFactory
    {
        return EmployeeFactory::new();
    }
}
