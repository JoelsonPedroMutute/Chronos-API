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
        Schema::table('employee_categories', function (Blueprint $table) {
            $table->string('default_position')->nullable();
            $table->string('default_department')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_categories', function (Blueprint $table) {
            $table->dropColumn('default_position');
            $table->dropColumn('default_department');
        });
    }
};
