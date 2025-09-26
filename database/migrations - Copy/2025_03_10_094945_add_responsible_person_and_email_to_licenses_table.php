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
        Schema::table('licenses', function (Blueprint $table) {
            $table->string('responsible_person')->after('license_no'); // ✅ Add Responsible Person
            $table->string('email')->after('responsible_person'); // ✅ Add Email
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('licenses', function (Blueprint $table) {
            $table->dropColumn(['responsible_person', 'email']); // ✅ Remove columns on rollback
        });
    }
};
