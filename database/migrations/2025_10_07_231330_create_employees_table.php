<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('address')->nullable();
            $table->date('hire_date')->nullable();
            $table->string('email')->unique();
            $table->string('phone_number', 15)->nullable();
            $table->timestamps();

            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->uuid('employee_category_id')->nullable();
            $table->foreign('employee_category_id')->references('id')->on('employee_categories')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
