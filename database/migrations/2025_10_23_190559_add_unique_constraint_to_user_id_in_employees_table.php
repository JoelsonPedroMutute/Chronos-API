<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Apenas adiciona a constraint de unicidade
            if (!Schema::hasColumn('employees', 'user_id')) {
                $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            }

            // Adiciona a restrição única (evita duplicidade)
            $table->unique('user_id', 'employees_user_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropUnique(['user_id']);
        });
    }
};
