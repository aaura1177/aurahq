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
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->date('date'); 
            $table->string('shift', 10)->nullable(); 
            $table->time('check_in_time')->nullable(); 
            $table->time('check_out_time')->nullable();
            $table->time('working_hours')->nullable();
            $table->time('overtime_hours')->nullable();
            $table->enum('status', ['Present', 'Absent', 'Half-Day', 'Leave'])->default('Absent');
            $table->string('leave_type', 20)->nullable(); 
            $table->string('device_id', 50)->nullable();
            $table->string('geo_location', 255)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
