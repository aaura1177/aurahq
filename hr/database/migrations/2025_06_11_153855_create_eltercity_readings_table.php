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
        Schema::create('eltercity_readings', function (Blueprint $table) {
        $table->id();
        $table->enum('time_slot', ['morning', 'evening'])->nullable();
        $table->string('reading')->nullable();
        $table->date('date'); 
        $table->string('screenshot')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eltercity_readings');
    }
};
