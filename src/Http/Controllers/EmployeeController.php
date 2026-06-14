<?php

namespace Whilesmart\Employees\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Whilesmart\Employees\Events\EmployeeLinkedToUser;
use Whilesmart\Employees\Http\Requests\StoreEmployeeRequest;
use Whilesmart\Employees\Http\Requests\UpdateEmployeeRequest;
use Whilesmart\Employees\Http\Resources\EmployeeResource;
use Whilesmart\Employees\Models\Employee;
use Whilesmart\OwnerAccess\Concerns\AuthorizesOwnerController;

class EmployeeController extends Controller
{
    use AuthorizesOwnerController;

    protected function modelClass(): string
    {
        return config('employees.model', Employee::class);
    }

    public function index(Request $request): JsonResponse
    {
        $query = $this->scopeAccessibleOwners($this->modelClass()::query(), $request->user());

        if ($request->filled('owner_type') && $request->filled('owner_id')) {
            $query->where('owner_type', $request->input('owner_type'))
                ->where('owner_id', $request->input('owner_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->input('employment_type'));
        }

        if ($request->filled('department')) {
            $query->where('department', $request->input('department'));
        }

        if ($request->boolean('has_login', false)) {
            $query->whereNotNull('user_id');
        }

        if ($request->filled('q')) {
            $term = strtolower($request->input('q'));
            $query->where(function ($q) use ($term) {
                $q->whereRaw('lower(name) like ?', ["%{$term}%"])
                    ->orWhereRaw('lower(email) like ?', ["%{$term}%"])
                    ->orWhereRaw('lower(title) like ?', ["%{$term}%"]);
            });
        }

        $employees = $query->orderBy('name')
            ->paginate((int) $request->input('per_page', 25));

        return response()->json([
            'success' => true,
            'data' => EmployeeResource::collection($employees)->response()->getData(true),
        ]);
    }

    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $employee = $this->modelClass()::create($request->validated());

        return response()->json([
            'success' => true,
            'data' => new EmployeeResource($employee),
        ], 201);
    }

    public function show(Employee $employee, Request $request): JsonResponse
    {
        $this->authorizeAccessTo($employee, $request->user());

        return response()->json([
            'success' => true,
            'data' => new EmployeeResource($employee),
        ]);
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): JsonResponse
    {
        $employee->fill($request->validated())->save();

        return response()->json([
            'success' => true,
            'data' => new EmployeeResource($employee),
        ]);
    }

    public function destroy(Employee $employee, Request $request): JsonResponse
    {
        $this->authorizeAccessTo($employee, $request->user());
        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Employee deleted.',
        ]);
    }

    public function linkUser(Request $request, Employee $employee): JsonResponse
    {
        $this->authorizeAccessTo($employee, $request->user());

        $validated = $request->validate([
            'user_id' => ['required', 'integer'],
        ]);

        $employee->user_id = $validated['user_id'];
        $employee->save();

        EmployeeLinkedToUser::dispatch($employee, $employee->user_id);

        return response()->json([
            'success' => true,
            'data' => new EmployeeResource($employee),
        ]);
    }
}
