<?php

use Illuminate\Support\Facades\Route;
use Whilesmart\Employees\Http\Controllers\EmployeeController;

Route::post('employees/{employee}/link-user', [EmployeeController::class, 'linkUser']);
Route::apiResource('employees', EmployeeController::class);
