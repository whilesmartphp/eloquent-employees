<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('employees.table', 'employees'), function (Blueprint $table) {
            $table->id();
            $table->morphs('owner');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('reporting_to_id')->nullable();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('title')->nullable();
            $table->string('department')->nullable();
            $table->string('status')->default('active');
            $table->string('employment_type')->default('full_time');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['owner_type', 'owner_id', 'email']);
            $table->index(['owner_type', 'owner_id', 'status']);
            $table->index('user_id');
            $table->index('reporting_to_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('employees.table', 'employees'));
    }
};
