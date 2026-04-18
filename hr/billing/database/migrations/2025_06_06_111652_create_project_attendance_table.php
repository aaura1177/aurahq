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
        Schema::create('project_attendance', function (Blueprint $table) {
            $table->id();
                   $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('project_id');

            // Hourly and Minute rate
            $table->decimal('h_rate', 10, 2);
            $table->decimal('m_rate', 10, 4);

            // Time tracking
            $table->time('start_time');
            $table->time('end_time');

            // Total minutes worked
            $table->integer('total_minutes');

            // Total amount for the session
            $table->decimal('total_amount', 10, 2);

            // Date of attendance
            $table->date('date');

            $table->timestamps();

            // Optional: Add foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
       
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_attendance');
    }
};
