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
        Schema::create('internship_attendances', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');       
            $table->unsignedBigInteger('employee_id');   

            $table->date('date'); 

            $table->time('check_in_time')->nullable(); 
            $table->time('check_out_time')->nullable();
            $table->time('working_hours')->nullable();

            $table->enum('status', ['Present', 'Absent', 'Half-Day', 'Leave'])->default('Absent');

            $table->text('remarks')->nullable();

            $table->timestamps(); // <== semicolon added here
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internship_attendances');
    }
};
