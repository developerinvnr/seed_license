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
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_type_id')->constrained('license_types')->onDelete('cascade');
            $table->foreignId('license_name_id')->constrained('license_names')->onDelete('cascade');
            $table->string('office_address');
            $table->string('license_no');
            $table->string('code', 50);
            $table->string('remark')->nullable();
            $table->date('letter_date');
            $table->date('date_of_issue');
            $table->date('valid_upto');
            $table->string('registered_address');
            $table->enum('reminder_option', ['Y', 'N']);
            $table->enum('frequency', ['weekly', 'daily', 'monthly', 'yearly'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
