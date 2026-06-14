<?php

namespace Whilesmart\Employees\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'owner_type' => $this->owner_type,
            'owner_id' => $this->owner_id,
            'user_id' => $this->user_id,
            'reporting_to_id' => $this->reporting_to_id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'title' => $this->title,
            'department' => $this->department,
            'status' => $this->status,
            'employment_type' => $this->employment_type,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
