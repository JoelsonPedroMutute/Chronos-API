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
            $table->enum('type', ['in', 'out']);
            $table->timestampTz('punch_time');
            $table->boolean('auto_closed')->default(false);
            $table->boolean('extra_time')->default(false);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->uuid('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
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
