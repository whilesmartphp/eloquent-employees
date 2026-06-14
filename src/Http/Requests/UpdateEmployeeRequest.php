<?php

namespace Whilesmart\Employees\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Whilesmart\OwnerAccess\Concerns\AuthorizesOwnerRequest;

class UpdateEmployeeRequest extends FormRequest
{
    use AuthorizesOwnerRequest;

    public function authorize(): bool
    {
        return $this->authorizeOwnerOfBoundModel('employee');
    }

    public function rules(): array
    {
        return [
            'reporting_to_id' => ['nullable', 'integer'],
            'name' => ['sometimes', 'string', 'max:200'],
            'email' => ['nullable', 'email', 'max:200'],
            'phone' => ['nullable', 'string', 'max:50'],
            'title' => ['nullable', 'string', 'max:120'],
            'department' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:active,inactive,on_leave,terminated'],
            'employment_type' => ['nullable', 'in:full_time,part_time,contractor'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
