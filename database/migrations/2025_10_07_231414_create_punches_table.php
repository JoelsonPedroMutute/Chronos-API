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
        Schema::create('punches', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['in', 'out', 'break_start', 'break_end']);
            $table->timestampTz('punch_time');
            $table->boolean('auto_closed')->default(false);
            $table->decimal('extra_time', 8, 2)->nullable(); // ✅ Mudou de boolean para decimal
            $table->text('note')->nullable();
            $table->uuid('employee_id');
            $table->foreignId('company_id');
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('punches');
    }
};