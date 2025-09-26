<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the foreign key constraint
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['license_access_id']);
        });


        // Modify the license_access_id column to TEXT or JSON to support comma-separated values
        Schema::table('users', function (Blueprint $table) {
            $table->text('license_access_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Revert the column type
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('license_access_id')->nullable()->change();
        });

        // Re-add the index
        Schema::table('users', function (Blueprint $table) {
            $table->index('license_access_id', 'users_license_access_id_foreign');
        });

        // Re-add the foreign key constraint
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('license_access_id')
                  ->references('id')
                  ->on('license_types')
                  ->onDelete('set null');
        });
    }
};