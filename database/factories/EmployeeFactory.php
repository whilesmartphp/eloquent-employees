<?php

namespace Whilesmart\Employees\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Whilesmart\Employees\Enums\EmployeeStatus;
use Whilesmart\Employees\Enums\EmploymentType;
use Whilesmart\Employees\Models\Employee;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'title' => $this->faker->jobTitle(),
            'department' => $this->faker->randomElement(['engineering', 'sales', 'finance', 'operations']),
            'status' => EmployeeStatus::Active->value,
            'employment_type' => EmploymentType::FullTime->value,
            'start_date' => $this->faker->dateTimeBetween('-3 years', 'now'),
        ];
    }

    public function contractor(): self
    {
        return $this->state(fn () => ['employment_type' => EmploymentType::Contractor->value]);
    }

    public function terminated(): self
    {
        return $this->state(fn () => [
            'status' => EmployeeStatus::Terminated->value,
            'end_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ]);
    }
}
