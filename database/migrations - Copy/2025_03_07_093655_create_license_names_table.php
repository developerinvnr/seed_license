<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('license_names', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('license_type_id');
            $table->string('license_name')->unique();
            $table->timestamps();
    
            $table->foreign('license_type_id')->references('id')->on('license_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('license_names');
    }
};
