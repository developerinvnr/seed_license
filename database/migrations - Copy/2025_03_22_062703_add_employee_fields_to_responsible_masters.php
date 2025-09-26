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
        Schema::table('responsible_masters', function (Blueprint $table) {
            $table->string('emp_email')->nullable();
            $table->string('emp_contact')->nullable();
            $table->string('emp_department')->nullable();
            $table->string('emp_designation')->nullable();
            $table->string('emp_state')->nullable();
            $table->string('emp_city')->nullable();
                });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('responsible_masters', function (Blueprint $table) {
            //
        });
    }
};
